<?php
session_start();
include 'dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: login.php");
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Hotel</a>
            <div class="d-flex">
                <?php if ($usuarioTipo === 'cliente'): ?>
                    <a class="btn btn-outline-light" href="reservaformulario.php">Fazer Reserva</a>
                <?php else: ?>
                    <a class="btn btn-outline-light" href="gerenciar_quartos.php">Gerenciar Quartos</a>
                <?php endif; ?>
                <a class="btn btn-outline-light ms-2" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h3>Bem-vindo, <?php echo $usuario['Nome']; ?></h3>
        <p>Email: <?php echo $usuario['Email']; ?></p>
        <p>Perfil: <?php echo $usuarioTipo === 'cliente' ? 'Cliente' : 'Administrador do Hotel'; ?></p>

        <h4>Reservas</h4>
        <?php if (count($reservas) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Quarto</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <?php if ($usuarioTipo === 'hotel'): ?><th>Cliente</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo $reserva['Quarto']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckIn'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['DataCheckOut'])); ?></td>
                            <?php if ($usuarioTipo === 'hotel'): ?><td><?php echo $reserva['Cliente']; ?></td><?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma reserva encontrada.</p>
        <?php endif; ?>
    </div>
</body>
</html>
