<?php
session_start();
include '../db/dbHotel.php';

if (!isset($_SESSION['id'])) {
  header("Location: ../index.php");
  exit;
}

// Função para pesquisar quartos apenas por número e descrição
function buscarQuartos($pesquisa = '')
{
  $conn = $GLOBALS['pdo'];

  if ($pesquisa) {
    $pesquisa = '%' . trim($pesquisa) . '%';

    $stmt = $conn->prepare("
      SELECT * FROM quartos
      WHERE CAST(numero AS CHAR) LIKE :pesquisa
      OR descricao LIKE :pesquisa
    ");
    $stmt->bindValue(':pesquisa', $pesquisa);
    $stmt->execute();
  } else {
    $stmt = $conn->query("SELECT * FROM quartos");
  }

  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Captura da pesquisa
$pesquisa = $_GET['pesquisa'] ?? '';
$quartos = buscarQuartos($pesquisa);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Quartos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/consulta_quartos.css">
</head>

<body class="bg-light">
  <?php include("../components/navbar.php"); ?>

  <div class="container mt-4 container-main bg-light">
    <div class="title-card mb-4">
      <h1><i class="fas fa-door-open me-2"></i>Gerenciamento de Quartos</h1>
      <button class="btn btn-success" onclick="window.location.href='../cadastros/cadastro_quarto.php'">
        <i class="fas fa-plus me-2"></i>Novo Quarto
      </button>
    </div>

    <div class="card-section">
      <form method="GET" action="./consulta_quartos.php" class="row g-3">
        <div class="col-12 col-md-8">
          <div class="input-group">
            <span class="input-group-text bg-success text-white">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" name="pesquisa"
              placeholder="Pesquisar por número ou descrição..."
              value="<?= htmlspecialchars($pesquisa) ?>">
          </div>
        </div>
        <div class="col-12 col-md-4">
          <button class="btn btn-success w-100" type="submit">
            <i class="fas fa-filter me-2"></i>Filtrar
          </button>
        </div>
      </form>
    </div>

    <div class="card-section table-responsive">
      <?php if (count($quartos) > 0): ?>
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Número</th>
              <th>Preço</th>
              <th>Descrição</th>
              <th>Configuração</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($quartos as $quarto): ?>
              <?php
              $capacidade = [];
              if ($quarto['camas_solteiro']) $capacidade[] = $quarto['camas_solteiro'] . ' Solteiro';
              if ($quarto['beliches']) $capacidade[] = $quarto['beliches'] . ' Beliche';
              if ($quarto['camas_casal']) $capacidade[] = $quarto['camas_casal'] . ' Casal';
              ?>
              <tr>
                <td><?= htmlspecialchars($quarto['numero']) ?></td>
                <td>R$ <?= number_format($quarto['preco'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($quarto['descricao']) ?></td>
                <td><?= implode(', ', $capacidade) ?: '-' ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary"
                    onclick="editarQuarto(<?= $quarto['id'] ?>)">
                    <i class="fas fa-edit me-1"></i>Editar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="alert alert-warning mb-0">Nenhum quarto encontrado</div>
      <?php endif; ?>
    </div>
  </div>

  <?php include("../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function editarQuarto(id) {
      window.location.href = '../cadastros/cadastro_quarto.php?id=' + id;
    }
  </script>
</body>

</html>