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
                    header("Location: reservaformulario.php");
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
    <title>Hotel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        html, body { height: 100vh; display: flex; flex-direction: column; }
        .w3-red { background-color: red !important; }
        .wrapper { flex: 1; }
        .container { max-width: 600px; margin-top: 50px; }
        footer { margin-top: auto; }
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
                <li class="nav-item"><a class="nav-link text-white" href="#">Início</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="reservaformulario.php">Reservar</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Contato</a></li>
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

<div class="wrapper">
    <div class="container mt-5">
        <h1 class="text-center">Bem-vindo ao Hotel</h1>
        <p class="text-center">Reserve seu quarto de forma simples e rápida.</p>
        <a href="reservaformulario.php" class="btn btn-primary d-block mx-auto">Faça sua Reserva</a>
    </div>
</div>

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

<!-- Footer fixado na parte inferior -->
<footer class="w3-red text-white text-center py-3">
    <p>&copy; 2025 Hotel. Todos os direitos reservados.</p>
    <p><a href="mailto:contato@hotel.com" class="text-white">contato@hotel.com</a></p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
function abrirCadastro() {
    document.getElementById('loginModal').style.display='none';
    document.getElementById('cadastroModal').style.display='block';
}
</script>

</body>
</html>
