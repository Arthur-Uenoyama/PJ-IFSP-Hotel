<?php
include '../db/dbHotel.php';

$conn = $pdo ?? $GLOBALS['pdo'];

//print_r($_POST);die();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tabela = $_POST['tabela'] ?? '';

    if ($tabela === "reserva") {
        $resultado = processarReserva($conn, $_POST);
        
        if ($resultado['success']) {
            // Redireciona para a tela de consulta com mensagem de sucesso
            header("Location: ../consultas/consulta_reservas.php?success=1&message=" . urlencode($resultado['message']));
            exit;
        } else {
            // Volta para o formulário com mensagem de erro
            header("Location: ../cadastros/cadastro_reserva.php?error=1&message=" . urlencode($resultado['message']));
            exit;
        }
    }
}

/**
 * Obtém os IDs dos quartos com base nos números
 */
function obterIdsQuartos($conn, $numerosQuartos) {
    $numeros = is_array($numerosQuartos) ? $numerosQuartos : explode(',', $numerosQuartos);
    $placeholders = implode(',', array_fill(0, count($numeros), '?'));
    
    $stmt = $conn->prepare("
        SELECT id, numero, status 
        FROM quartos 
        WHERE numero IN ($placeholders)
    ");
    
    $stmt->execute($numeros);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Processa uma reserva completa com quarto principal e adicionais
 */
function processarReserva($conn, $dadosPost) {
    try {
        $conn->beginTransaction();
        
        // 1. Obter os quartos
        $quartoPrincipal = obterIdsQuartos($conn, $dadosPost['quartos_selecionados']);
        $quartosHospedes = !empty($dadosPost['quartos_hospedes']) ? 
            obterIdsQuartos($conn, $dadosPost['quartos_hospedes']) : [];
        
        // Verificar se o quarto principal foi encontrado
        if (empty($quartoPrincipal)) {
            throw new Exception("Quarto principal não encontrado");
        }
        
        // 2. Preparar dados básicos
        $clienteId = $dadosPost['cliente_id'];
        $documento = $dadosPost['documento'];
        $dataCheckin = $dadosPost['data_checkin'];
        $dataCheckout = $dadosPost['data_checkout'];
        $observacoes = $dadosPost['obs'] ?? null;
        $metodoPagamento = $dadosPost['pagamento'];
        $valorPago = str_replace(['R$', '.', ','], ['', '', '.'], $dadosPost['valor_pago']);
        
        // Calcular valor total (soma de todos os quartos selecionados)
        $valores = explode(',', str_replace(['R$', ' '], ['', ''], $dadosPost['valores_selecionados']));
        $valorTotal = array_sum($valores);

        // Por isto:
        $valorPago = (float)str_replace(['R$', '.', ','], ['', '', '.'], $valorPago);
        $valorTotal = (float)$valorTotal - $valorPago;
        
        // 3. Processar o quarto principal
        $quartoPrincipal = $quartoPrincipal[0];
        
        // Verificar disponibilidade
        if ($quartoPrincipal['status'] !== 'Disponível') {
            throw new Exception("Quarto principal {$quartoPrincipal['numero']} não está disponível");
        }
        
        // 4. Inserir reserva principal (única reserva na tabela)
        $stmtReserva = $conn->prepare("
            INSERT INTO reservas (
                usuario_id, quarto_id, cpf_cnpj, 
                data_checkin, hora_checkin, data_checkout, hora_checkout,
                tipo_camas, valor_reserva, tipo_pensao, forma_pagamento, 
                status, observacoes
            ) VALUES (
                :usuario_id, :quarto_id, :cpf_cnpj,
                :data_checkin, :hora_checkin, :data_checkout, :hora_checkout,
                :tipo_camas, :valor_reserva, :tipo_pensao, :forma_pagamento,
                :status, :observacoes
            )
        ");
        
        $stmtReserva->execute([
            ':usuario_id' => $clienteId,
            ':quarto_id' => $quartoPrincipal['id'],
            ':cpf_cnpj' => $documento,
            ':data_checkin' => $dataCheckin,
            ':hora_checkin' => '14:00:00',
            ':data_checkout' => $dataCheckout,
            ':hora_checkout' => '12:00:00',
            ':tipo_camas' => 'Solteiro',
            ':valor_reserva' => $valorTotal, // Valor total de todos os quartos
            ':tipo_pensao' => 'Café da Manhã',
            ':forma_pagamento' => $metodoPagamento,
            ':status' => 'Pendente',
            ':observacoes' => $observacoes
        ]);
        
        $reservaId = $conn->lastInsertId();
        
        // 5. Atualizar status do quarto principal
        //$conn->prepare("UPDATE quartos SET status = 'Ocupado' WHERE id = ?")->execute([$quartoPrincipal['id']]);
        
        // 6. Processar pagamento
        $stmtPagamento = $conn->prepare("
            INSERT INTO pagamentos (
                reserva_id, valor, metodo, status, data_pagamento
            ) VALUES (
                :reserva_id, :valor, :metodo, :status, NOW()
            )
        ");
        
        $stmtPagamento->execute([
            ':reserva_id' => $reservaId,
            ':valor' => $valorPago,
            ':metodo' => $metodoPagamento,
            ':status' => $valorPago > 0 ? 'aprovado' : 'pendente'
        ]);
        
        // 7. Processar hóspedes secundários (incluindo os quartos adicionais)
        if (!empty($dadosPost['hospedes'])) {
            foreach ($dadosPost['hospedes'] as $hospede) {
                // Encontrar o quarto correspondente
                $quartoHospede = array_filter($quartosHospedes, function($q) use ($hospede) {
                    return $q['numero'] == $hospede['quarto'];
                });
                
                if (!empty($quartoHospede)) {
                    $quartoHospede = array_shift($quartoHospede);
                    
                    // Inserir hóspede secundário
                    $stmtHospede = $conn->prepare("
                        INSERT INTO hospedes_secundarios (
                            reserva_id, nome, cpf_cnpj, quarto_id
                        ) VALUES (
                            :reserva_id, :nome, :cpf_cnpj, :quarto_id
                        )
                    ");
                    
                    $stmtHospede->execute([
                        ':reserva_id' => $reservaId,
                        ':nome' => $hospede['nome'],
                        ':cpf_cnpj' => $hospede['documento'],
                        ':quarto_id' => $quartoHospede['id']
                    ]);
                    
                    // Atualizar status do quarto do hóspede
                    //$conn->prepare("UPDATE quartos SET status = 'Ocupado' WHERE id = ?")->execute([$quartoHospede['id']]);
                }
            }
        }
        
        $conn->commit();
        return [
            'success' => true, 
            'message' => 'Reserva cadastrada com sucesso!'
        ];
        
    } catch (Exception $e) {
        $conn->rollBack();
        return [
            'success' => false, 
            'message' => 'Erro ao processar reserva: ' . $e->getMessage()
        ];
    }
}
?>
