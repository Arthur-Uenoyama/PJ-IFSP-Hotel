<?php
session_start();
require_once __DIR__ . '/../db/dbHotel.php';

header('Content-Type: application/json; charset=utf-8');

$conn = $pdo ?? $GLOBALS['pdo'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método não permitido", 405);
    }

    // Validar parâmetros
    $requiredParams = ['checkin', 'checkout'];
    foreach ($requiredParams as $param) {
        if (!isset($_GET[$param])) {
            throw new Exception("Parâmetro '$param' é obrigatório", 400);
        }
    }

    $checkin = $_GET['checkin'];
    $checkout = $_GET['checkout'];
    
    $sql = "SELECT numero, preco FROM quartos 
            WHERE status = 'Disponível'
            AND NOT EXISTS (
                SELECT 1 FROM reservas 
                WHERE quarto_id = quartos.id
                AND data_checkin < ? 
                AND data_checkout > ?
            )";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$checkout, $checkin]);
    
    $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'quartos' => $quartos
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
