<?php
session_start();
include(__DIR__ . '/.././db/dbHotel.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../consultas/consulta_reservas.php");
    exit;
}

$reserva_id = $_GET['id'];

// Busca os dados da reserva no banco
$stmt = $pdo->prepare("SELECT r.*, q.numero AS numero_quarto, u.nome_completo AS nome_usuario 
                       FROM reservas r
                       JOIN quartos q ON r.quarto_id = q.id
                       JOIN usuarios u ON r.usuario_id = u.id
                       WHERE r.id = ?");
$stmt->execute([$reserva_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca os hóspedes secundários
$stmt_hospedes = $pdo->prepare("SELECT hs.*, q.numero as numero_quarto 
                               FROM hospedes_secundarios hs
                               JOIN quartos q ON hs.quarto_id = q.id
                               WHERE hs.reserva_id = ?");
$stmt_hospedes->execute([$reserva_id]);
$hospedes_secundarios = $stmt_hospedes->fetchAll(PDO::FETCH_ASSOC);

$checkin = $reserva['data_checkin'];
$checkout = $reserva['data_checkout'];

// Consulta para quartos não ocupados no período
$stmt_quartos_disponiveis = $pdo->prepare("
    SELECT q.* 
    FROM quartos q
    WHERE q.id NOT IN (
        SELECT r.quarto_id
        FROM reservas r
        WHERE (
            (r.data_checkin < :checkout AND r.data_checkout > :checkin)
            AND r.status NOT IN ('cancelada', 'finalizada')
            AND r.id != :reserva_id
        )
    )
    AND q.status = 'Disponível'
    ORDER BY q.numero
");

$stmt_quartos_disponiveis->execute([
    ':checkin' => $checkin,
    ':checkout' => $checkout,
    ':reserva_id' => $reserva_id
]);

$quartos_disponiveis = $stmt_quartos_disponiveis->fetchAll(PDO::FETCH_ASSOC);

if (!$reserva) {
    header("Location: ../consultas/consulta_reservas.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reserva_id = $_POST['reserva_id'] ?? null;
    $quarto_numero = $_POST['numero_quarto'] ?? null;
    $tipo_cama = $_POST['tipo_cama'] ?? null;
    $status = $_POST['status'] ?? null;
    $observacoes = $_POST['observacoes'] ?? null;
    $valor_total = isset($_POST['valorTotal']) ? floatval(str_replace(',', '.', $_POST['valorTotal'])) : null;
    $valor_pago = isset($_POST['valorPago']) ? floatval(str_replace(',', '.', $_POST['valorPago'])) : null;

    // Debug: Log dos dados recebidos
    error_log("Dados recebidos no POST: " . print_r($_POST, true));

    if (!$reserva_id || !$quarto_numero || !$tipo_cama || !$status || $valor_total === null || $valor_pago === null) {
        echo "<script>alert('Todos os campos obrigatórios devem ser preenchidos.'); window.history.back();</script>";
        exit;
    }

    $valor_restante = $valor_total - $valor_pago;

    // Verifica se o quarto existe
    $stmt = $pdo->prepare("SELECT id FROM quartos WHERE numero = ?");
    $stmt->bindValue(1, $quarto_numero, PDO::PARAM_STR);
    $stmt->execute();
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        echo "<script>alert('Quarto não encontrado. Por favor, verifique o número do quarto.'); window.history.back();</script>";
        exit;
    }

    $quarto_id = $quarto['id'];

    $pdo->beginTransaction();

    try {
        // Atualiza a reserva
        $sql = "UPDATE reservas SET
                    quarto_id = ?, 
                    tipo_camas = ?, 
                    valor_reserva = ?, 
                    status = ?, 
                    observacoes = ?, 
                    valor_pago = ?, 
                    valor_restante = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $quarto_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $tipo_cama, PDO::PARAM_STR);
        $stmt->bindValue(3, $valor_total, PDO::PARAM_STR);
        $stmt->bindValue(4, $status, PDO::PARAM_STR);
        $stmt->bindValue(5, $observacoes, PDO::PARAM_STR);
        $stmt->bindValue(6, $valor_pago, PDO::PARAM_STR);
        $stmt->bindValue(7, $valor_restante, PDO::PARAM_STR);
        $stmt->bindValue(8, $reserva_id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar a reserva: " . implode(", ", $stmt->errorInfo()));
        }

        // Remove hóspedes secundários existentes
        $stmt = $pdo->prepare("DELETE FROM hospedes_secundarios WHERE reserva_id = ?");
        $stmt->bindValue(1, $reserva_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao remover hóspedes secundários: " . implode(", ", $stmt->errorInfo()));
        }

        // Insere novos hóspedes secundários (se houver)
        if (isset($_POST['hospedes_secundarios']) && is_array($_POST['hospedes_secundarios'])) {
            $stmt = $pdo->prepare("INSERT INTO hospedes_secundarios (reserva_id, nome, documento, quarto_id) VALUES (?, ?, ?, ?)");
            
            foreach ($_POST['hospedes_secundarios'] as $hospede) {
                if (empty($hospede['nome']) || empty($hospede['documento']) || empty($hospede['quarto_id'])) {
                    continue;
                }

                $stmt->bindValue(1, $reserva_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $hospede['nome'], PDO::PARAM_STR);
                $stmt->bindValue(3, $hospede['documento'], PDO::PARAM_STR);
                $stmt->bindValue(4, $hospede['quarto_id'], PDO::PARAM_INT);
                
                if (!$stmt->execute()) {
                    throw new Exception("Erro ao inserir hóspede secundário: " . implode(", ", $stmt->errorInfo()));
                }
            }
        }

        // Finaliza a transação
        $pdo->commit();
        echo "<script>alert('Reserva atualizada com sucesso!'); window.location.href='../reservas/listar_reservas.php';</script>";
    } catch (Exception $e) {
        // Caso haja erro, desfaz a transação
        $pdo->rollback();
        error_log($e->getMessage());
        echo "<script>alert('Ocorreu um erro ao atualizar a reserva: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/editar_reserva.css">
</head>

<body class="bg-light">

    <?php include("../components/navbar.php"); ?>

    <div class="container mt-5 bg-light"></br></br>
        <h1 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Editar Reserva</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $reserva_id; ?>" method="POST">
            <input type="hidden" name="reserva_id" value="<?= $reserva_id ?>">

            <!-- Seção de Dados do Cliente -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="fas fa-user-check me-2"></i>Dados do Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="suggestions-container">
                            <input type="hidden" id="cliente_id" name="cliente_id" value="<?= $reserva['usuario_id'] ?? '' ?>">
                            <div class="input-group cliente-input-group">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <div id="clienteSelecionadoContainer" class="cliente-selecionado-container <?= empty($reserva['nome_usuario']) ? 'd-none' : '' ?>">
                                    <span id="nomeClienteSelecionado" class="cliente-selecionado-nome"><?= htmlspecialchars($reserva['nome_usuario'] ?? '') ?></span>
                                    <input type="hidden" id="documento_real" name="documento" value="<?= htmlspecialchars($reserva['cpf_cnpj'] ?? '') ?>">
                                </div>
                                <input type="text" class="form-control cliente-input-visual <?= !empty($reserva['nome_usuario']) ? 'd-none' : '' ?>"
                                    id="documento_visual"
                                    placeholder="Digite o CPF/CNPJ do cliente"
                                    oninput="buscarSugestoes(this.value)"
                                    value="<?= htmlspecialchars($reserva['cpf_cnpj'] ?? '') ?>">
                                <button class="btn btn-outline-secondary cliente-btn-limpar" type="button" onclick="limparCliente()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="suggestionsDropdown" class="suggestions-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#cadastroCliente">
                            <i class="fas fa-user-plus me-2"></i>Novo Cliente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Seção de Datas -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="fas fa-calendar-alt me-2"></i>Período da Reserva</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="check_in" class="form-label">Check-in:</label>
                        <input type="date" class="form-control bg-light" name="check_in" id="check_in"
                            value="<?= htmlspecialchars($reserva['data_checkin'] ?? '') ?>"
                            min="<?= date('Y-m-d', strtotime('-31 days')) ?>"
                            max="<?= date('Y-m-d', strtotime('+729 days')) ?>" required disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="check_out" class="form-label">Check-out:</label>
                        <input type="date" class="form-control bg-light" name="check_out" id="check_out"
                            value="<?= htmlspecialchars($reserva['data_checkout'] ?? '') ?>"
                            min="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                            max="<?= date('Y-m-d', strtotime('+730 days')) ?>" required disabled>
                    </div>
                </div>
            </div>

            <!-- Seção de Detalhes do Quarto -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-house-door me-2"></i>Detalhes do Quarto</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="numero_quarto" class="form-label">Número do Quarto:</label>
                        <select class="form-select" name="numero_quarto" id="numero_quarto" required>
                            <?php foreach ($quartos_disponiveis as $quarto): ?>
                                <option value="<?= $quarto['id'] ?>" 
                                    <?= ($quarto['id'] == $reserva['quarto_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($quarto['numero']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_cama" class="form-label">Tipo de Cama:</label>
                        <select class="form-select" name="tipo_cama" id="tipo_cama" required>
                            <option value="solteiro" <?= ($reserva['tipo_camas'] ?? '') == 'solteiro' ? 'selected' : '' ?>>Cama de Solteiro</option>
                            <option value="beliche" <?= ($reserva['tipo_camas'] ?? '') == 'beliche' ? 'selected' : '' ?>>Cama Beliche</option>
                            <option value="casal" <?= ($reserva['tipo_camas'] ?? '') == 'casal' ? 'selected' : '' ?>>Cama de Casal</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pension" class="form-label">Tipo de Pensão:</label>
                        <select class="form-control bg-light" id="pension" name="pension" required disabled>
                            <option value="cafe" <?= ($reserva['tipo_pensao'] ?? '') == 'cafe' ? 'selected' : '' ?>>Café da Manhã</option>
                            <option value="completa" <?= ($reserva['tipo_pensao'] ?? '') == 'completa' ? 'selected' : '' ?>>Pensão Completa</option>
                            <option value="meia" <?= ($reserva['tipo_pensao'] ?? '') == 'meia' ? 'selected' : '' ?>>Meia Pensão</option>
                            <option value="nenhuma" <?= ($reserva['tipo_pensao'] ?? '') == 'nenhuma' ? 'selected' : '' ?>>Sem Pensão</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Seção de Valores -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-cash-coin me-2"></i>Valores</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="valorTotal" name="valorTotal"
                                value="<?= number_format($reserva['valor_reserva'] ?? 0, 2, ',', '.') ?>" readonly>
                        </div>
                        <small class="form-text text-muted">Valor Total</small>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="valorPago" name="valorPago"
                                value="<?= number_format($reserva['valor_pago'] ?? 0, 2, ',', '.') ?>">
                        </div>
                        <small class="form-text text-muted">Valor Pago</small>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="valorRestante" name="valorRestante"
                                value="<?= number_format(($reserva['valor_reserva'] ?? 0) - ($reserva['valor_pago'] ?? 0), 2, ',', '.') ?>" readonly>
                        </div>
                        <small class="form-text text-muted">Valor Restante</small>
                    </div>
                </div>
            </div>

            <!-- Seção de Hóspedes Secundários -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-people-fill me-2"></i>Hóspedes Secundários</h5>
                <button type="button" class="btn btn-outline-success w-100 mb-3" onclick="adicionarHospede()">
                    <i class="bi bi-plus-circle me-2"></i>Adicionar Hóspede
                </button>
                <div class="list-group mb-3" id="hospedesList">
                    <?php if (!empty($hospedes_secundarios)): ?>
                        <?php foreach ($hospedes_secundarios as $index => $hospede): ?>
                            <div class="list-group-item hospede-item">
                                <input type="hidden" name="hospedes_secundarios[<?= $index ?>][id]" value="<?= $hospede['id'] ?>">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="form-control"
                                            name="hospedes_secundarios[<?= $index ?>][nome]"
                                            value="<?= htmlspecialchars($hospede['nome'] ?? '') ?>"
                                            placeholder="Nome completo" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="text" class="form-control"
                                            name="hospedes_secundarios[<?= $index ?>][documento]"
                                            value="<?= htmlspecialchars($hospede['documento'] ?? '') ?>"
                                            placeholder="CPF/CNPJ" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="form-select select-quarto" 
                                                name="hospedes_secundarios[<?= $index ?>][quarto_id]" required>
                                            <option value="">Selecione o quarto</option>
                                            <?php foreach ($quartos_disponiveis as $quarto): ?>
                                                <option value="<?= $quarto['id'] ?>"
                                                    <?= ($hospede['quarto_id'] == $quarto['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($quarto['numero']) ?> - R$ <?= number_format($quarto['preco'], 2, ',', '.') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2 text-center">
                                        <button type="button" class="btn btn-sm btn-danger w-100"
                                            onclick="removerHospede(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Seção de Status da Reserva -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-clipboard-check me-2"></i>Status da Reserva</h5>
                <div class="btn-group w-100" role="group" id="statusGroup">
                    <?php
                    $statusOptions = [
                        'confirmada' => 'btn-outline-success',
                        'pendente' => 'btn-outline-warning',
                        'em andamento' => 'btn-outline-primary',
                        'finalizada' => 'btn-outline-info',
                        'cancelada' => 'btn-outline-danger'
                    ];

                    $currentStatus = strtolower($reserva['status'] ?? 'confirmada');

                    foreach ($statusOptions as $status => $btnClass) {
                        $active = ($currentStatus === $status) ? 'active' : '';
                        echo '<button type="button" class="btn ' . $btnClass . ' ' . $active . '" data-status="' . $status . '">' . ucfirst($status) . '</button>';
                    }
                    ?>
                </div>
                <input type="hidden" name="status" id="status" value="<?= htmlspecialchars($currentStatus) ?>">
            </div>

            <!-- Seção de Observações -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="fas fa-comment-alt me-2"></i>Observações</h5>
                <textarea class="form-control" name="observacoes" rows="3"
                    placeholder="Informações adicionais sobre a reserva..."><?= htmlspecialchars($reserva['observacoes'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between mb-5">
                <a href="../consultas/consulta_reservas.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>  

    <?php include("../components/footer.php"); ?>

    <!-- Bootstrap JS e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Scripts personalizados -->
    <script>
        // Funções para manipulação de valores monetários
        function brToNumber(value) {
            if (!value) return 0;
            return parseFloat(value.replace(/\./g, '').replace(',', '.'));
        }

        function formatBr(value) {
            return value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function calcularRestante() {
            const valorTotal = brToNumber(document.getElementById('valorTotal').value);
            const valorPago = brToNumber(document.getElementById('valorPago').value);
            const valorRestante = Math.max(0, valorTotal - valorPago);

            document.getElementById('valorRestante').value = formatBr(valorRestante);
        }

        // Configuração do campo monetário
        function setupCurrencyField(element) {
            element.addEventListener('input', function(e) {
                let value = this.value.replace(/[^\d]/g, '');
                value = (parseInt(value) / 100).toFixed(2);
                this.value = formatBr(parseFloat(value));
                calcularRestante();
            });

            element.addEventListener('blur', function() {
                let value = brToNumber(this.value);
                this.value = formatBr(value);
            });

            element.addEventListener('keydown', function(e) {
                if (!/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight|ArrowUp|ArrowDown/.test(e.key)) {
                    e.preventDefault();
                }
            });
        }

        // Inicialização dos botões de status
        function initStatusButtons() {
            const statusField = document.getElementById('status');
            const statusGroup = document.getElementById('statusGroup');

            console.log('Status field:', statusField);
            console.log('Status group:', statusGroup);

            if (!statusField || !statusGroup) return;

            function updateActiveButton() {
                const currentStatus = statusField.value;
                console.log('Current status:', currentStatus);
                const buttons = statusGroup.querySelectorAll('button');

                buttons.forEach(button => {
                    button.classList.remove('active');
                    if (button.dataset.status === currentStatus) {
                        button.classList.add('active');
                    }
                });
            }

            statusGroup.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function() {
                    statusField.value = this.dataset.status;
                    console.log('Status selecionado:', statusField.value);
                    updateActiveButton();
                });
            });

            updateActiveButton();
        }

        // Funções para busca de clientes
        function buscarSugestoes(valor) {
            if (valor.length < 3) {
                document.getElementById('suggestionsDropdown').style.display = 'none';
                return;
            }

            fetch(`../buscar_clientes.php?termo=${encodeURIComponent(valor)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Erro:', data.error);
                        return;
                    }
                    exibirSugestoes(data);
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    document.getElementById('suggestionsDropdown').style.display = 'none';
                });
        }

        function exibirSugestoes(clientes) {
            const dropdown = document.getElementById('suggestionsDropdown');
            dropdown.innerHTML = '';

            if (!Array.isArray(clientes) || clientes.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            clientes.forEach(cliente => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                const nome = cliente.nome_completo || '[Nome não disponível]';
                const doc = cliente.cpf_cnpj ? ` (${cliente.cpf_cnpj})` : '';

                item.innerHTML = `<div><strong>${nome}</strong></div><div class="text-muted small">${doc}</div>`;
                item.addEventListener('click', () => selecionarCliente(cliente));
                dropdown.appendChild(item);
            });

            dropdown.style.display = 'block';
        }

        function selecionarCliente(cliente) {
            const inputVisual = document.getElementById('documento_visual');
            const containerSelecionado = document.getElementById('clienteSelecionadoContainer');
            const btnLimpar = document.querySelector('.cliente-btn-limpar');

            inputVisual.classList.add('d-none');
            containerSelecionado.classList.remove('d-none');

            document.getElementById('nomeClienteSelecionado').textContent = cliente.nome_completo || 'Cliente Selecionado';
            document.getElementById('cliente_id').value = cliente.id;
            document.getElementById('documento_real').value = cliente.cpf_cnpj || '';

            btnLimpar.classList.remove('btn-outline-secondary');
            btnLimpar.classList.add('btn-danger');
            btnLimpar.innerHTML = '<i class="fas fa-times me-1"></i> Limpar';

            document.getElementById('suggestionsDropdown').style.display = 'none';
            containerSelecionado.style.width = inputVisual.offsetWidth + 'px';
        }

        function limparCliente() {
            document.getElementById('documento_visual').classList.remove('d-none');
            document.getElementById('documento_visual').value = '';
            document.getElementById('clienteSelecionadoContainer').classList.add('d-none');
            document.getElementById('cliente_id').value = '';
            document.getElementById('documento_real').value = '';

            const btnLimpar = document.querySelector('.input-group button');
            btnLimpar.classList.remove('btn-danger');
            btnLimpar.classList.add('btn-outline-secondary');
            btnLimpar.innerHTML = '<i class="fas fa-times"></i>';

            document.getElementById('suggestionsDropdown').style.display = 'none';
        }

        // Funções para hóspedes secundários
        function adicionarHospede() {
            const container = document.getElementById('hospedesList');
            const newIndex = container.querySelectorAll('.hospede-item').length;

            const newItem = document.createElement('div');
            newItem.className = 'list-group-item hospede-item';
            newItem.innerHTML = `
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control" 
                               name="hospedes_secundarios[${newIndex}][nome]" 
                               placeholder="Nome completo" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="text" class="form-control"
                            name="hospedes_secundarios[${newIndex}][documento]"
                            placeholder="CPF/CNPJ" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <select class="form-select select-quarto" 
                                name="hospedes_secundarios[${newIndex}][quarto_id]" 
                                required>
                            <option value="">Selecione o quarto</option>
                            <?php foreach ($quartos_disponiveis as $quarto): ?>
                                <option value="<?= $quarto['id'] ?>">
                                    <?= htmlspecialchars($quarto['numero']) ?> - R$ <?= number_format($quarto['preco'], 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 text-center">
                        <button type="button" class="btn btn-sm btn-danger w-100" 
                                onclick="removerHospede(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>`;

            container.appendChild(newItem);
            newItem.querySelector('.select-quarto').addEventListener('change', atualizarValorTotal);
        }

        function removerHospede(botao) {
            if (confirm('Tem certeza que deseja remover este hóspede?')) {
                botao.closest('.hospede-item').remove();
                atualizarValorTotal();
            }
        }

        // Atualização do valor total
        function atualizarValorTotal() {
            // 1. Calcular número de diárias
            const checkin = new Date(document.getElementById('check_in').value);
            const checkout = new Date(document.getElementById('check_out').value);
            const diffTime = checkout - checkin;
            const diarias = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));

            // 2. Obter valor do quarto principal
            const quartoPrincipalSelect = document.getElementById('numero_quarto');
            const quartoPrincipalId = quartoPrincipalSelect.value;
            let valorQuartoPrincipal = 0;

            // Buscar o valor do quarto principal
            <?php foreach ($quartos_disponiveis as $quarto): ?>
                if (quartoPrincipalId == '<?= $quarto['id'] ?>') {
                    valorQuartoPrincipal = <?= $quarto['preco'] ?>;
                }
            <?php endforeach; ?>

            // 3. Calcular valor dos hóspedes secundários
            let valorHospedesSecundarios = 0;
            document.querySelectorAll('.select-quarto').forEach(select => {
                if (select.value) {
                    const quartoId = select.value;
                    <?php foreach ($quartos_disponiveis as $quarto): ?>
                        if (quartoId == '<?= $quarto['id'] ?>') {
                            valorHospedesSecundarios += <?= $quarto['preco'] ?>;
                        }
                    <?php endforeach; ?>
                }
            });

            // 4. Calcular valor total (quartos × diárias)
            const valorTotal = (valorQuartoPrincipal + valorHospedesSecundarios) * diarias;

            // 5. Atualizar o campo
            document.getElementById('valorTotal').value = formatBr(valorTotal);

            // 6. Atualizar valor restante
            calcularRestante();
        }

        // Inicialização quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            // Formata valores iniciais
            document.getElementById('valorTotal').value = formatBr(brToNumber('<?= number_format($reserva['valor_reserva'], 2, ',', '.') ?>'));
            document.getElementById('valorPago').value = '<?= isset($reserva['valor_pago']) ? number_format($reserva['valor_pago'], 2, ',', '.') : '0,00' ?>';
            document.getElementById('valorRestante').value = formatBr(brToNumber('<?= number_format(($reserva['valor_reserva'] ?? 0) - ($reserva['valor_pago'] ?? 0), 2, ',', '.') ?>'));

            // Configura campo de valor pago
            setupCurrencyField(document.getElementById('valorPago'));

            // Inicializa botões de status
            initStatusButtons();

            // Adiciona eventos aos selects existentes
            document.querySelectorAll('.select-quarto').forEach(select => {
                select.addEventListener('change', atualizarValorTotal);
            });

            // Adiciona evento ao quarto principal
            document.getElementById('numero_quarto').addEventListener('change', atualizarValorTotal);

            // Adiciona evento ao campo de valor pago
            document.getElementById('valorPago').addEventListener('input', calcularRestante);

            // Calcula valores iniciais
            atualizarValorTotal();
        });

        // Ajuste de layout
        window.addEventListener('resize', function() {
            const inputVisual = document.getElementById('documento_visual');
            const containerSelecionado = document.getElementById('clienteSelecionadoContainer');

            if (inputVisual && containerSelecionado && !containerSelecionado.classList.contains('d-none')) {
                containerSelecionado.style.width = inputVisual.offsetWidth + 'px';
            }
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.suggestions-container')) {
                document.getElementById('suggestionsDropdown').style.display = 'none';
            }
        });
    </script>
</body>

</html>