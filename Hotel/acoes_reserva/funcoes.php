<?php
session_start();
include '../db/dbHotel.php';

$conn = $pdo ?? $GLOBALS['pdo'];

//echo"<pre>";print_r($_POST);die();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // 1. Process and validate input data
        $allowedStatuses = ['pendente', 'confirmada', 'em andamento', 'finalizada', 'cancelada'];
        $status = in_array($_POST['status'], $allowedStatuses) ? $_POST['status'] : 'pendente';
        
        $reservationData = [
            'user_id' => filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT),
            'reservation_id' => filter_input(INPUT_POST, 'reserva', FILTER_VALIDATE_INT),
            'document' => preg_replace('/[^0-9]/', '', $_POST['documento']),
            'room_number' => filter_input(INPUT_POST, 'numero_quarto', FILTER_SANITIZE_STRING),
            'bed_type' => filter_input(INPUT_POST, 'tipo_cama', FILTER_SANITIZE_STRING),
            'total_value' => (float)str_replace(['.', ','], ['', '.'], $_POST['valorTotal']),
            'paid_value' => (float)str_replace(['.', ','], ['', '.'], $_POST['valorPago']),
            'remaining_value' => (float)str_replace(['.', ','], ['', '.'], $_POST['valorRestante']),
            'status' => $status,
            'notes' => filter_input(INPUT_POST, 'obs', FILTER_SANITIZE_STRING)
        ];

        // Validate required fields
        if (!$reservationData['user_id'] || $reservationData['user_id'] <= 0) {
            throw new Exception("ID de usuário inválido");
        }
        
        if (!$reservationData['reservation_id'] || $reservationData['reservation_id'] <= 0) {
            throw new Exception("ID de reserva inválido");
        }

        // 2. Get room information
        $roomStmt = $conn->prepare("SELECT id FROM quartos WHERE numero = ?");
        $roomStmt->execute([$reservationData['room_number']]);
        $room = $roomStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            throw new Exception("Quarto não encontrado");
        }
        $reservationData['room_id'] = $room['id'];

        // 3. Update main reservation - REMOVED 'atualizado_em' column
        $updateReservation = $conn->prepare("
            UPDATE reservas 
            SET 
                usuario_id = :user_id,
                quarto_id = :room_id,
                cpf_cnpj = :document,
                tipo_camas = :bed_type,
                valor_reserva = :total_value,
                status = :status,
                observacoes = :notes
            WHERE 
                id = :reservation_id
        ");
        
        $updateResult = $updateReservation->execute([
            ':user_id' => $reservationData['user_id'],
            ':room_id' => $reservationData['room_id'],
            ':document' => $reservationData['document'],
            ':bed_type' => $reservationData['bed_type'],
            ':total_value' => $reservationData['total_value'],
            ':status' => $reservationData['status'],
            ':notes' => $reservationData['notes'],
            ':reservation_id' => $reservationData['reservation_id']
        ]);

        if (!$updateResult) {
            $errorInfo = $updateReservation->errorInfo();
            throw new Exception("Falha ao atualizar reserva: {$errorInfo[2]}");
        }

        // 4. Process payments if any amount was paid
        if ($reservationData['paid_value'] > 0) {
            $paymentStmt = $conn->prepare("
                INSERT INTO pagamentos 
                (reserva_id, valor, metodo, status, data_pagamento)
                VALUES (:reservation_id, :amount, 'pix', 'aprovado', NOW())
                ON DUPLICATE KEY UPDATE 
                valor = valor + :amount, 
                data_pagamento = NOW()
            ");
            
            $paymentStmt->execute([
                ':reservation_id' => $reservationData['reservation_id'],
                ':amount' => $reservationData['paid_value']
            ]);
        }

        // 5. Handle secondary guests
// 5. Handle secondary guests
if (!empty($_POST['hospedes_secundarios'])) {
    // Clear existing guests
    $deleteGuests = $conn->prepare("DELETE FROM hospedes_secundarios WHERE reserva_id = ?");
    $deleteGuests->execute([$reservationData['reservation_id']]);
    
    // Prepare statements
    $addGuest = $conn->prepare("
        INSERT INTO hospedes_secundarios 
        (reserva_id, nome, cpf_cnpj, quarto_id) 
        VALUES (:reservation_id, :name, :document, :room_id)
    ");
    
    $getRoomId = $conn->prepare("SELECT id FROM quartos WHERE numero = ? LIMIT 1");
    
    foreach ($_POST['hospedes_secundarios'] as $guest) {
        if (empty($guest['nome']) || empty($guest['documento'])) {
            continue;
        }
        
        // Sanitize data
        $guestName = filter_var($guest['nome'], FILTER_SANITIZE_STRING);
        $guestDoc = preg_replace('/[^0-9]/', '', $guest['documento']);
        
        // Determine room ID for this guest
        if (!empty($guest['quarto_id']) && is_numeric($guest['quarto_id'])) {
            // Se quarto_id já foi fornecido diretamente
            $guestRoomId = (int)$guest['quarto_id'];
        } elseif (!empty($guest['numero_quarto'])) {
            // Se número do quarto foi fornecido, buscar o ID
            $getRoomId->execute([$guest['numero_quarto']]);
            $room = $getRoomId->fetch(PDO::FETCH_ASSOC);
            $guestRoomId = $room ? $room['id'] : $reservationData['room_id'];
        } else {
            // Default to main room if no specific room provided
            $guestRoomId = $reservationData['room_id'];
        }
        
        // Insert guest with their specific room_id
        $addGuest->execute([
            ':reservation_id' => $reservationData['reservation_id'],
            ':name' => $guestName,
            ':document' => $guestDoc,
            ':room_id' => $guestRoomId
        ]);
    }
}

        // 6. Update room status if needed
        if (in_array($status, ['confirmada', 'em andamento'])) {
            $updateRoom = $conn->prepare("UPDATE quartos SET status = 'Ocupado' WHERE id = ?");
            $updateRoom->execute([$reservationData['room_id']]);
        }

        $conn->commit();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Reserva atualizada com sucesso!'];
        header("Location: ../consultas/consulta_reservas.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao atualizar reserva: ' . $e->getMessage()];
        //header("Location: " . $_SERVER['HTTP_REFERER']);
        //exit();
        print_r($_SESSION['message']);die();
    }
} else {
    header("Location: index.php");
    exit();
}
