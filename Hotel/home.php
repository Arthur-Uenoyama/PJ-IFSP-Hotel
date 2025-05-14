<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

$tipo = $_SESSION['tipo'];

// Incluir o arquivo de conexão com o banco de dados
include("./db/dbHotel.php");
$conn = $pdo ?? $GLOBALS['pdo'];

// Consultas usando PDO
// Total de hóspedes
$queryHospedes = "SELECT COUNT(*) AS total FROM usuarios";
$resultHospedes = $pdo->query($queryHospedes);
$totalHospedes = $resultHospedes->fetch(PDO::FETCH_ASSOC)['total'];

// Total de reservas
$queryReservas = "SELECT COUNT(*) AS total FROM reservas";
$resultReservas = $pdo->query($queryReservas);
$totalReservas = $resultReservas->fetch(PDO::FETCH_ASSOC)['total'];

// Total de quartos disponíveis
$queryQuartosDisponiveis = "SELECT COUNT(*) AS total FROM quartos WHERE status = 'Disponível'";
$resultQuartosDisponiveis = $pdo->query($queryQuartosDisponiveis);
$totalQuartosDisponiveis = $resultQuartosDisponiveis->fetch(PDO::FETCH_ASSOC)['total'];

// Total de funcionários
$queryFuncionarios = "SELECT COUNT(*) AS total FROM funcionarios";
$resultFuncionarios = $pdo->query($queryFuncionarios);
$totalFuncionarios = $resultFuncionarios->fetch(PDO::FETCH_ASSOC)['total'];

// Total de pedidos de serviço de quarto
$queryPedidosServico = "SELECT COUNT(*) AS total FROM pedidos_servico_quarto";
$resultPedidosServico = $pdo->query($queryPedidosServico);
$totalPedidosServico = $resultPedidosServico->fetch(PDO::FETCH_ASSOC)['total'];
// Consultas de reservas no mês atual
$queryReservasMes = "SELECT COUNT(*) AS total FROM reservas WHERE MONTH(data_checkin) = MONTH(CURRENT_DATE)";
$resultReservasMes = $pdo->query($queryReservasMes);
$reservasMes = $resultReservasMes->fetch(PDO::FETCH_ASSOC)['total'];
// Consultas de reservas confirmadas
$queryReservasConfirmadas = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Confirmada'";
$resultReservasConfirmadas = $pdo->query($queryReservasConfirmadas);
$reservasConfirmadas = $resultReservasConfirmadas->fetch(PDO::FETCH_ASSOC)['total'];
// Consultas de reservas aguardando confirmação
$queryReservasAguardando = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Aguardando'";
$resultReservasAguardando = $pdo->query($queryReservasAguardando);
$reservasAguardando = $resultReservasAguardando->fetch(PDO::FETCH_ASSOC)['total'];
// Consultas de reservas canceladas
$queryReservasCanceladas = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Cancelada'";
$resultReservasCanceladas = $pdo->query($queryReservasCanceladas);
$reservasCanceladas = $resultReservasCanceladas->fetch(PDO::FETCH_ASSOC)['total'];
// Consultas de quartos ocupados
$queryOcupados = "SELECT COUNT(*) AS total FROM quartos WHERE status = 'Ocupado'";
$resultOcupados = $pdo->query($queryOcupados);
$totalOcupados = $resultOcupados->fetch(PDO::FETCH_ASSOC)['total'];
// Total de reservas
$queryReservas = "SELECT COUNT(*) AS total FROM reservas";
$resultReservas = $pdo->query($queryReservas);
$totalReservas = $resultReservas->fetch(PDO::FETCH_ASSOC)['total'];

// Total de reservas no mês atual
$queryReservasMes = "SELECT COUNT(*) AS total FROM reservas WHERE MONTH(data_checkin) = MONTH(CURRENT_DATE)";
$resultReservasMes = $pdo->query($queryReservasMes);
$reservasMes = $resultReservasMes->fetch(PDO::FETCH_ASSOC)['total'];

// Total de reservas confirmadas
$queryReservasConfirmadas = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Confirmada'";
$resultReservasConfirmadas = $pdo->query($queryReservasConfirmadas);
$reservasConfirmadas = $resultReservasConfirmadas->fetch(PDO::FETCH_ASSOC)['total'];

// Total de reservas aguardando confirmação
$queryReservasAguardando = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Aguardando'";
$resultReservasAguardando = $pdo->query($queryReservasAguardando);
$reservasAguardando = $resultReservasAguardando->fetch(PDO::FETCH_ASSOC)['total'];

// Total de reservas canceladas
$queryReservasCanceladas = "SELECT COUNT(*) AS total FROM reservas WHERE status = 'Cancelada'";
$resultReservasCanceladas = $pdo->query($queryReservasCanceladas);
$reservasCanceladas = $resultReservasCanceladas->fetch(PDO::FETCH_ASSOC)['total'];

