<?php
session_start();
include './db/dbHotel.php';

$logado = isset($_SESSION['id']);

if ($logado) {
  header("Location: home.php");
  exit(); // Always add exit after header redirect
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Apê Pousada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://www.w3schools.com/w3css/4/w3.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/index.css">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <div class="div-navbar">
        <a class="navbar-brand" href="#">
          <i class="bi bi-building fs-4 me-2"></i>Apê Pousada
        </a>
      </div>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse d-flex justify-content-center" id="navbarContent">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" id="nav1" onclick="ativarNavItem(event, 'nav1')">
              <i class="fas fa-home me-1"></i>Início
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#rooms" id="nav2" onclick="ativarNavItem(event, 'nav2')">
              <i class="fas fa-bed me-1"></i>Quartos
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#services" id="nav3" onclick="ativarNavItem(event, 'nav3')">
              <i class="fas fa-concierge-bell me-1"></i>Serviços
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#about" id="nav4" onclick="ativarNavItem(event, 'nav4')">
              <i class="fas fa-info-circle me-1"></i>Sobre
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#contact" id="nav5" onclick="ativarNavItem(event, 'nav5')">
              <i class="fas fa-envelope me-1"></i>Contato
            </a>
          </li>
        </ul>
      </div>
      <div class="div-navbar-button">
        <?php if ($logado): ?>
          <a class="nav-link text-white" href="home.php">
            <i class="bi bi-person-circle me-2"></i>Perfil
          </a>
        <?php else: ?>
          <button onclick="document.getElementById('loginModal').style.display='block'" class="btn btn-success">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
          </button>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Modal de Login -->
  <div id="loginModal" class="w3-modal">
    <div class="w3-modal-content" style="max-width:450px;border-radius:10px;overflow:hidden">
      <!-- Cabeçalho com borda superior arredondada -->
      <div class="w3-container" style="
          background: linear-gradient(135deg, #2e7d32, #1b5e20);
          padding:30px;
          border-radius:10px 10px 0 0;
      ">
        <span onclick="document.getElementById('loginModal').style.display='none'"
          class="w3-button w3-display-topright w3-text-white">&times;</span>
        <h2 class="w3-center w3-text-white mb-4">
          <i class="fas fa-door-open me-2"></i>Bem-vindo
        </h2>
      </div>

      <form class="w3-container" method="POST" style="padding:30px" id="loginForm">
        <input type="hidden" name="login">

        <!-- Campo Email -->
        <div class="w3-section">
          <label class="w3-text-dark-grey"><b>E-mail</b></label>
          <div class="w3-input-group">
            <span class="w3-input-icon"><i class="fas fa-envelope text-success"></i></span>
            <input class="w3-input w3-border w3-round-large"
              type="email"
              name="email"
              placeholder="exemplo@email.com"
              required
              style="padding-left:40px"
              value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
          </div>
        </div>

        <!-- Campo Senha -->
        <div class="w3-section">
          <label class="w3-text-dark-grey"><b>Senha</b></label>
          <div class="w3-input-group">
            <span class="w3-input-icon"><i class="fas fa-lock text-success"></i></span>
            <input class="w3-input w3-border w3-round-large"
              type="password"
              name="senha"
              placeholder="••••••••"
              required
              style="padding-left:40px">
          </div>
        </div>

        <!-- Links de Ação -->
        <div class="w3-section w3-center" style="margin:20px 0">
          <a href="#" onclick="abrirEsqueciSenha()"
            class="w3-text-dark-grey w3-hover-text-green"
            style="text-decoration:none;font-size:14px">
            Esqueci minha senha
          </a>
          <span class="w3-mx-2">|</span>
          <a href="#" onclick="abrirCadastro()"
            class="w3-text-dark-grey w3-hover-text-green"
            style="text-decoration:none;font-size:14px">
            Criar nova conta
          </a>
        </div>

        <!-- Botão de Login -->
        <button class="w3-button w3-block w3-round-large w3-padding"
          type="submit"
          style="background:#2e7d32;color:white">
          <i class="fas fa-sign-in-alt me-2"></i>Entrar
        </button>
      </form>
    </div>
  </div>

  <!-- Modal Esqueci a Senha -->
  <div id="esqueciSenhaModal" class="w3-modal">
    <div class="w3-modal-content" style="max-width:450px;border-radius:10px;overflow:hidden">
      <!-- Cabeçalho -->
      <div class="w3-container" style="
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            padding:30px;
            border-radius:10px 10px 0 0;
        ">
        <span onclick="document.getElementById('esqueciSenhaModal').style.display='none'"
          class="w3-button w3-display-topright w3-text-white">&times;</span>
        <h2 class="w3-center w3-text-white mb-4">
          <i class="fas fa-key me-2"></i>Redefinir Senha
        </h2>
      </div>

      <form class="w3-container" method="POST" style="padding:30px">
        <!-- Email -->
        <div class="w3-section">
          <label class="w3-text-dark-grey"><b>E-mail cadastrado</b></label>
          <div class="w3-input-group">
            <span class="w3-input-icon"><i class="fas fa-envelope text-success"></i></span>
            <input class="w3-input w3-border w3-round-large"
              type="email"
              name="email_recuperacao"
              placeholder="exemplo@email.com"
              required
              style="padding-left:40px">
          </div>
        </div>

        <!-- Nova Senha -->
        <div class="w3-section">
          <label class="w3-text-dark-grey"><b>Nova Senha</b></label>
          <div class="w3-input-group">
            <span class="w3-input-icon"><i class="fas fa-lock text-success"></i></span>
            <input class="w3-input w3-border w3-round-large"
              type="password"
              name="novaSenha"
              id="novaSenha"
              placeholder="••••••••"
              required
              style="padding-left:40px"
              oninput="validarSenhas()">
          </div>
        </div>

        <!-- Confirmar Senha -->
        <div class="w3-section">
          <label class="w3-text-dark-grey"><b>Confirmar Nova Senha</b></label>
          <div class="w3-input-group">
            <span class="w3-input-icon"><i class="fas fa-check-circle text-success"></i></span>
            <input class="w3-input w3-border w3-round-large"
              type="password"
              name="confirmarSenha"
              id="confirmarSenha"
              placeholder="••••••••"
              required
              style="padding-left:40px"
              oninput="validarSenhas()">
          </div>
          <div id="senhaError" class="w3-text-red" style="display:none;font-size:14px">
            <i class="fas fa-exclamation-circle"></i> As senhas não coincidem
          </div>
        </div>

        <!-- Botão -->
        <button class="w3-button w3-block w3-round-large w3-padding"
          type="submit"
          id="submitSenha"
          style="background:#2e7d32;color:white;margin-top:20px">
          <i class="fas fa-sync-alt me-2"></i>Redefinir Senha
        </button>

        <!-- Voltar para Login -->
        <div class="w3-center w3-margin-top">
          <a href="#"
            class="w3-text-dark-grey w3-hover-text-green"
            style="text-decoration:none;font-size:14px"
            onclick="fecharEsqueciSenha();document.getElementById('loginModal').style.display='block'">
            <i class="fas fa-arrow-left me-2"></i>Voltar para Login
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal de Cadastro -->
  <?php include("./components/cadastro.php"); ?>

  <!-- Hero Section -->
  <header class="hero d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center">
      <h1 class="display-4 fw-bold">Bem-vindo ao Apê Pousada</h1>
      <p class="lead">Sua estadia de luxo e conforto em um ambiente sofisticado</p>
      <?php if ($logado): ?>
        <a href="./hospedes/reserva_quartos_cliente.php" class="btn btn-success btn-lg mt-3">
          <i class="bi bi-calendar-check me-2"></i>Reservar Agora
        </a>
      <?php else: ?>
        <a href="#" class="btn btn-success btn-lg mt-3" onclick="document.getElementById('loginModal').style.display='block'">
          <i class="bi bi-calendar-check me-2"></i>Reservar Agora
        </a>
      <?php endif; ?>

    </div>
  </header>

  <!-- Seção de Quartos Personalizável - Versão Estilizada -->
  <section id="rooms" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="h2 text-success">Monte Seu Quarto</h2>
        <p class="lead text-muted">Configure suas datas e acomodação</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-0 shadow-lg overflow-hidden">
            <div class="card-body p-4">

              <!-- Seção de Datas com Borda -->
              <div class="date-section border p-3 rounded-3 mb-4 bg-light">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-success">Check-in</label>
                    <input type="date" class="form-control border-success" id="checkinDate" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+729 days')); ?>" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold text-success">Check-out</label>
                    <input type="date" class="form-control border-success" id="checkoutDate" min="<?php echo date('Y-m-d', strtotime('+1 days')); ?>" max="<?php echo date('Y-m-d', strtotime('+730 days')); ?>" required>
                  </div>
                </div>
              </div>

              <!-- Controles de Configuração -->
              <h5 class="mb-4 text-success">Selecione as camas:</h5>
              <div class="row g-4">
                <!-- Beliche -->
                <div class="col-md-4">
                  <div class="p-4 text-center bed-card">
                    <div class="icon-wrapper mb-3">
                      <img src="https://img.icons8.com/?size=60&id=20629&format=png&color=2e7d32" alt="">
                    </div>
                    <h4 class="mb-2">Beliche</h4>
                    <small class="text-muted d-block mb-3">(2 pessoas cada)</small>
                    <div class="counter-controls">
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('bunk', -1)">
                        <i class="fas fa-minus"></i>
                      </button>
                      <span id="bunkCount" class="count-display">0</span>
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('bunk', 1)">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Cama Casal -->
                <div class="col-md-4">
                  <div class="p-4 text-center bed-card">
                    <div class="icon-wrapper mb-3">
                      <img src="https://img.icons8.com/?size=60&id=59736&format=png&color=2e7d32" alt="">
                    </div>
                    <h4 class="mb-2">Cama Casal</h4>
                    <small class="text-muted d-block mb-3">(2 pessoas)</small>
                    <div class="counter-controls">
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('double', -1)">
                        <i class="fas fa-minus"></i>
                      </button>
                      <span id="doubleCount" class="count-display">0</span>
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('double', 1)">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Cama Solteiro -->
                <div class="col-md-4">
                  <div class="p-4 text-center bed-card">
                    <div class="icon-wrapper mb-3">
                      <i class="fas fa-bed fa-3x text-success"></i>
                    </div>
                    <h4 class="mb-2">Solteiro</h4>
                    <small class="text-muted d-block mb-3">(1 pessoa cada)</small>
                    <div class="counter-controls">
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('single', -1)">
                        <i class="fas fa-minus"></i>
                      </button>
                      <span id="singleCount" class="count-display">0</span>
                      <button class="btn btn-sm btn-outline-success" onclick="adjustBed('single', 1)">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Resumo -->
            <div class="summary-box mt-5 p-4 bg-success text-white rounded-3">
              <div class="row align-items-center">
                <div class="col-12 text-center">
                  <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fas fa-users fa-2x"></i>
                    <div>
                      <small class="d-block">Total de Pessoas</small>
                      <span id="totalPeople" class="h2 mb-0">0</span>/4
                    </div>
                  </div>
                </div>
              </div>

              <?php if ($logado): ?>
                <button class="btn btn-light btn-lg w-100 mt-4" id="reserveButton" onclick="redirectToReservation()" disabled>
                  <i class="fas fa-check-circle me-2"></i>Buscar disponibilidade
                </button>
              <?php else: ?>
                <button class="btn btn-light btn-lg w-100 mt-4" id="reserveButton" onclick="document.getElementById('loginModal').style.display='block'" disabled>
                  <i class="fas fa-check-circle me-2"></i>Buscar disponibilidade
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Serviços -->
  <section id="services" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="display-5 fw-bold mb-3">Serviços Essenciais</h2>
        <p class="text-muted">Tudo para sua produtividade e conforto básico</p>
      </div>

      <div class="row g-4">
        <!-- Wi-Fi -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm service-card">
            <div class="card-body text-center p-4">
              <div class="icon-wrapper mb-4">
                <i class="fas fa-wifi fa-2x text-success"></i>
              </div>
              <h4 class="mb-3">Wi-Fi Corporativo</h4>
              <p class="text-muted mb-0">
                Conexão estável de 100MB para trabalho remoto e videoconferências
              </p>
            </div>
          </div>
        </div>

        <!-- Café -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm service-card">
            <div class="card-body text-center p-4">
              <div class="icon-wrapper mb-4">
                <i class="fas fa-mug-hot fa-2x text-success"></i>
              </div>
              <h4 class="mb-3">Café Executivo</h4>
              <p class="text-muted mb-0">
                Café da manhã rápido das 5h30 às 8h30
              </p>
            </div>
          </div>
        </div>

        <!-- Serviço Rápido -->
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm service-card">
            <div class="card-body text-center p-4">
              <div class="icon-wrapper mb-4">
                <i class="fas fa-concierge-bell fa-2x text-success"></i>
              </div>
              <h4 class="mb-3">Serviço Ágil</h4>
              <p class="text-muted mb-0">
                Check-in/out expresso e apoio logístico 24/7
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção Sobre o Hotel -->
  <section id="about" class="py-5 bg-light">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-md-6">
          <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <!-- Primeira Imagem -->
              <div class="carousel-item active">
                <img src="uploads/Logo.png"
                  alt="Pousada simples em meio à natureza"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Segunda Imagem -->
              <div class="carousel-item">
                <img src="uploads/img1.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Quarta Imagem -->
              <div class="carousel-item">
                <img src="uploads/img3.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Quinta Imagem -->
              <div class="carousel-item">
                <img src="uploads/img4.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Sexta Imagem -->
              <div class="carousel-item">
                <img src="uploads/img5.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Sétima Imagem -->
              <div class="carousel-item">
                <img src="uploads/img6.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Oitava Imagem -->
              <div class="carousel-item">
                <img src="uploads/img7.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Nona Imagem -->
              <div class="carousel-item">
                <img src="uploads/img8.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Décima Imagem -->
              <div class="carousel-item">
                <img src="uploads/img9.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
              <!-- Décima Primeira Imagem -->
              <div class="carousel-item">
                <img src="uploads/img10.jpg"
                  alt="Quarto aconchegante"
                  class="d-block w-100 img-fluid rounded-3 shadow-sm">
              </div>
            </div>
            <!-- Controles do Carrossel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Próximo</span>
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <h2 class="h3 mb-4">Um Refúgio Essencial</h2>
          <div class="about-content">
            <p class="lead text-muted">
              Na Apê Pousada, entendemos que seu descanso é fundamental para um dia produtivo.
              Oferecemos o básico bem feito:
            </p>
            <ul class="list-unstyled">
              <li class="mb-3">
                <i class="fas fa-leaf text-success me-2"></i>
                <strong>Localização tranquila:</strong> Afastada do centro urbano, em área arborizada
              </li>
              <li class="mb-3">
                <i class="fas fa-bed text-success me-2"></i>
                <strong>Quartos funcionais:</strong> Camas confortáveis
              </li>
              <li class="mb-3">
                <i class="fas fa-coffee text-success me-2"></i>
                <strong>Café reforçado:</strong> Servido das 5h30 às 8h30 no refeitório
              </li>
              <li class="mb-3">
                <i class="fas fa-wifi text-success me-2"></i>
                <strong>Conexão prática:</strong> Wi-Fi estável nas áreas comuns
              </li>
            </ul>
            <p class="text-muted mb-0">
              Um lugar para recarregar as energias após um dia intenso de trabalho, sem luxos desnecessários, mas com tudo que realmente importa para seu descanso.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Reserva -->
  <section id="reservation" class="py-5 bg-success text-white">
    <div class="container text-center">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <h2 class="h1 mb-4">Pronto para Reservar?</h2>
          <p class="lead mb-5">Escolha suas datas e garanta seu quarto em menos de 2 minutos</p>
          <?php if ($logado): ?>
            <a href="./hospedes/reserva_quartos_cliente.php" class="btn btn-light btn-lg px-5">
              <i class="fas fa-bed text-success me-2"></i>Reservar Agora
            </a>
          <?php else: ?>
            <a href="#" class="btn btn-light btn-lg px-5" onclick="document.getElementById('loginModal').style.display='block'">
              <i class="fas fa-bed text-success me-2"></i>Reservar Agora
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Seção de Contato -->
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="h3 mb-3">Fale Conosco</h2>
        <p class="text-muted">Formas diretas de contato para sua comodidade</p>
      </div>

      <div class="row justify-content-center g-4">
        <!-- Telefone -->
        <a href="tel:+551799614-1426" class="col-md-4 text-center text-decoration-none text-dark">
          <div class="p-4 border rounded bg-white h-100">
            <i class="fas fa-phone-alt fs-4 text-success mb-3"></i>
            <h5 class="mb-3">Telefone Fixo</h5>
            <p class="text-muted mb-0">(17) 99614-1426</p>
          </div>
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/5517997658060" class="col-md-4 text-center text-decoration-none text-dark" target="_blank">
          <div class="p-4 border rounded bg-white h-100">
            <i class="fab fa-whatsapp fs-4 text-success mb-3"></i>
            <h5 class="mb-3">WhatsApp</h5>
            <p class="mb-0">(17) 99765-8060</p>
          </div>
        </a>

        <!-- E-mail -->
        <a href="mailto:ww.neto@hotmail.com" class="col-md-4 text-center text-decoration-none text-dark">
          <div class="p-4 border rounded bg-white h-100">
            <i class="fas fa-envelope fs-4 text-success mb-3"></i>
            <h5 class="mb-3">E-mail</h5>
            <p class="mb-0">ww.neto@hotmail.com</p>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Apê Pousada. Todos os direitos reservados.</p>
    </div>
  </footer>

  <script>
    function abrirEsqueciSenha() {
      document.getElementById('loginModal').style.display = 'none';
      document.getElementById('esqueciSenhaModal').style.display = 'block';
    }

    function fecharEsqueciSenha() {
      document.getElementById('esqueciSenhaModal').style.display = 'none';
    }

    // Fechar o modal de login ao clicar fora dele
    document.getElementById('loginModal').addEventListener('click', function(event) {
      if (event.target === this) {
        this.style.display = 'none';
      }
    });

    // Fechar o modal de esqueci a senha ao clicar fora dele
    document.getElementById('esqueciSenhaModal').addEventListener('click', function(event) {
      if (event.target === this) {
        this.style.display = 'none';
      }
    });

    let beds = {
      bunk: 0, // Beliche
      double: 0, // Cama de Casal
      single: 0 // Cama de Solteiro
    };

    const prices = {
      bunk: 80,
      double: 100,
      single: 50
    } // Valores exemplos

    function updateReserveButtonState() {
      // Soma o total de camas selecionadas
      const totalBeds = beds.bunk + beds.double + beds.single;

      // Habilita ou desabilita o botão com base no total de camas
      const reserveButton = document.getElementById('reserveButton');
      reserveButton.disabled = totalBeds === 0; // Desabilita se nenhuma cama for selecionada
    }

    function adjustBed(type, delta) {
      const newValue = beds[type] + delta;

      // Verificar limites
      if (newValue < 0) return;

      const totalPeople = (type === 'bunk' ? (newValue * 2) : (beds.bunk * 2)) +
        (type === 'double' ? (newValue * 2) : (beds.double * 2)) +
        (type === 'single' ? newValue : beds.single);

      if (totalPeople > 4) {
        alert('Capacidade máxima de 4 pessoas por quarto');
        return;
      }

      // Atualiza o valor do tipo de cama
      beds[type] = newValue;
      document.getElementById(`${type}Count`).textContent = newValue;

      // Atualiza o total de pessoas no span
      document.getElementById('totalPeople').textContent = totalPeople;

      // Atualiza o estado do botão
      updateReserveButtonState();
    }

    // Ativa e desativa os botões da navbar
    function ativarNavItem(event, id) {
      // Remove a classe 'active' de todos os itens
      let navItems = document.querySelectorAll(".nav-link");
      navItems.forEach(item => item.classList.remove("active"));

      // Adiciona a classe 'active' ao item clicado
      let navAtivo = document.getElementById(id);
      navAtivo.classList.add("active");

      // Obtém o destino do link
      let destino = navAtivo.getAttribute("href");

      if (destino === "#") {
        // Se for o link "Início", rola para o topo da página
        event.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: "smooth"
        });
      } else if (destino && destino.startsWith("#")) {
        // Para outras seções, faz a rolagem suave
        event.preventDefault();
        document.querySelector(destino).scrollIntoView({
          behavior: "smooth"
        });
      }
    }

    function validarSenhas() {
      const senha = document.getElementById('novaSenha').value;
      const confirmar = document.getElementById('confirmarSenha').value;
      const errorDiv = document.getElementById('senhaError');
      const submitBtn = document.getElementById('submitSenha');

      if (senha !== confirmar && confirmar !== '') {
        errorDiv.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
      } else {
        errorDiv.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
      }
    }

    // Abre o modal automaticamente se houver erro de login
    document.addEventListener('DOMContentLoaded', function() {
      updateReserveButtonState();
      const loginForm = document.querySelector('#loginModal form');

      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          e.preventDefault();

          // Mostrar loader ou desativar botão
          const submitBtn = loginForm.querySelector('button[type="submit"]');
          const originalBtnText = submitBtn.innerHTML;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
          submitBtn.disabled = true;

          // Coletar dados do formulário
          const formData = new FormData(loginForm);

          // Enviar via AJAX
          fetch('login.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Redirecionar se o login for bem-sucedido
                window.location.href = data.redirect;
              } else {
                // Mostrar mensagem de erro
                showLoginError(data.message);
              }
            })
            .catch(error => {
              showLoginError('Erro ao processar a requisição');
              console.error('Error:', error);
            })
            .finally(() => {
              // Restaurar botão
              submitBtn.innerHTML = originalBtnText;
              submitBtn.disabled = false;
            });
        });
      }

      function showLoginError(message) {
        // Remove mensagens de erro anteriores
        const existingError = document.querySelector('#loginModal .w3-panel.w3-red');
        if (existingError) {
          existingError.remove();
        }

        // Cria nova mensagem de erro
        const errorDiv = document.createElement('div');
        errorDiv.className = 'w3-panel w3-red w3-round-large w3-padding mt-3';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${message}`;

        // Insere após o botão de submit
        const submitBtn = document.querySelector('#loginModal button[type="submit"]');
        submitBtn.parentNode.insertBefore(errorDiv, submitBtn.nextSibling);

        // Rolagem suave para o erro
        errorDiv.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
      }
    });

    function redirectToReservation() {
      window.location.href = './hospedes/reserva_quartos_cliente.php';
    }
  </script>

  <!-- Bootstrap JS e dependências -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>