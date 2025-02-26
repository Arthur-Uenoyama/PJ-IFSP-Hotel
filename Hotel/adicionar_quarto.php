<?php
session_start();
include('dbHotel.php'); 

// Verifica se o usuário está logado e tem permissão para adicionar quartos
if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    $imagem = '';

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['imagem']['name'])) {
        $diretorioDestino = "uploads/";
        if (!is_dir($diretorioDestino)) {
            mkdir($diretorioDestino, 0777, true);
        }

        $nomeArquivo = time() . "_" . basename($_FILES['imagem']['name']);
        $caminhoArquivo = $diretorioDestino . $nomeArquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoArquivo)) {
            $imagem = $caminhoArquivo;
        } else {
            $erro = "Erro ao fazer upload da imagem.";
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO quartos (numero, tipo, preco, descricao, imagem) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$numero, $tipo, $preco, $descricao, $imagem]);
        $sucesso = "Quarto adicionado com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar quarto: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Quarto - Hotel Lux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #f8f9fa;
    }
    .navbar-custom {
      background-color: #343a40;
    }
    .footer {
      background-color: #343a40;
      color: #ffffff;
      padding: 15px 0;
    }
    .container {
      max-width: 600px;
      margin-top: 50px;
    }
    .hero {
      background: url('https://source.unsplash.com/1600x900/?hotel') no-repeat center center;
      background-size: cover;
      height: 100vh;
      position: relative;
      color: #fff;
    }
    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }

        .footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 15px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="#">Hotel Lux</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
        <li class="nav-item"><a class="nav-link" href="perfil.php">Gerenciar Quartos</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Conteúdo Principal -->
<div class="container mt-5">
  <h3 class="mb-4">Adicionar Novo Quarto</h3>

  <?php if (isset($sucesso)): ?>
    <div class="alert alert-success"><?php echo $sucesso; ?></div>
  <?php elseif (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Número do Quarto</label>
      <input type="text" name="numero" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Tipo</label>
      <select name="tipo" class="form-select" required>
        <option value="Simples">Simples</option>
        <option value="Duplo">Duplo</option>
        <option value="Luxo">Luxo</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Preço por Noite (R$)</label>
      <input type="number" name="preco" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Descrição</label>
      <textarea name="descricao" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagem do Quarto</label>
      <input type="file" name="imagem" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Adicionar Quarto</button>
    <a href="gerenciar_quartos.php" class="btn btn-secondary">Voltar</a>
  </form>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </div>
  </footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