// Total de quartos disponíveis
$queryQuartosDisponiveis = "SELECT COUNT(*) AS total FROM quartos WHERE status = 'Disponível'";
$resultQuartosDisponiveis = $pdo->query($queryQuartosDisponiveis);
$totalQuartosDisponiveis = $resultQuartosDisponiveis->fetch(PDO::FETCH_ASSOC)['total'];

// Total de quartos ocupados
$queryOcupados = "SELECT COUNT(*) AS total FROM quartos WHERE status = 'Ocupado'";
$resultOcupados = $pdo->query($queryOcupados);
$totalOcupados = $resultOcupados->fetch(PDO::FETCH_ASSOC)['total'];

// Total de quartos
$queryTotalQuartos = "SELECT COUNT(*) AS total FROM quartos";
$resultTotalQuartos = $pdo->query($queryTotalQuartos);
$totalQuartos = $resultTotalQuartos->fetch(PDO::FETCH_ASSOC)['total'];
// Quartos reservados no próximo mês
$queryQuartosProximoMes = "SELECT COUNT(*) AS total FROM reservas WHERE MONTH(data_checkin) = MONTH(CURRENT_DATE + INTERVAL 1 MONTH)";
$resultQuartosProximoMes = $pdo->query($queryQuartosProximoMes);
$quartosProximoMes = $resultQuartosProximoMes->fetch(PDO::FETCH_ASSOC)['total'];
$queryMediaAvaliacao = "SELECT IFNULL(TRUNCATE(AVG(nota), 1), 0) AS media FROM avaliacoes";
$stmt = $pdo->prepare($queryMediaAvaliacao);
$stmt->execute();
$mediaAvaliacao = $stmt->fetchColumn();
$queryClientesCadastrados = "SELECT COUNT(*) AS total FROM usuarios";
$stmt = $pdo->query($queryClientesCadastrados);
$clientesCadastrados = $stmt->fetchColumn();
$queryServicosPendentes = "SELECT COUNT(*) AS total FROM pedidos_servico_quarto WHERE status = 'pendente'";
$stmt = $pdo->query($queryServicosPendentes);
$servicosPendentes = $stmt->fetchColumn();

$queryRecebidoMes = "SELECT SUM(valor) FROM pagamentos WHERE MONTH(data_pagamento) = MONTH(CURDATE()) AND status = 'aprovado'";
$stmt = $pdo->query($queryRecebidoMes);
$recebidoMes = $stmt->fetchColumn();

$queryPendentes = "SELECT SUM(valor) FROM pagamentos WHERE status = 'pendente'";
$stmt = $pdo->query($queryPendentes);
$pendenteAtual = $stmt->fetchColumn();

$queryPendentesProximoMes = "SELECT SUM(valor) FROM pagamentos WHERE MONTH(data_pagamento) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND status = 'pendente'";
$stmt = $pdo->query($queryPendentesProximoMes);
$pendenteProximo = $stmt->fetchColumn();
$queryContasPagar = "SELECT SUM(valor) AS total FROM pagamentos WHERE status = 'pendente' AND data_baixa IS NULL";
$stmt = $pdo->query($queryContasPagar);
$contasPagar = $stmt->fetchColumn();

// Serviços de quarto atendidos
$queryServicosAtendidos = "SELECT COUNT(*) AS total FROM pedidos_servico_quarto WHERE status = 'Atendido'";
$resultServicosAtendidos = $pdo->query($queryServicosAtendidos);
$servicosAtendidos = $resultServicosAtendidos->fetch(PDO::FETCH_ASSOC)['total'];

