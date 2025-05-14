<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado (descomente se necessário)
/*
if (!isset($_SESSION['usuarioId']) || $_SESSION['usuarioTipo'] !== 'hotel') {
    header("Location: ../index.php");
    exit;
}
*/

$usuarioId = $_SESSION['usuarioId'] ?? null;
$usuarioTipo = $_SESSION['usuarioTipo'] ?? null; // 'cliente' ou 'hotel'
?>

<!-- Importação do Google Fonts para ícones -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
    /* Estilização da sidebar */
    #mySidebar {
        width: 250px;
        position: fixed;
        left: -250px;
        top: 0;
        height: 100%;
        background: #343a40;
        color: white;
        transition: 0.3s;
        padding-top: 20px;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
    }
    #mySidebar a {
        padding: 12px 20px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        transition: 0.2s;
    }
    #mySidebar a:hover {
        background: #495057;
    }
    .sidebar-open {
        left: 0 !important;
    }
    .close-btn {
        text-align: right;
        padding: 10px;
    }
    .close-btn button {
        background: none;
        border: none;
        color: white;
        font-size: 22px;
        cursor: pointer;
    }
    /* Ajustes da navbar */
    .navbar {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .navbar-brand {
        font-weight: bold;
        font-size: 20px;
    }
    .navbar .btn-outline-light {
        font-size: 20px;
        border: none;
        transition: 0.2s;
    }
    .navbar .btn-outline-light:hover {
        color: #ffc107;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Apê Pousada</a>

        <!-- Botão para abrir a sidebar -->
        <button class="btn btn-outline-light me-2" onclick="toggleSidebar()">☰</button>

        <!-- Botão responsivo -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#perfilModal">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div id="mySidebar">
    <div class="close-btn">
        <button onclick="toggleSidebar()">
            <span class="material-symbols-outlined">arrow_back_ios</span>
        </button>
    </div>
    
    <?php if ($usuarioTipo === 'cliente'): ?>
        <a href="reserva_quartos_cliente.php"><i class="fas fa-bed"></i> Fazer Reserva</a>
    <?php else: ?>
        <a href="consulta_quartos.php"><i class="fas fa-plus-square"></i> Gerenciar Quartos</a>
        <a href="home.php"><i class="fas fa-plus-square"></i> Gerenciar Reservas</a>
        <a href="consulta_funcionarios.php"><i class="fas fa-users"></i> Gerenciar Funcionários</a>
        <a href="baixas_pagamento.php"><i class="fas fa-money-check-alt"></i> Baixas de Pagamento</a>
    <?php endif; ?>
</div>

<script>
    function toggleSidebar() {
        let sidebar = document.getElementById("mySidebar");
        if (sidebar.classList.contains("sidebar-open")) {
            sidebar.classList.remove("sidebar-open");
        } else {
            sidebar.classList.add("sidebar-open");
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
