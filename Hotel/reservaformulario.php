<?php
include('dbHotel.php');
session_start();

$message = '';

// Verifica se o usuário está logado
$usuario_logado = isset($_SESSION['usuario_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $room_type = $_POST['room_type'];

    // Verifica se o usuário existe pelo e-mail
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $usuario_id = $user['id'];

        // Insere a reserva no banco de dados
        $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, data_checkin, data_checkout, room_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $check_in, $check_out, $room_type]);

        $message = '<div class="alert alert-success text-center" role="alert">Reserva realizada com sucesso!</div>';
    } else {
        $message = '<div class="alert alert-danger text-center" role="alert">Erro: Usuário não encontrado. Cadastre-se antes de fazer a reserva.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Reserva de Hotel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Início</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="reservaformulario.php">Reservar</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">Contato</a></li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="perfil.php">Perfil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Mensagem de sucesso ou erro -->
<div class="container">
    <?php if ($message): ?>
        <div class="col-md-8 mx-auto">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de Reserva -->
    <h2 class="text-center mt-4">Faça sua Reserva</h2>
    <form action="reservaformulario.php" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" class="form-control" name="name" id="name" required>
            <div class="invalid-feedback">Por favor, insira seu nome.</div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Por favor, insira um email válido.</div>
        </div>
        <div class="mb-3">
            <label for="check_in" class="form-label">Data de Check-in:</label>
            <input type="date" class="form-control" name="check_in" id="check_in" required>
        </div>
        <div class="mb-3">
            <label for="check_out" class="form-label">Data de Check-out:</label>
            <input type="date" class="form-control" name="check_out" id="check_out" required>
        </div>
        <div class="mb-3">
            <label for="room_type" class="form-label">Tipo de Quarto:</label>
            <select class="form-select" name="room_type" id="room_type">
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="suite">Suite</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Reservar</button>
    </form>
</div>

<!-- Footer fixado na parte inferior -->
<footer class="w3-red text-white text-center py-3">
    <p>&copy; 2025 Hotel. Todos os direitos reservados.</p>
    <p><a href="mailto:contato@hotel.com" class="text-white">contato@hotel.com</a></p>
</footer>

<!-- Bootstrap JS e validação do formulário -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

</body>
</html>