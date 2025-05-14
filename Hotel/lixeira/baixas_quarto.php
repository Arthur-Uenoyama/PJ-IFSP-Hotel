<?php
session_start();
include './db/dbHotel.php';

if (!isset($_SESSION['usuarioId'])) {
    header("Location: ../index.php");
    exit;
}

$quartoIds = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if ($quartoIds) {
    $idsArray = explode(',', $quartoIds);
    $idsArray = array_filter($idsArray, 'is_numeric'); // Filtra apenas valores numéricos

    if (!empty($idsArray)) {
        try {
            // Criar placeholders dinâmicos para múltiplos IDs
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            
            if ($acao === 'manutencao') {
                $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Manutenção' WHERE id IN ($placeholders)");
            } elseif ($acao === 'ocupado') {
                $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Ocupado' WHERE id IN ($placeholders)");
            } else {
                $stmt = $pdo->prepare("UPDATE Quartos SET status = 'Disponível' WHERE id IN ($placeholders)");
            }

            $stmt->execute($idsArray);

            if ($stmt->rowCount() > 0) {
                header("Location: home.php");
                exit;
            } else {
                echo "Nenhuma alteração foi realizada.";
            }

        } catch (PDOException $e) {
            echo "Erro ao atualizar status do quarto: " . $e->getMessage();
        }
    } else {
        echo "Nenhum ID válido recebido.";
        exit;
    }
} else {
    echo "ID do quarto inválido.";
    exit;
}
?>
