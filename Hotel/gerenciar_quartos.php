<?php 
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
    <title>Gerenciar Quartos</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
      .w3-red { background-color: red !important; }
      .container { max-width: 800px; margin-top: 50px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg w3-red">
  <div class="container-fluid">
    <a class="navbar-brand text-white" href="#">Hotel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-white" href="index.php">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="reservaformulario.php">Reservar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="gerenciar_quartos.php">Gerenciar Quartos</a>
        </li>
        <li class="nav-item">
          <?php if ($logado): ?>
            <a class="nav-link text-white" href="perfil.php">Perfil</a>
          <?php else: ?>
            <a class="nav-link text-white" href="login.php">Login</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Gerenciar Quartos</h2>
    <table class="table table-bordered mt-4">
        <thead>
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
    <a href="adicionar_quarto.php" class="btn btn-success">Adicionar Novo Quarto</a>
</div>

<!-- Footer -->
<footer class="w3-red text-white text-center py-3 mt-5">
    <p>&copy; 2025 Hotel. Todos os direitos reservados.</p>
    <p><a href="mailto:contato@hotel.com" class="text-white">contato@hotel.com</a></p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
