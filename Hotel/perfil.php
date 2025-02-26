<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Buscar informações do usuário
try {
    $stmt = $pdo->prepare("SELECT Id, Nome, Email, Tipo FROM Usuarios WHERE Id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar usuário: " . $e->getMessage();
}

$reservas = [];
$quartos = [];

if ($usuarioTipo === 'cliente') {
    // Carregar reservas do cliente
    try {
        $stmt = $pdo->prepare("SELECT r.Id, r.DataCheckIn, r.DataCheckOut, q.Nome AS Quarto
                               FROM Reservas r
                               JOIN Quartos q ON r.QuartoId = q.Id
                               WHERE r.ClienteId = ?");
        $stmt->execute([$usuarioId]);
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao carregar reservas: " . $e->getMessage();
    }
} elseif ($usuarioTipo === 'hotel') {
    // Carregar todos os quartos do hotel
    try {
        $stmt = $pdo->query("SELECT * FROM quartos");
        $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perfil - Hotel Lux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">Hotel Lux</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <?php if ($usuarioTipo === 'cliente'): ?>
            <li class="nav-item"><a class="nav-link" href="quartos.php">Fazer Reserva</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="adicionar_quarto.php">Adicionar Quartos</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5 pt-5">
    <h1 class="text-center">Bem-vindo, <?php echo htmlspecialchars($usuario['Nome']); ?></h1>
    <p class="text-center">Perfil: <?php echo $usuarioTipo === 'cliente' ? 'Cliente' : 'Administrador do Hotel'; ?></p>
    
    <?php if ($usuarioTipo === 'cliente'): ?>
      <div class="card p-4">
        <h4>Minhas Reservas</h4>
        <?php if (count($reservas) > 0): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Quarto</th>
                <th>Check-in</th>
                <th>Check-out</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $reserva): ?>
                <tr>
                  <td><?php echo htmlspecialchars($reserva['Quarto']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckIn'])); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckOut'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>Nenhuma reserva encontrada.</p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="card p-4">
        <h4>Gerenciar Quartos</h4>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>Número</th>
              <th>Tipo</th>
              <th>Preço</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($quartos as $quarto): ?>
              <tr>
                <td><?php echo htmlspecialchars($quarto['numero']); ?></td>
                <td><?php echo htmlspecialchars($quarto['tipo']); ?></td>
                <td>R$ <?php echo number_format($quarto['preco'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($quarto['status']); ?></td>
                <td>
                  <a href="editar_quarto.php?id=<?php echo $quarto['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                  <a href="excluir_quarto.php?id=<?php echo $quarto['id']; ?>" class="btn btn-danger btn-sm">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
