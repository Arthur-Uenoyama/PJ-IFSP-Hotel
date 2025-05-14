<?php
header('Content-Type: application/json');

// Configuração da conexão com o banco de dados
require_once __DIR__ . '/../db/dbHotel.php';

$termo = $_GET['termo'] ?? '';

$conn = $pdo ?? $GLOBALS['pdo'];

try {
    // Prepara a consulta SQL
    $stmt = $conn->prepare("SELECT id, nome_completo, cpf_cnpj FROM usuarios 
                          WHERE cpf_cnpj LIKE :termo OR nome_completo LIKE :termo
                          LIMIT 10");
    
    // Adiciona wildcards ao termo de busca
    $termoBusca = "%$termo%";
    $stmt->bindParam(':termo', $termoBusca);
    $stmt->execute();
    
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //print_r($clientes);die();
    
    echo json_encode($clientes);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
