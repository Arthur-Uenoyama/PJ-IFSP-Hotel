<?php
session_start();
include '.././db/dbHotel.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioId = $_SESSION['id'];
$quartoSelecionado = $_GET['quarto'] ?? null;

// Função auxiliar para cores de status
function getStatusColor($status) {
    $colors = [
        'pendente' => 'warning',
        'concluido' => 'success',
        'cancelado' => 'danger'
    ];
    return $colors[strtolower($status)] ?? 'secondary';
}

// Buscar pedidos de serviço de quarto do cliente
$pedidos = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            psq.id, 
            psq.descricao, 
            psq.status, 
            psq.prioridade, 
            psq.tipoServico, 
            psq.horarioPreferencial, 
            psq.criado_em AS DataSolicitacao, 
            q.numero AS NumeroQuarto, 
            psq.valor AS TaxaCusto
        FROM pedidos_servico_quarto psq
        INNER JOIN reservas r ON psq.reserva_id = r.id
        INNER JOIN quartos q ON r.quarto_id = q.id
        WHERE r.usuario_id = ?
        ORDER BY psq.criado_em DESC
    ");
    $stmt->execute([$usuarioId]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar pedidos: " . $e->getMessage();
}

// Buscar quartos reservados pelo usuário
$quartosReservados = [];
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT q.numero 
        FROM quartos q
        INNER JOIN reservas r ON q.id = r.quarto_id
        WHERE r.usuario_id = ? 
          AND CURDATE() BETWEEN r.data_checkin AND r.data_checkout
        ORDER BY q.numero
    ");
    $stmt->execute([$usuarioId]);
    $quartosReservados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar quartos reservados: " . $e->getMessage();
}

