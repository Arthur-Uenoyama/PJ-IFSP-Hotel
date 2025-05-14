<?php
session_start();
include '.././db/dbHotel.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Inicialização de variáveis e filtros
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$tipo = $_GET['tipo'] ?? '';

$receitaTotal = 0;
$despesaTotal = 0;
$transacoes = [];

try {
    // Consulta para Receitas
    $sqlReceita = "SELECT SUM(valor) AS total FROM pagamentos WHERE status = 'aprovado'";
    $stmtReceita = $pdo->prepare($sqlReceita);
    $stmtReceita->execute();
    $receitaTotal = $stmtReceita->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Consulta para Despesas (se a tabela existir)
    $tabelaDespesasExiste = $pdo->query("SHOW TABLES LIKE 'despesas'")->rowCount() > 0;
    if($tabelaDespesasExiste) {
        $stmtDespesa = $pdo->query("SELECT SUM(valor) AS total FROM despesas");
        $despesaTotal = $stmtDespesa->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // Consulta principal com filtros
    $sql = "
        (SELECT 'Receita' AS tipo, valor, data_pagamento AS data, 'Pagamento' AS descricao 
        FROM pagamentos WHERE status = 'aprovado')";
    
    if($tabelaDespesasExiste) {
        $sql .= " UNION ALL 
            (SELECT 'Despesa' AS tipo, valor, data_despesa AS data, descricao 
            FROM despesas)";
    }

    // Aplicar filtros
    $where = [];
    $params = [];

    if($data_inicio && $data_fim) {
        $where[] = "data BETWEEN :data_inicio AND :data_fim";
        $params[':data_inicio'] = $data_inicio;
        $params[':data_fim'] = $data_fim;
    }

    if($tipo) {
        $where[] = "tipo = :tipo";
        $params[':tipo'] = $tipo;
    }

    if(!empty($where)) {
        $sql = "SELECT * FROM ($sql) AS subquery WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY data DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$lucroTotal = $receitaTotal - $despesaTotal;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/relatorio_financeiro.css">
</head>
<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <div class="container container-main mt-5 bg-light">
        <div class="title-card mb-4">
            <h1><i class="fas fa-chart-line me-2"></i>Relatório Financeiro</h1>
        </div>

        <!-- Cards de Resumo -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card-resumo text-center">
                    <h5 class="mb-3">Receita Total</h5>
                    <div class="valor receita">
                        R$ <?= number_format($receitaTotal, 2, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-resumo text-center">
                    <h5 class="mb-3">Despesa Total</h5>
                    <div class="valor despesa">
                        R$ <?= number_format($despesaTotal, 2, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-resumo text-center">
                    <h5 class="mb-3">Lucro Total</h5>
                    <div class="valor lucro">
                        R$ <?= number_format($lucroTotal, 2, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-section">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <input type="date" class="form-control" name="data_inicio" 
                        value="<?= htmlspecialchars($data_inicio) ?>" 
                        placeholder="Data inicial" min="2020-01-01" max="<?php echo date('Y-m-d', strtotime('+730 days')); ?>">
                </div>
                <div class="col-12 col-md-3">
                    <input type="date" class="form-control" name="data_fim" 
                        value="<?= htmlspecialchars($data_fim) ?>" 
                        placeholder="Data final" min="2020-01-01" max="<?php echo date('Y-m-d', strtotime('+730 days')); ?>">
                </div>
                <div class="col-12 col-md-4">
                    <select class="form-select" name="tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="Receita" <?= $tipo === 'Receita' ? 'selected' : '' ?>>Receita</option>
                        <?php if($tabelaDespesasExiste): ?>
                        <option value="Despesa" <?= $tipo === 'Despesa' ? 'selected' : '' ?>>Despesa</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-success w-100 btn-icon" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="card-section table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacoes as $transacao): ?>
                    <tr>
                        <td>
                            <span class="status-badge <?= $transacao['tipo'] === 'Receita' ? 'bg-success' : 'bg-danger' ?> text-white">
                                <?= htmlspecialchars($transacao['tipo']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($transacao['descricao']) ?></td>
                        <td class="fw-bold">
                            R$ <?= number_format($transacao['valor'], 2, ',', '.') ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($transacao['data'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>