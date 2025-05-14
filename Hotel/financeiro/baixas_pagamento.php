<?php
declare(strict_types=1);
session_start();

// Configurações iniciais
require_once __DIR__ . '/.././db/dbHotel.php';

// Verificação de autenticação e permissões
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Constantes e configurações
const ALLOWED_STATUSES = ['pendente', 'confirmada', 'cancelada', 'finalizada', 'em andamento'];
const MAX_DATE_RANGE = 'P2Y'; // 2 anos para o máximo de intervalo de datas

// Funções auxiliares
function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatDateForDisplay(?string $date): string {
    if (empty($date) || !strtotime($date)) return '-';
    
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format('d/m/Y');
    } catch (Exception $e) {
        error_log("Erro ao formatar data: " . $e->getMessage());
        return '-';
    }
}

function formatCurrency(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function validateDateRange(?string $startDate, ?string $endDate): bool {
    if (empty($startDate) || empty($endDate)) return true;
    
    try {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        return $start <= $end;
    } catch (Exception $e) {
        error_log("Erro ao validar intervalo de datas: " . $e->getMessage());
        return false;
    }
}

// Processamento dos parâmetros GET
$filterParams = [
    'data_inicio' => FILTER_SANITIZE_STRING,
    'data_fim' => FILTER_SANITIZE_STRING,
    'status' => FILTER_SANITIZE_STRING
];

$filteredInput = filter_input_array(INPUT_GET, $filterParams);
$dataInicio = $filteredInput['data_inicio'] ?? '';
$dataFim = $filteredInput['data_fim'] ?? '';
$status = in_array($filteredInput['status'] ?? '', ALLOWED_STATUSES) ? $filteredInput['status'] : '';

// Validação das datas
if (!validateDateRange($dataInicio, $dataFim)) {
    $_SESSION['error_message'] = "A data de início não pode ser maior que a data fim";
    header("Location: baixas_pagamento.php");
    exit;
}

// Buscar pagamentos
$pagamentos = [];
try {
    $query = "
        SELECT 
            p.id, 
            p.valor, 
            p.metodo AS tipo_pagamento, 
            p.status AS statusPag,
            r.status,
            p.data_baixa, 
            p.data_pagamento,
            u.nome_completo AS cliente, 
            q.numero AS quarto
        FROM pagamentos p
        INNER JOIN reservas r ON p.reserva_id = r.id
        INNER JOIN usuarios u ON r.usuario_id = u.id
        INNER JOIN quartos q ON r.quarto_id = q.id
    ";

    $whereConditions = [];
    $params = [];
    $types = [];

    // Filtro por período
    if ($dataInicio && $dataFim) {
        $whereConditions[] = "p.data_baixa BETWEEN :data_inicio AND :data_fim";
        $params[':data_inicio'] = $dataInicio;
        $params[':data_fim'] = $dataFim;
        $types[':data_inicio'] = PDO::PARAM_STR;
        $types[':data_fim'] = PDO::PARAM_STR;
    }

    // Filtro por status
    if ($status) {
        $whereConditions[] = "p.status = :status";
        $params[':status'] = $status;
        $types[':status'] = PDO::PARAM_STR;
    }

    // Construir a query final
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $query .= " ORDER BY p.data_baixa DESC";

    $statement = $pdo->prepare($query);
    
    // Bind dos parâmetros de forma segura
    foreach ($params as $key => $value) {
        $statement->bindValue($key, $value, $types[$key] ?? PDO::PARAM_STR);
    }

    $statement->execute();
    $pagamentos = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar pagamentos: " . $e->getMessage());
    $_SESSION['error_message'] = "Ocorreu um erro ao carregar os pagamentos.";
}

// Processar baixa de pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagamentoId'])) {
    $pagamentoId = filter_input(INPUT_POST, 'pagamentoId', FILTER_VALIDATE_INT);
    
    if ($pagamentoId) {
        try {
            $pdo->beginTransaction();

            // Verificar se o pagamento existe e está pendente antes de atualizar
            $checkStmt = $pdo->prepare("SELECT status FROM pagamentos WHERE id = :id FOR UPDATE");
            $checkStmt->bindValue(':id', $pagamentoId, PDO::PARAM_INT);
            $checkStmt->execute();
            $currentStatus = $checkStmt->fetchColumn();

            if ($currentStatus === 'pendente') {
                $updateStmt = $pdo->prepare("UPDATE pagamentos 
                                           SET status = 'aprovado', data_baixa = NOW() 
                                           WHERE id = :id");
                $updateStmt->bindValue(':id', $pagamentoId, PDO::PARAM_INT);
                $updateStmt->execute();

                $pdo->commit();
                $_SESSION['success_message'] = "Baixa confirmada com sucesso!";
            } else {
                $pdo->rollBack();
                $_SESSION['error_message'] = "O pagamento não está mais pendente.";
            }

            header("Location: baixas_pagamento.php");
            exit;
            
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erro ao processar pagamento: " . $e->getMessage());
            $_SESSION['error_message'] = "Erro ao processar o pagamento.";
            header("Location: baixas_pagamento.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gerenciamento de pagamentos do hotel">
    <title>Baixa de Pagamentos</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/baixas_pagamento.css?v=<?= filemtime('../css/baixas_pagamento.css') ?>">

    <!-- Preload de fontes -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2"
        as="font" type="font/woff2" crossorigin>
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <div class="container container-main mt-5 bg-light">

        <div class="title-card mb-4">
            <h1><i class="fas fa-hand-holding-usd me-2"></i>Baixa de Pagamentos</h1>
        </div>

        <!-- Filtros -->
        <div class="card-section">
            <form method="GET" class="row g-3" id="filterForm">
                <div class="col-12 col-md-3">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                        value="<?= sanitizeInput($dataInicio) ?>" min="2020-01-01"
                        max="<?= (new DateTime())->add(new DateInterval(MAX_DATE_RANGE))->format('Y-m-d') ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                        value="<?= sanitizeInput($dataFim) ?>" min="2020-01-01"
                        max="<?= (new DateTime())->add(new DateInterval(MAX_DATE_RANGE))->format('Y-m-d') ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="" <?= empty($status) ? 'selected' : '' ?>>Todos os Status</option>
                        <?php foreach (ALLOWED_STATUSES as $allowedStatus): ?>
                        <option value="<?= $allowedStatus ?>" <?= $status === $allowedStatus ? 'selected' : '' ?>>
                            <?= ucfirst($allowedStatus) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100 btn-icon" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabela de resultados -->
        <div class="card-section table-responsive">
            <?php if (empty($pagamentos)): ?>
            <div class="alert alert-warning">
                Nenhum pagamento encontrado com os filtros selecionados.
            </div>
            <?php else: ?>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Quarto</th>
                        <th>Valor</th>
                        <th>Data Pagamento</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagamentos as $pagamento): ?>
                    <tr>
                        <td><?= sanitizeInput($pagamento['cliente']) ?></td>
                        <td><?= sanitizeInput($pagamento['quarto']) ?></td>
                        <td class="fw-bold"><?= formatCurrency((float)$pagamento['valor']) ?></td>
                        <td><?= formatDateForDisplay($pagamento['data_baixa']) ?></td>
                        <td><?= sanitizeInput($pagamento['tipo_pagamento']) ?></td>
                        <td>
                            <?php
                                        $badgeClass = match ($pagamento['status']) {
                                            'confirmada' => 'bg-success',
                                            'cancelada' => 'bg-danger',
                                            'finalizada' => 'bg-primary',
                                            'em andamento' => 'bg-info',
                                            default => 'bg-warning text-dark'
                                        };
                                    ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= ucfirst(sanitizeInput($pagamento['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="pagamentoId" value="<?= (int)$pagamento['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-success"
                                    <?= (($pagamento['status'] === 'pendente' || $pagamento['status'] === 'cancelada') || $pagamento['statusPag'] === 'aprovado') ? 'disabled' : '' ?>
                                    onclick="return confirm('Confirmar baixa deste pagamento?')">
                                    <i class="fas fa-check-circle me-1"></i>Confirmar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validação do formulário de filtro
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const dataInicio = document.getElementById('data_inicio').value;
        const dataFim = document.getElementById('data_fim').value;

        if (dataInicio && dataFim && new Date(dataInicio) > new Date(dataFim)) {
            alert('A data de início não pode ser maior que a data fim');
            e.preventDefault();
            return false;
        }
        return true;
    });

    // Adiciona máscara para campos de valor (se necessário)
    document.querySelectorAll('.currency-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            this.value = value;
        });
    });
    </script>
</body>

</html>