// Buscar todos os quartos disponíveis
$quartosDisponiveis = [];
try {
    $stmt = $pdo->prepare("
        SELECT q.numero 
        FROM quartos q
        WHERE q.id NOT IN (
            SELECT r.quarto_id 
            FROM reservas r 
            WHERE CURDATE() BETWEEN r.data_checkin AND r.data_checkout
        )
        ORDER BY q.numero
    ");
    $stmt->execute();
    $quartosDisponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar quartos disponíveis: " . $e->getMessage();
}

// Adicionar um novo pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'] ?? null;
    $numeroQuarto = $_POST['numero_quarto'] ?? null;
    $tipoServico = $_POST['service_type'] ?? null;
    $prioridade = $_POST['priority'] ?? 'Normal';
    $horarioPreferencial = $_POST['preferred_time'] ?? null;
    $taxaCusto = isset($_POST['valor_taxa']) ? floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor_taxa'])) : null;

    if (!$descricao || !$numeroQuarto || !$tipoServico) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.history.back();</script>";
        exit;
    }

    try {
        // Verificar se o quarto está reservado pelo usuário
        $stmt = $pdo->prepare("
            SELECT r.id AS reserva_id
            FROM reservas r
            INNER JOIN quartos q ON r.quarto_id = q.id
            WHERE q.numero = ? 
              AND r.usuario_id = ?
              AND CURDATE() BETWEEN r.data_checkin AND r.data_checkout
            LIMIT 1
        ");
        $stmt->execute([$numeroQuarto, $usuarioId]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            // Verificar se o quarto existe e está disponível
            $stmt = $pdo->prepare("
                SELECT id FROM quartos 
                WHERE numero = ? 
                AND id NOT IN (
                    SELECT quarto_id FROM reservas 
                    WHERE CURDATE() BETWEEN data_checkin AND data_checkout
                )
                LIMIT 1
            ");
            $stmt->execute([$numeroQuarto]);
            $quartoDisponivel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$quartoDisponivel) {
                echo "<script>alert('Quarto não disponível ou não encontrado.'); window.history.back();</script>";
                exit;
            }
            
            // Para quartos não reservados, usar reserva_id NULL ou criar lógica especial
            $reservaId = null;
            echo "<script>alert('Serviço em quarto não reservado requer aprovação.'); window.location.href = './servico_quarto.php';</script>";
            exit;
        } else {
            $reservaId = $reserva['reserva_id'];
        }

        // Inserir o pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos_servico_quarto (reserva_id, descricao, status, prioridade, tipoServico, horarioPreferencial, valor, criado_em) 
            VALUES (?, ?, 'pendente', ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$reservaId, $descricao, $prioridade, $tipoServico, $horarioPreferencial, $taxaCusto]);

        echo "<script>alert('Pedido de serviço adicionado com sucesso!'); window.location.href = './servico_quarto.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao adicionar pedido: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço de Quarto - Apê Pousada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/servico_quarto.css">
</head>

<body class="bg-light">

    <?php include("../components/navbar.php"); ?>

    <div class="container container-main mt-5 bg-light">
        <h1 class="mb-4"><i class="fas fa-concierge-bell me-2"></i>Serviço de Quarto</h1>

        <!-- Card de Novo Pedido -->
        <div class="card-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-green m-0"><i class="bi bi-plus-circle me-2"></i>Novo Pedido</h5>
                <button class="btn btn-sm btn-outline-success" onclick="toggleFormAdvanced()">
                    <i class="bi bi-gear me-1"></i>Opções Avançadas
                </button>
            </div>

            <form method="POST" id="serviceForm">
                <div class="row g-3">
                    <!-- Campo de Tipo de Serviço -->
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Serviço</label>
                        <select class="form-select" name="service_type" id="serviceType" required>
                            <option value="">Selecione...</option>
                            <option value="limpeza">Limpeza</option>
                            <option value="alimentacao">Alimentação</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <!-- Campo de Prioridade -->
                    <div class="col-md-6">
                        <label class="form-label">Prioridade</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-success active" data-priority="normal">
                                Normal
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-priority="urgente">
                                Urgente
                            </button>
                        </div>
                        <input type="hidden" name="priority" id="priority" value="normal">
                    </div>

                    <!-- Campos Principais -->
                    <div class="col-12">
                        <label class="form-label">Descrição Detalhada</label>
                        <textarea name="descricao" class="form-control" rows="3"
                            placeholder="Descreva o serviço necessário..." required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Número do Quarto</label>
                        <select class="form-select" name="numero_quarto" id="numeroQuarto" required>
                            <option value="">Selecione o quarto...</option>
                            
                            <!-- Quartos reservados pelo usuário -->
                            <?php if (!empty($quartosReservados)): ?>
                                <optgroup label="Meus Quartos Reservados">
                                <?php foreach ($quartosReservados as $quarto): ?>
                                    <option value="<?= htmlspecialchars($quarto['numero']) ?>" 
                                        <?= ($quartoSelecionado == $quarto['numero']) ? 'selected' : '' ?>>
                                        Quarto <?= htmlspecialchars($quarto['numero']) ?>
                                    </option>
                                <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            
                            <!-- Quartos disponíveis -->
                            <?php if (!empty($quartosDisponiveis)): ?>
                                <optgroup label="Quartos Disponíveis">
                                <?php foreach ($quartosDisponiveis as $quarto): ?>
                                    <option value="<?= htmlspecialchars($quarto['numero']) ?>">
                                        Quarto <?= htmlspecialchars($quarto['numero']) ?> (Disponível)
                                    </option>
                                <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            
                            <?php if (empty($quartosReservados) && empty($quartosDisponiveis)): ?>
                                <option value="" disabled>Nenhum quarto encontrado</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Seção Avançada -->
                    <div class="col-12 advanced-options" style="display: none;">
                        <div class="border-top pt-3 mt-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Horário Preferencial</label>
                                    <input type="time" class="form-control" name="preferred_time">
                                </div>
                                <div class="col-md-6 d-flex flex-column">
                                    <div class="form-check form-switch mt-auto">
                                        <input class="form-check-input" type="checkbox" name="taxa_custo" id="taxaCustoCheckbox">
                                        <label class="form-check-label" for="taxaCustoCheckbox">
                                            Taxa Adicional
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12" id="campoValor" style="display: none;">
                                    <label class="form-label">Valor do Serviço</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" name="valor_taxa" id="valor_taxa" class="form-control"
                                            placeholder="Valor do serviço">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex gap-3 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-send-check me-2"></i>Solicitar Serviço
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Card de Histórico -->
        <div class="card-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-green m-0"><i class="bi bi-clock-history me-2"></i>Histórico de Serviços</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success" onclick="filterServices('all')">
                        Todos
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="filterServices('pendente')">
                        Pendentes
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="filterServices('concluido')">
                        Concluídos
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle hover-effect">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Horário Preferencial</th>
                            <th>Data da Solicitação</th>
                            <th>Quarto</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr data-status="<?= strtolower($pedido['status']) ?>">
                                <td><?= htmlspecialchars($pedido['tipoServico']) ?></td>
                                <td><?= nl2br(htmlspecialchars($pedido['descricao'])) ?></td>
                                <td>
                                    <span class="status-badge bg-<?= getStatusColor($pedido['status']) ?>">
                                        <?= htmlspecialchars($pedido['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($pedido['prioridade']) ?></td>
                                <td><?= $pedido['horarioPreferencial'] ? date('H:i', strtotime($pedido['horarioPreferencial'])) : 'N/A' ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido['DataSolicitacao'])) ?></td>
                                <td><?= htmlspecialchars($pedido['NumeroQuarto']) ?></td>
                                <td><?= $pedido['TaxaCusto'] ? 'R$ ' . number_format($pedido['TaxaCusto'], 2, ',', '.') : 'N/A' ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Cancelar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-selecionar o quarto se passado por parâmetro
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const quartoParam = urlParams.get('quarto');
            
            if (quartoParam) {
                const selectQuarto = document.getElementById('numeroQuarto');
                if (selectQuarto) {
                    selectQuarto.value = quartoParam;
                }
            }
        });

        // Controles do Formulário
        function toggleFormAdvanced() {
            const advancedOptions = document.querySelector('.advanced-options');
            if (advancedOptions) {
                advancedOptions.style.display = advancedOptions.style.display === 'none' ? 'block' : 'none';
            }
        }

        document.getElementById('taxaCustoCheckbox').addEventListener('change', function() {
            document.getElementById('campoValor').style.display = this.checked ? 'block' : 'none';
        });

        // Controle de Prioridade
        document.querySelectorAll('[data-priority]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-priority]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('priority').value = this.dataset.priority;
            });
        });

        // Filtragem de Serviços
        function filterServices(status) {
            document.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
            });
        }

        // Limpar Formulário
        function clearForm() {
            document.getElementById('serviceForm').reset();
            const advancedOptions = document.querySelector('.advanced-options');
            if (advancedOptions) advancedOptions.style.display = 'none';
            document.getElementById('campoValor').style.display = 'none';
            
            // Resetar prioridade para normal
            document.querySelectorAll('[data-priority]').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.priority === 'normal') {
                    btn.classList.add('active');
                }
            });
            document.getElementById('priority').value = 'normal';
        }

        // Formatação de moeda
        document.addEventListener('DOMContentLoaded', () => {
            const valorTaxaInput = document.getElementById('valor_taxa');
            if (valorTaxaInput) {
                valorTaxaInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = (value/100).toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).replace('R$', '').trim();
                    e.target.value = value;
                });
            }
        });

        // Ativar Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(t => new bootstrap.Tooltip(t));
    </script>
</body>
</html>