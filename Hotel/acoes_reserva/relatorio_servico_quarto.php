<?php
$servicosAtendidos = 0;
$servicosPendentes = 0;
$lucroTotal = 0;

session_start();

// Incluir a conexão com o banco de dados
include '.././db/dbHotel.php';

if (!isset($_SESSION['id'])) {
  header("Location: ../index.php");
  exit;
}

// Inicializando filtros
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$tipo = $_GET['tipo'] ?? '';

$receitaTotal = 0;
$despesaTotal = 0;
$transacoes = [];

try {
  // Receita total
  $stmtReceita = $pdo->prepare("SELECT SUM(Valor) AS Total FROM Pagamentos WHERE Status = 'aprovado'");
  $stmtReceita->execute();
  $receitaTotal = $stmtReceita->fetch(PDO::FETCH_ASSOC)['Total'] ?? 0;

  $despesaTotal = 0;

  $sqlTransacoes = "
    SELECT 
      'Receita' AS Tipo, p.Valor, p.data_pagamento AS Data, 'Pagamento' AS Descricao, q.numero AS numero_quarto, u.nome_completo AS cliente 
    FROM Pagamentos p
    JOIN reservas r ON p.reserva_id = r.id
    JOIN quartos q ON r.quarto_id = q.id
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE p.Status = 'aprovado'";

  if ($data_inicio && $data_fim) {
    $sqlTransacoes .= " AND p.data_pagamento BETWEEN :data_inicio AND :data_fim";
  }

  if ($tipo) {
    $sqlTransacoes .= " AND p.Status = :tipo";
  }

  $sqlTransacoes .= " UNION ALL
    SELECT 
      'Serviço' AS Tipo, s.Valor, s.criado_em AS Data, s.Descricao, q.numero AS numero_quarto, u.nome_completo AS cliente
    FROM pedidos_servico_quarto s
    JOIN reservas r ON s.reserva_id = r.id
    JOIN quartos q ON r.quarto_id = q.id
    JOIN usuarios u ON r.usuario_id = u.id";

  if ($data_inicio && $data_fim) {
    $sqlTransacoes .= " AND s.criado_em BETWEEN :data_inicio AND :data_fim";
  }

  if ($tipo) {
    $sqlTransacoes .= " AND s.status = :tipo";
  }

  $sqlTransacoes .= " ORDER BY Data DESC";

  $stmtTransacoes = $pdo->prepare($sqlTransacoes);

  if ($data_inicio && $data_fim) {
    $stmtTransacoes->bindParam(':data_inicio', $data_inicio);
    $stmtTransacoes->bindParam(':data_fim', $data_fim);
  }

  if ($tipo) {
    $stmtTransacoes->bindParam(':tipo', $tipo);
  }

  $stmtTransacoes->execute();
  $transacoes = $stmtTransacoes->fetchAll(PDO::FETCH_ASSOC);

  // Contagem de serviços e lucro
  foreach ($transacoes as $transacao) {
    if ($transacao['Tipo'] === 'Serviço') {
      $lucroTotal += $transacao['Valor'];
      if (str_contains(strtolower($transacao['Descricao']), 'pendente')) {
        $servicosPendentes++;
      } else {
        $servicosAtendidos++;
      }
    }
  }

} catch (PDOException $e) {
  echo "Erro ao carregar serviços: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatório de Serviços de Quarto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/relatorio_servico_quarto.css">
</head>

<body class="bg-light">
  <?php include("../components/navbar.php"); ?>

  <div class="container container-main mt-5 bg-light">
    <div class="title-card mb-4">
      <h1><i class="fas fa-concierge-bell me-2"></i>Relatório de Serviços de Quarto</h1>
      <button class="btn btn-success btn-icon" onclick="window.location.href='../acoes_reserva/servico_quarto.php'">
        <i class="fas fa-plus me-2"></i>Novo Serviço
      </button>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card-resumo text-center">
          <h5 class="mb-3">Serviços Atendidos</h5>
          <div class="valor receita">
            <?= $servicosAtendidos ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-resumo text-center">
          <h5 class="mb-3">Serviços Pendentes</h5>
          <div class="valor despesa">
            <?= $servicosPendentes ?>
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

    <!-- Card de Filtros -->
    <div class="card-section">
      <form method="GET" action="../consulta/consulta_servicos.php" class="row g-3">
        <div class="col-12 col-md-3">
          <input type="date" class="form-control" name="data_inicio" id="data_inicio" min="2020-01-01" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12 col-md-3">
          <input type="date" class="form-control" name="data_fim" id="data_fim" min="2020-01-01" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-3">
          <select class="form-select" name="tipo" id="tipo">
            <option value="">Todos</option>
            <option value="Atendido" <?= $tipo === 'Atendido' ? 'selected' : '' ?>>Atendido</option>
            <option value="Pendente" <?= $tipo === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <button class="btn btn-success w-100 btn-icon" type="submit">
            <i class="fas fa-filter me-2"></i>Filtrar
          </button>
        </div>
      </form>
    </div>

    <!-- Card da Tabela -->
    <div class="card-section table-container bg-light">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Quarto</th>
            <th>Cliente</th>
            <th>Tipo de Serviço</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transacoes as $transacao): ?>
            <tr>
              <td><?= $transacao['numero_quarto'] ?? 'N/A' ?></td>
              <td><?= $transacao['cliente'] ?? 'N/A' ?></td>
              <td><?= $transacao['Descricao'] ?? 'N/A' ?></td>
              <td>
                <span class="status-badge <?= ($transacao['Tipo'] === 'Receita') ? 'badge-atendido' : 'badge-pendente' ?>">
                  <?= $transacao['Tipo'] ?>
                </span>
              </td>
              <td class="fw-bold">R$ <?= number_format($transacao['Valor'] ?? 0, 2, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($transacao['Data'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include("../components/footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
