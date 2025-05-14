<?php
require_once __DIR__ . '/../db/dbHotel.php';

// Limpa qualquer saída anterior
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['termo']) || strlen($_GET['termo']) < 3) {
        echo json_encode([]);
        exit;
    }

    $termo = '%' . $_GET['termo'] . '%';
    
    $stmt = $pdo->prepare("
        SELECT id, nome_completo, cpf_cnpj 
        FROM usuarios 
        WHERE nome_completo LIKE :termo OR cpf_cnpj LIKE :termo
        LIMIT 10
    ");
    $stmt->bindParam(':termo', $termo);
    $stmt->execute();
    
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verifica se há resultados
    if (empty($clientes)) {
        echo json_encode([]);
    } else {
        echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    // Log do erro para debug
    error_log('Erro na busca de clientes: ' . $e->getMessage());
    
    // Retorna um JSON válido mesmo em caso de erro
    echo json_encode(['error' => 'Ocorreu um erro na busca']);
}

exit; // Garante que nada mais será enviado
?>