$queryDespesaFuncionarios = "SELECT SUM(salario) AS total FROM funcionarios";
$resultDespesaFuncionarios = $pdo->query($queryDespesaFuncionarios);
$despesaFuncionarios = $resultDespesaFuncionarios->fetchColumn();
$queryFeriasVencidas = "SELECT COUNT(*) AS funcionarios_podem_tirar_ferias FROM funcionarios WHERE dt_final_ferias IS NULL OR DATE_ADD(dt_final_ferias, INTERVAL 11 MONTH) <= CURDATE();";
$resultFeriasVencidas = $pdo->query($queryFeriasVencidas);
$feriasVencidas = $resultFeriasVencidas->fetchColumn();
$queryValoresReceber = "SELECT SUM(valor) AS total FROM pagamentos WHERE status = 'pendente' AND data_baixa IS NULL";
$resultValoresReceber = $pdo->query($queryValoresReceber);
$valoresReceber = $resultValoresReceber->fetchColumn();
$queryProximaConta = "SELECT valor FROM pagamentos WHERE status = 'pendente' AND data_pagamento > CURDATE() ORDER BY data_pagamento ASC LIMIT 1";
$resultProximaConta = $pdo->query($queryProximaConta);
$proximaConta = $resultProximaConta->fetchColumn();

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerenciamento de Hotel</title>
    <link rel="stylesheet" href="./css/home.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
    <?php include("./components/navbar.php"); ?>

    <main>
        <section class="dashboard">
            <!-- Card: Reservas -->
            <a href="./consultas/consulta_reservas.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Reservas</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total de Reservas:</span>
                        <span class="badge bg-info"><?php echo $totalReservas; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Reservas neste mês:</span>
                        <span class="badge bg-success"><?php echo $reservasMes; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Reservas confirmadas:</span>
                        <span class="badge bg-info"><?php echo $reservasConfirmadas; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Reservas aguardando confirmação:</span>
                        <span class="badge bg-warning text-dark"><?php echo $reservasAguardando; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Reservas canceladas:</span>
                        <span class="badge bg-danger"><?php echo $reservasCanceladas; ?></span>
                    </div>
                </div>
            </a>


            <!-- Card: Quartos -->
            <a href="./consultas/consulta_quartos.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bed fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Quartos</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Quantidade de quartos:</span>
                        <span class="badge bg-success"><?php echo $totalQuartos; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Quartos ocupados:</span>
                        <span class="badge bg-warning text-dark"><?php echo $totalOcupados; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Quartos reservados no próximo mês:</span>
                        <span class="badge bg-secondary"><?php echo $quartosProximoMes; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Média de avaliações:</span>
                        <span class="badge bg-success"><?php echo $mediaAvaliacao; ?></span>
                    </div>
                </div>
            </a>


            <a href="./consultas/consulta_clientes.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Clientes</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Clientes em check-in hoje:</span>
                        <span class="badge bg-success"><?= $clientesCadastrados ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Clientes em check-out hoje:</span>
                        <span class="badge bg-success"><?= $clientesCadastrados ?></span>
                    </div>
                </div>
            </a>

            <!-- Card: Serviços de Quarto -->
            <a href="./acoes_reserva/relatorio_servico_quarto.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-concierge-bell fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Serviços de Quarto</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Serviços atendidos:</span>
                        <span class="badge bg-success"><?php echo $servicosAtendidos; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Serviços pendentes:</span>
                        <span class="badge bg-warning text-dark"><?php echo $servicosPendentes; ?></span>
                    </div>
                </div>
            </a>

<!-- Card: Funcionários -->
<a href="./consultas/consulta_funcionarios.php" class="card text-decoration-none text-dark">
    <div class="card-header text-white">
        <div class="d-flex align-items-center">
            <i class="fas fa-users fa-2x me-3"></i>
            <h2 class="mb-0 text-white">Funcionários</h2>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span>Funcionários ativos:</span>
            <span class="badge bg-success"><?php echo $totalFuncionarios; ?></span>
        </div>
        <hr class="my-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span>Despesa total:</span>
            <span class="badge bg-warning text-dark">R$ <?php echo $despesaFuncionarios; ?></span>
        </div>
        <hr class="my-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span>Férias vencidas:</span>
            <span class="badge bg-danger"><?php echo $feriasVencidas; ?></span>
        </div>
    </div>
</a>

            <a href="./financeiro/baixas_pagamento.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-money-bill-wave fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Baixas de pagamento:</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Valores recebidos no mês:</span>
                        <span class="badge bg-success">R$ <?= number_format($recebidoMes, 2, ',', '.') ?></span>
                    </div>
                    <hr class="my-2">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Valores pendentes (reservas em andamento):</span>
                            <span class="badge bg-warning text-dark">R$ <?= number_format($pendenteAtual, 2, ',', '.') ?></span>
                        </div>
                    </div>
                    <hr class="my-2">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Valores pendentes (próximo mês):</span>
                            <span class="badge bg-warning text-dark">R$ <?= number_format($pendenteProximo, 2, ',', '.') ?></span>
                        </div>
                    </div>
                    
                </div>
            </a>

            <a href="./financeiro/relatorio_financeiro.php" class="card text-decoration-none text-dark">
                <div class="card-header text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line fa-2x me-3"></i>
                        <h2 class="mb-0 text-white">Relatório Financeiro</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Contas a pagar:</span>
                        <span class="badge bg-success">R$ <?= number_format($contasPagar, 2, ',', '.') ?></span>
                    </div>
                    <hr class="my-2">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Valores a receber:</span>
                            <span class="badge bg-warning text-dark">R$ <?= number_format($valoresReceber, 2, ',', '.') ?></span>
                        </div>
                    </div>
                    <hr class="my-2">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Próxima conta a pagar:</span>
                            <span class="badge bg-warning text-dark"><?= $proximaConta ?></span>
                        </div>
                    </div>
                    
                </div>
            </a>

        </section>
    </main>
    <?php include("./components/footer.php"); ?>

    <script>
        // Impede o usuário de voltar para a página anterior
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function() {
            history.pushState(null, null, document.URL);
        });
    </script>
</body>

</html>