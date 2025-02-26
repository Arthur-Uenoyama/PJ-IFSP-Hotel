<?php
session_start();
include 'dbHotel.php';

$logado = isset($_SESSION['usuarioId']);
$erroLogin = "";
$erroCadastro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Processar Login
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        try {
            $stmt = $pdo->prepare("SELECT Id, Nome, Email, Senha, Tipo FROM Usuarios WHERE Email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['Senha'])) {
                $_SESSION['usuarioId'] = $usuario['Id'];
                $_SESSION['usuarioNome'] = $usuario['Nome'];
                $_SESSION['usuarioTipo'] = $usuario['Tipo']; // 'cliente' ou 'hotel'

                // Redireciona com base no tipo de usuário
                if ($usuario['Tipo'] === 'hotel') {
                    header("Location: perfil.php");
                } else {
                    header("Location: perfil.php");
                }
                exit;
            } else {
                $erroLogin = "Email ou senha inválidos.";
            }
        } catch (PDOException $e) {
            $erroLogin = "Erro ao conectar ao banco de dados.";
        }
    } elseif (isset($_POST['cadastro'])) {
        // Processar Cadastro
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = password_hash(trim($_POST['senha']), PASSWORD_DEFAULT);
        $tipo = $_POST['tipo']; // 'cliente' ou 'hotel'

        try {
            $stmt = $pdo->prepare("INSERT INTO Usuarios (Nome, Email, Senha, Tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $tipo]);

            $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Faça login.";
        } catch (PDOException $e) {
            $erroCadastro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotel Lux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
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

    /* Estilos dos modais */
    .w3-modal-content { border-radius: 10px; }
        .modal-header { font-size: 1.5rem; font-weight: bold; color: #333; }

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
          <li class="nav-item"><a class="nav-link active" href="#">Início</a></li>
          <li class="nav-item"><a class="nav-link" href="#rooms">Quartos</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Serviços</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">Sobre</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contato</a></li>
          <li class="nav-item">
            <?php if ($logado): ?>
                <a class="nav-link text-white" href="perfil.php">Perfil</a>
            <?php else: ?>
                <button onclick="document.getElementById('loginModal').style.display='block'" class="w3-button w3-green w3-round">Login</button>
            <?php endif; ?>
        </li>
        </ul>
      </div>
    </div>
  </nav>

<!-- Modal de Login -->
<div id="loginModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
        <div class="w3-center"><br>
            <span onclick="document.getElementById('loginModal').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright">×</span>
            <img src="img/igreja.jpg" alt="Avatar" style="width:30%" class="w3-circle w3-margin-top">
        </div>

        <!-- Formulário de Login -->
        <form class="w3-container" method="POST">
            <input type="hidden" name="login">
            <div class="w3-section">
                <label><b>Email</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="email" name="email" required>
                
                <label><b>Senha</b></label>
                <input class="w3-input w3-border" type="password" name="senha" required>
                
                <button class="w3-button w3-block w3-green w3-section w3-padding w3-round" type="submit">Entrar</button>
                
                <p>Não possui um login? <a href="#" onclick="abrirCadastro()">Registrar</a></p>
            </div>

            <?php if (!empty($erroLogin)): ?>
                <div class="w3-panel w3-red w3-padding"><?php echo $erroLogin; ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Modal de Cadastro -->
<div id="cadastroModal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
        <div class="w3-center"><br>
            <span onclick="document.getElementById('cadastroModal').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright">×</span>
        </div>

        <form class="w3-container" method="POST">
            <input type="hidden" name="cadastro">
            <div class="w3-section">
                <label><b>Nome</b></label>
                <input class="w3-input w3-border" type="text" name="nome" required>

                <label><b>Email</b></label>
                <input class="w3-input w3-border" type="email" name="email" required>

                <label><b>Senha</b></label>
                <input class="w3-input w3-border" type="password" name="senha" required>

                <label><b>Tipo de Conta</b></label>
                <select class="w3-input w3-border" name="tipo" required>
                    <option value="cliente">Cliente</option>
                    <option value="hotel">Hotel</option>
                </select>

                <button class="w3-button w3-block w3-green w3-section w3-padding w3-round" type="submit">Cadastrar</button>
            </div>
            
            <?php if (!empty($erroCadastro)): ?>
                <div class="w3-panel w3-red w3-padding"><?php echo $erroCadastro; ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>

  <!-- Hero Section -->
  <header class="hero d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center">
      <h1 class="display-4 fw-bold">Bem-vindo ao Hotel Lux</h1>
      <p class="lead">Sua estadia de luxo e conforto em um ambiente sofisticado</p>
      <a href="quartos.php" class="btn btn-primary btn-lg mt-3">Reserve Agora</a>
    </div>
  </header>

  <!-- Seção de Quartos -->
  <section id="rooms" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2>Nossos Quartos</h2>
        <p>Experimente o melhor em conforto e elegância</p>
      </div>
      <div class="row">
        <!-- Quarto Deluxe -->
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="https://source.unsplash.com/600x400/?room" class="card-img-top" alt="Quarto Deluxe">
            <div class="card-body">
              <h5 class="card-title">Quarto Deluxe</h5>
              <p class="card-text">Quarto espaçoso com vista panorâmica e comodidades exclusivas.</p>
              <a href="#reservation" class="btn btn-outline-primary">Ver Detalhes</a>
            </div>
          </div>
        </div>
        <!-- Suite Presidencial -->
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="https://source.unsplash.com/600x400/?suite" class="card-img-top" alt="Suite Presidencial">
            <div class="card-body">
              <h5 class="card-title">Suite Presidencial</h5>
              <p class="card-text">O ápice do luxo, com sala de estar privativa e serviços exclusivos.</p>
              <a href="#reservation" class="btn btn-outline-primary">Ver Detalhes</a>
            </div>
          </div>
        </div>
        <!-- Quarto Executivo -->
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="https://source.unsplash.com/600x400/?hotel-room" class="card-img-top" alt="Quarto Executivo">
            <div class="card-body">
              <h5 class="card-title">Quarto Executivo</h5>
              <p class="card-text">Ideal para viajantes de negócios, com todo conforto e tecnologia.</p>
              <a href="#reservation" class="btn btn-outline-primary">Ver Detalhes</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Serviços -->
  <section id="services" class="bg-light py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2>Serviços</h2>
        <p>Descubra nossas ofertas exclusivas</p>
      </div>
      <div class="row">
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-wifi fs-1"></i>
          <h4 class="mt-3">Wi-Fi Grátis</h4>
          <p>Conexão rápida e segura em todas as áreas do hotel.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-cup-straw fs-1"></i>
          <h4 class="mt-3">Restaurante</h4>
          <p>Culinária refinada e ambiente aconchegante para suas refeições.</p>
        </div>
        <div class="col-md-4 text-center mb-4">
          <i class="bi bi-bell fs-1"></i>
          <h4 class="mt-3">Serviço de Quarto</h4>
          <p>Atendimento 24 horas para garantir sua comodidade.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção Sobre o Hotel -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="https://source.unsplash.com/600x400/?hotel-lobby" alt="Sobre Nós" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
          <h2>Sobre o Hotel Lux</h2>
          <p>Localizado no coração da cidade, o Hotel Lux oferece uma experiência única de conforto e sofisticação. Nossa missão é proporcionar momentos inesquecíveis com serviços de alta qualidade e atendimento personalizado.</p>
          <a href="#contact" class="btn btn-primary">Saiba Mais</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Reserva -->
  <section id="reservation" class="bg-primary text-white py-5">
    <div class="container text-center">
      <h2 class="mb-4">Faça sua Reserva</h2>
      <p class="mb-4">Garanta já a sua estadia com condições especiais</p>
      <a href="#" class="btn btn-light btn-lg">Reserve Agora</a>
    </div>
  </section>

  <!-- Seção de Contato -->
  <section id="contact" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2>Contato</h2>
        <p>Entre em contato e tire suas dúvidas</p>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <form>
            <div class="mb-3">
              <label for="name" class="form-label">Nome</label>
              <input type="text" class="form-control" id="name" placeholder="Seu nome">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" class="form-control" id="email" placeholder="Seu e-mail">
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Mensagem</label>
              <textarea class="form-control" id="message" rows="4" placeholder="Sua mensagem"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Hotel Lux. Todos os direitos reservados.</p>
    </div>
  </footer>

  <script>
    function abrirCadastro() {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('cadastroModal').style.display = 'block';
    }
</script>

  <!-- Bootstrap JS e dependências -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
