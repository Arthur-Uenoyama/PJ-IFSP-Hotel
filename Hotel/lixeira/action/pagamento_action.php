<?php
include './db/dbHotel.php';
include 'navbar_action.php';
include 'index_action.php';

// Buscar pagamentos pendentes
$pagamentos = [];
try {
    $pesquisa = $_GET['pesquisa'] ?? '';
    $pension = $_GET['pension'] ?? '';
    $data_inicio = $_GET['data_inicio'] ?? '';
    $data_fim = $_GET['data_fim'] ?? '';

    $query = "SELECT p.id, p.valor, p.status, u.nome AS cliente, r.data_checkin, r.data_checkout 
              FROM pagamentos p
              JOIN reservas r ON p.reserva_id = r.id
              JOIN clientes u ON r.usuario_id = u.id
              WHERE p.status = 'pendente'";

    if ($pesquisa) {
        $query .= " AND u.nome LIKE :pesquisa";
    }

    if ($pension) {
        $query .= " AND r.tipo_pensao = :pension";
    }

    if ($data_inicio && $data_fim) {
        $query .= " AND r.data_checkin BETWEEN :data_inicio AND :data_fim";
    }

    $stmt = $pdo->prepare($query);

    if ($pesquisa) {
        $stmt->bindValue(':pesquisa', '%' . $pesquisa . '%');
    }

    if ($pension) {
        $stmt->bindValue(':pension', $pension);
    }

    if ($data_inicio && $data_fim) {
        $stmt->bindValue(':data_inicio', $data_inicio);
        $stmt->bindValue(':data_fim', $data_fim);
    }

    $stmt->execute();
    $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar pagamentos: " . $e->getMessage());
}

// Marcar pagamento como aprovado
if (isset($_POST['pagamento_id'])) {
    try {
        $pagamentoId = $_POST['pagamento_id'];
        $stmt = $pdo->prepare("UPDATE pagamentos SET status = 'aprovado' WHERE id = ?");
        $stmt->execute([$pagamentoId]);
        header("Location: pagamentos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar pagamento: " . $e->getMessage());
    }
}

?>
