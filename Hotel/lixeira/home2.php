<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerenciamento de Hotel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: white;
            color: black;
        }

        .topbar {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        main {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: center;
            padding: 20px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            margin-top: 10vh;
        }

        .card {
            background-color: white;
            color: black;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
            min-height: 200px;
            cursor: pointer;
            border: 1px solid #ddd;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
        }

        .card h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .card p {
            margin: 10px 0;
            font-size: 14px;
            color: #555;
        }

        .card .icon {
            font-size: 24px;
            color: #333;
        }

        a {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>

<body class="bg-light">
    <?php include("./components/navbar.php"); ?>

    <main>
        <section class="dashboard">
            <!-- Card 1: Reservas -->
            <a href="consulta_reservas.php" class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt icon"></i>
                    <h2>Reservas</h2>
                </div>
                <p>Reservar neste mês: 50</p>
                <p>Reservas confirmadas: 40</p>
                <p>Reservas aguardando confirmação: 5</p>
                <p>Reservas canceladas: 5</p>
            </a>

            <!-- Card 2: Quartos -->
            <a href="consulta_quartos.php" class="card">
                <div class="card-header">
                    <i class="fas fa-bed icon"></i>
                    <h2>Quartos</h2>
                </div>
                <p>Quantidade de quartos: 100</p>
                <p>Quartos ocupados: 60 (30 Luxo, 20 Standard, 10 Econômico)</p>
                <p>Quartos reservados no próximo mês: 40</p>
                <p>Média de avaliações: Luxo (4.8), Standard (4.5), Econômico (4.0)</p>
            </a>

            <!-- Card: Serviços de quarto Pendentes -->
            <a href="relatorio_servico_quarto.php" class="card">
                <div class="card-header">
                    <i class="fas fa-concierge-bell icon"></i>
                    <h2>Serviços de Quarto</h2>
                </div>
                <p>Serviços de Quarto Atendidos: 25</p>
                <p>Serviços de Quarto Pendentes: 5</p>
            </a>

            <!-- Card: Clientes -->
            <a href="consulta_clientes.php" class="card">
                <div class="card-header">
                    <i class="fas fa-users icon"></i>
                    <h2>Clientes</h2>
                </div>
                <p>Clientes cadastrdos: 25</p>
            </a>

            <!-- Card 3: Funcionários -->
            <a href="consulta_funcionarios.php" class="card">
                <div class="card-header">
                    <i class="fas fa-users icon"></i>
                    <h2>Funcionários</h2>
                </div>
                <p>Funcionários ativos: 25</p>
                <p>Despesa com funcionários: R$ 80.000,00</p>
                <p>Funcionários com férias vencidas: 3</p>
            </a>

            <!-- Card 4: Baixas de Pagamentos -->
            <a href="baixas_pagamento.php" class="card">
                <div class="card-header">
                    <i class="fas fa-money-bill-wave icon"></i>
                    <h2>Baixas de Pagamentos</h2>
                </div>
                <p>Valores recebidos no mês: R$ 150.000,00</p>
                <p>Valores pendentes (reservas em andamento): R$ 30.000,00</p>
                <p>Valores pendentes (próximo mês): R$ 50.000,00</p>
            </a>

            <!-- Card 5: Relatório Financeiro -->
            <a href="relatorio_financeiro.php" class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line icon"></i>
                    <h2>Relatório Financeiro</h2>
                </div>
                <p>Contas a pagar: R$ 40.000,00</p>
                <p>Valores a receber: R$ 60.000,00</p>
                <p>Próxima conta a pagar: 30/03/2025 (R$ 10.000,00)</p>
            </a>
        </section>
    </main>

    <?php include("../components/footer.php"); ?>
</body>

</html>
