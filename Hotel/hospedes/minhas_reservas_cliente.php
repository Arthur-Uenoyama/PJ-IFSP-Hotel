<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
include '.././db/dbHotel.php';
include '../components/avaliacao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioId = $_SESSION['id'];

$minhasReservas = [];

$sql = "SELECT 
            r.id AS ReservaId, 
            q.numero AS QuartoNumero, 
            u.nome_completo, 
            r.valor_reserva AS Preco, 
            r.data_checkin, 
            r.data_checkout, 
            r.status 
        FROM reservas r 
        LEFT JOIN quartos q ON r.quarto_id = q.id 
        LEFT JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.usuario_id = :usuarioId";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':usuarioId', $usuarioId);

    $stmt->execute();
    $minhasReservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log para debug
    error_log("Reservas encontradas: " . print_r($minhasReservas, true));

    // Fecha a sessão para evitar interferências
    session_write_close();
} catch (PDOException $e) {
    die("Erro ao buscar reservas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/minhas_reservas_cliente.css">
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <main class="flex-grow-1 mt-5 bg-light">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 fw-bold text-success">
                    <i class="fas fa-calendar-alt me-2"></i>Minhas Reservas
                </h1>
                <a href="./reserva_quartos_cliente.php" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nova Reserva
                </a>
            </div>

            <?php if (count($minhasReservas) > 0): ?>
                <div class="row g-4">
                    <?php foreach ($minhasReservas as $reserva): ?>
                        <?php
                        $statusClass = match (strtolower(trim($reserva['status']))) {
                            'confirmada' => 'badge-sucesso',
                            'pendente' => 'badge-pendente',
                            'em andamento' => 'badge-primario',
                            'finalizada' => 'badge-info',
                            'cancelada' => 'badge-perigo',
                            default => 'badge-secundario'
                        };

                        error_log("Status da reserva: " . $reserva['status']);
                        ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="reserva-card h-100">
                                <img src="../uploads/quarto-<?= htmlspecialchars($reserva['QuartoNumero']) ?>.jpg"
                                    class="card-img-top"
                                    alt="Quarto <?= htmlspecialchars($reserva['QuartoNumero']) ?>"
                                    onerror="this.onerror=null; this.src='../uploads/1740695620_teste.jpeg';">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h2 class="h5 mb-0">
                                            Quarto #<?= htmlspecialchars($reserva['QuartoNumero']) ?>
                                        </h2>
                                        <button
                                            class="status-badge text-white <?= $statusClass ?>"
                                            type="button"
                                            onclick="console.log('Status da reserva:', '<?= $reserva['status'] ?>'); gerenciarReserva('<?= $reserva['status'] ?>', <?= $reserva['ReservaId'] ?>)">
                                            <?= htmlspecialchars($reserva['status']) ?>
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-wallet info-icon"></i>
                                            <span class="fw-bold">Valor Total:</span>
                                            <span class="ms-auto">R$ <?= number_format($reserva['Preco'], 2, ',', '.') ?></span>
                                        </div>

                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-sign-in-alt info-icon"></i>
                                            <span>Check-in:</span>
                                            <span class="ms-auto"><?= date('d/m/Y', strtotime($reserva['data_checkin'])) ?></span>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-sign-out-alt info-icon"></i>
                                            <span>Check-out:</span>
                                            <span class="ms-auto"><?= date('d/m/Y', strtotime($reserva['data_checkout'])) ?></span>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <?= htmlspecialchars($reserva['nome_completo']) ?>
                                            <button class="btn btn-sm btn-outline-warning btn-icon ms-2"
                                            onclick="abrirModalAvaliacao(<?= $reserva['ReservaId'] ?>)">
                                            <i class="fas fa-star"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times empty-state-icon"></i>
                    <h2 class="h4 mb-3">Nenhuma reserva encontrada</h2>
                    <p class="text-muted mb-4">Parece que você ainda não fez nenhuma reserva.</p>
                    <a href="./reserva_quartos_cliente.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>Fazer Primeira Reserva
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function gerenciarReserva(status, reservaId) {
            if (status.toLowerCase() === 'pendente') {
                // Exibir mensagem de confirmação para confirmar a reserva
                if (confirm('Deseja confirmar esta reserva?')) {
                    // Fazer a requisição para confirmar a reserva
                    confirmarReserva(reservaId);
                }
            } else if (status.toLowerCase() === 'confirmada') {
                // Exibir mensagem para entrar em contato com o hotel
                alert('Para cancelar ou alterar esta reserva, entre em contato com o hotel.');
            } else {
                // Mensagem padrão para outros status
                console.log('Status da reserva:', status);
            }
        }

        function confirmarReserva(reservaId) {
            // Fazer uma requisição AJAX para confirmar a reserva
            fetch(`../hospedes/confirmar_reserva.php?id=${reservaId}`, {
                    method: 'POST',
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição ao servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Reserva confirmada com sucesso!');
                        location.reload(); // Recarregar a página para atualizar o status
                    } else {
                        alert(data.message || 'Erro ao confirmar a reserva. Tente novamente.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao confirmar a reserva. Tente novamente.');
                });
        }
    </script>
</body>

</html>