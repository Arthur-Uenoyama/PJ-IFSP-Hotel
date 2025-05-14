<?php
session_start();
require_once '.././db/dbHotel.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    $_SESSION['erro'] = "Você precisa estar logado para fazer uma reserva";
    header("Location: ../login.php");
    exit;
}

// Sanitizar e validar os dados de entrada
$quarto_id = filter_input(INPUT_POST, 'quarto_id', FILTER_SANITIZE_NUMBER_INT);
$check_in = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
$check_out = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
$pagamento = filter_input(INPUT_POST, 'pagamento', FILTER_SANITIZE_STRING);
$observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_STRING);
$parcelas = filter_input(INPUT_POST, 'parcelas', FILTER_SANITIZE_NUMBER_INT);

// Validar dados obrigatórios
if (empty($quarto_id) || empty($check_in) || empty($check_out) || empty($pagamento)) {
    $_SESSION['erro'] = "Preencha todos os campos obrigatórios";
    header("Location: reserva_quartos_cliente.php");
    exit;
}

// Validar datas
if (strtotime($check_out) <= strtotime($check_in)) {
    $_SESSION['erro'] = "Data de check-out deve ser posterior ao check-in";
    header("Location: reserva_quartos_cliente.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Verificar disponibilidade do quarto
    $stmtDisponibilidade = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                                        WHERE quarto_id = ? 
                                        AND status NOT IN ('cancelada', 'finalizada')
                                        AND (
                                            (data_checkin <= ? AND data_checkout >= ?) OR
                                            (data_checkin <= ? AND data_checkout >= ?) OR
                                            (data_checkin >= ? AND data_checkout <= ?)
                                        )");
    $stmtDisponibilidade->execute([
        $quarto_id,
        $check_out,
        $check_in,
        $check_in,
        $check_out,
        $check_in,
        $check_out
    ]);

    if ($stmtDisponibilidade->fetchColumn() > 0) {
        throw new Exception("Quarto não disponível no período selecionado");
    }

    // 2. Obter informações do quarto
    $stmtQuarto = $pdo->prepare("SELECT preco, numero, camas_solteiro, beliches, camas_casal FROM quartos WHERE id = ?");
    $stmtQuarto->execute([$quarto_id]);
    $quarto = $stmtQuarto->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        throw new Exception("Quarto não encontrado");
    }

    // 3. Determinar o tipo de cama
    $tipo_cama = 'Casal'; // padrão
    if ($quarto['camas_solteiro'] > 0) {
        $tipo_cama = 'Solteiro';
    } elseif ($quarto['beliches'] > 0) {
        $tipo_cama = 'Beliche';
    }

    // 4. Calcular valor total da reserva
    $dias = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $valor_total = $quarto['preco'] * $dias;

    // Aplicar desconto para PIX (5%)
    if ($pagamento === 'pix') {
        $valor_total = $valor_total * 0.95;
    }

    // 5. Obter informações do usuário
    $stmtUsuario = $pdo->prepare("SELECT nome_completo, cpf_cnpj FROM usuarios WHERE id = ?");
    $stmtUsuario->execute([$_SESSION['id']]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuário não encontrado");
    }

    // 6. Inserir a reserva
    $stmtReserva = $pdo->prepare("INSERT INTO reservas 
                                (usuario_id, quarto_id, cpf_cnpj, data_checkin, hora_checkin, 
                                data_checkout, hora_checkout, tipo_camas, valor_reserva, 
                                forma_pagamento, status, observacoes)
                                VALUES (?, ?, ?, ?, '14:00:00', ?, '12:00:00', ?, ?, ?, 'pendente', ?)");
    
    $stmtReserva->execute([
        $_SESSION['id'],
        $quarto_id,
        $usuario['cpf_cnpj'],
        $check_in,
        $check_out,
        $tipo_cama,
        $valor_total,
        $pagamento,
        $observacoes
    ]);

    $reserva_id = $pdo->lastInsertId();

    // 8. Registrar o pagamento
    $stmtPagamento = $pdo->prepare("INSERT INTO pagamentos 
                                  (reserva_id, valor, metodo, status)
                                  VALUES (?, ?, ?, 'pendente')");
    $stmtPagamento->execute([
        $reserva_id,
        $valor_total,
        $pagamento
    ]);

    $pdo->commit();

    $_SESSION['sucesso'] = "Reserva realizada com sucesso! Número: " . $quarto['numero'];
    header("Location: minhas_reservas_cliente.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro'] = "Erro ao realizar reserva: " . $e->getMessage();
    header("Location: reserva_quartos_cliente.php");
    exit;
}
?>
