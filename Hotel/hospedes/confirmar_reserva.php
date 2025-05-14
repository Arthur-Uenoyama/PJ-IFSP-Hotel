<?php
// filepath: d:\XAMPP\htdocs\StayEase-Solutionsv2\Hotel\acoes_reserva\confirmar_reserva.php
session_start();
include '../db/dbHotel.php';

// Verifica se o método da requisição é POST e se o ID da reserva foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $reservaId = intval($_GET['id']);

    try {
        // Atualizar o status da reserva para "confirmada" apenas se estiver "pendente"
        $stmt = $pdo->prepare("UPDATE reservas SET status = 'confirmada' WHERE id = :id AND status = 'pendente'");
        $stmt->bindValue(':id', $reservaId, PDO::PARAM_INT);
        $stmt->execute();

        // Verifica se a atualização foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            // Retorna um JSON de sucesso
            echo json_encode(['success' => true, 'message' => 'Reserva confirmada com sucesso!']);
        } else {
            // Retorna um JSON de erro caso a reserva já esteja confirmada ou não exista
            echo json_encode(['success' => false, 'message' => 'Erro: Reserva não encontrada ou já confirmada.']);
        }
    } catch (PDOException $e) {
        // Retorna um JSON de erro em caso de falha no banco de dados
        echo json_encode(['success' => false, 'message' => 'Erro ao confirmar a reserva: ' . $e->getMessage()]);
    }
} else {
    // Retorna um JSON de erro caso a requisição seja inválida
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
