<?php 

include './db/dbHotel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuarioId'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Buscar informações do usuário
try {
    if ($usuarioTipo === 'cliente') {
        $stmt = $pdo->prepare("SELECT id, nome_completo AS Nome, email FROM clientes WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, nome AS Nome, email FROM funcionarios WHERE id = ?");
    }
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar usuário: " . $e->getMessage();
    exit;
}

$quartos = [];
$termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

if ($usuarioTipo === 'hotel') {
    // Ajustar consulta de quartos para refletir a estrutura correta do banco
    $sql = "SELECT q.id AS QuartoId, q.numero, q.preco, q.status, 
                   r.data_checkin, r.data_checkout, c.nome_completo AS ClienteNome
            FROM quartos q
            LEFT JOIN reservas r ON q.id = r.quarto_id
            LEFT JOIN clientes c ON r.usuario_id = c.id";

    if (!empty($termo)) {
        $sql .= " WHERE c.nome_completo LIKE :termo";
    }

    try {
        $stmt = $pdo->prepare($sql);
        if (!empty($termo)) {
            $stmt->bindValue(':termo', "%$termo%");
        }
        $stmt->execute();
        $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
}

?>
