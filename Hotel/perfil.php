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

// Se for cliente, carregar reservas
$reservas = [];
if ($usuarioTipo === 'cliente') {
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
}

// Se for hotel, carregar todas as reservas
if ($usuarioTipo === 'hotel') {
    try {
        $stmt = $pdo->prepare("SELECT r.Id, r.DataCheckIn, r.DataCheckOut, q.Nome AS Quarto, u.Nome AS Cliente
                               FROM Reservas r
                               JOIN Quartos q ON r.QuartoId = q.Id
                               JOIN Usuarios u ON r.ClienteId = u.Id");
        $stmt->execute();
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao carregar reservas: " . $e->getMessage();
    }
}
session_start();
include('dbHotel.php');
$logado = isset($_SESSION['usuarioId']);

// Consulta para obter os quartos
try {
    $stmt = $pdo->query("SELECT * FROM quartos");
    $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar quartos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perfil - Hotel Lux</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    body {
      background: #f8f9fa;
    }
    .profile-header {
      background: url('https://source.unsplash.com/1600x400/?hotel,lobby') no-repeat center center;
      background-size: cover;
      position: relative;
      height: 300px;
      color: #fff;
    }
    .profile-header::before {
      content: "";
      position: absolute;
      top:0;
      left:0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .profile-header .header-content {
      position: relative;
      z-index: 2;
      padding: 100px 0;
      text-align: center;
    }
    .profile-container {
      margin-top: -100px;
    }
    .card-profile {
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">Hotel Lux</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <?php if ($usuarioTipo === 'cliente'): ?>
            <li class="nav-item"><a class="nav-link" href="reservaformulario.php">Fazer Reserva</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="adicionar_quarto.php">Adicionar Quartos</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Profile Header -->
  <header class="profile-header">
    <div class="header-content">
      <h1>Bem-vindo, <?php echo $usuario['Nome']; ?></h1>
      <p><?php echo $usuarioTipo === 'cliente' ? 'Perfil: Cliente' : 'Perfil: Administrador do Hotel'; ?></p>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container profile-container">
    <!-- Dados do Usuário -->
    <div class="card card-profile p-4 mb-4">
      <div class="row">
        <div class="col-md-6">
          <h4>Informações do Usuário</h4>
          <p><strong>Email:</strong> <?php echo $usuario['Email']; ?></p>
        </div>
      </div>
    </div>

    <!-- Reservas -->
    <div class="card card-profile p-4">
      <h4>Reservas</h4>
      <?php if (count($reservas) > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Quarto</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <?php if ($usuarioTipo === 'hotel'): ?>
                  <th>Cliente</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $reserva): ?>
                <tr>
                  <td><?php echo $reserva['Quarto']; ?></td>
                  <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckIn'])); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckOut'])); ?></td>
                  <?php if ($usuarioTipo === 'hotel'): ?>
                    <td><?php echo $reserva['Cliente']; ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>Nenhuma reserva encontrada.</p>
      <?php endif; ?>
    </div>
  </div>


  <!-- Conteúdo Principal -->
<div class="container">
  <h2 class="text-center mb-4">Gerenciar Quartos</h2>
  <div class="table-responsive">
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
</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
