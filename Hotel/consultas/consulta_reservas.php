<?php
session_start();
include '.././db/dbHotel.php';
include '../components/avaliacao.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

function buscarReservas($pesquisa = '', $data_checkin = null, $data_checkout = null)
{
    $conn = $GLOBALS['pdo'];

    $sql = "SELECT r.*, 
                   q.numero AS numero_quarto,
                   u.nome_completo AS nome_usuario
            FROM reservas r
            JOIN quartos q ON r.quarto_id = q.id
            JOIN usuarios u ON r.usuario_id = u.id";

    $conditions = [];
    $params = [];

    // Filtro de texto apenas nos campos especificados
    if (!empty($pesquisa)) {
        $termo = '%' . trim($pesquisa) . '%';
        $conditions[] = "(CAST(r.id AS CHAR) LIKE :pesquisa OR
                         u.nome_completo LIKE :pesquisa OR
                         r.status LIKE :pesquisa OR
                         CAST(r.valor_reserva AS CHAR) LIKE :pesquisa)";
        $params[':pesquisa'] = $termo;
    }

    // Filtro por data de check-in
    if (!empty($data_checkin)) {
        $conditions[] = "r.data_checkin >= :data_checkin";
        $params[':data_checkin'] = $data_checkin;
    }

    // Filtro por data de check-out
    if (!empty($data_checkout)) {
        $conditions[] = "r.data_checkout <= :data_checkout";
        $params[':data_checkout'] = $data_checkout;
    }

    // Combina todas as condições
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ordenação por data de check-in (mais recentes primeiro)
    $sql .= " ORDER BY r.data_checkin DESC";

    $stmt = $conn->prepare($sql);

    // Bind dos parâmetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Captura dos parâmetros de pesquisa
$pesquisa = $_GET['pesquisa'] ?? '';
$data_checkin = $_GET['data_checkin'] ?? null;
$data_checkout = $_GET['data_checkout'] ?? null;

// Validação das datas
if ($data_checkin && !DateTime::createFromFormat('Y-m-d', $data_checkin)) {
    $data_checkin = null; // Ignora data inválida
}

if ($data_checkout && !DateTime::createFromFormat('Y-m-d', $data_checkout)) {
    $data_checkout = null; // Ignora data inválida
}

// Verifica se há pelo menos um filtro ativo
$reservas = buscarReservas($pesquisa, $data_checkin, $data_checkout);

// Captura da pesquisa (se não existir, fica vazio '')
//$pesquisa = $_GET['pesquisa'] ?? '';
//$reservas = buscarReservas($pesquisa);

//echo"<pre>";print_r($reservas);die();

function formatarDataBrasil($dataMysql)
{
    if (empty($dataMysql)) {
        return ''; // Retorna vazio se a data for vazia
    }

    // Cria um objeto DateTime a partir da string MySQL
    $data = new DateTime($dataMysql);

    // Formata para o padrão brasileiro
    return $data->format('d/m/Y');
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/consulta_reservas.css">
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>
    <div class="container container-main mt-5 bg-light">
        <div class="title-card mb-4">
            <h1><i class="fas fa-calendar-alt me-2"></i>Gerenciamento de Reservas</h1>
            <button class="btn btn-success btn-icon" onclick="window.location.href='../cadastros/cadastro_reserva.php'">
                <i class="fas fa-plus me-2"></i>Nova Reserva
            </button>
        </div>

        <!-- Card de Pesquisa -->
        <div class="card-section">
            <form method="GET" action="../consultas/consulta_reservas.php" class="row g-3">
                <div class="col-12 col-md-4 mt-5">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="pesquisa"
                            value="<?= htmlspecialchars($pesquisa) ?>"
                            placeholder="Pesquisar por Reserva, Cliente, Status ou Valor...">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label for="data_checkin" class="form-label small">Check-in a partir de:</label>
                    <input type="date" class="form-control" name="data_checkin" id="data_checkin"
                        value="<?= htmlspecialchars($data_checkin) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label for="data_checkout" class="form-label small">Check-out até:</label>
                    <input type="date" class="form-control" name="data_checkout" id="data_checkout"
                        value="<?= htmlspecialchars($data_checkout) ?>">
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100 btn-icon" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Card da Tabela -->
        <div class="card-section table-container">
            <?php if (count($reservas) > 0): ?>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Reserva</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Cliente</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reservas): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservas['id']) ?></td>
                                <td>R$ <?= number_format($reservas['valor_reserva'], 2, ',', '.') ?></td>
                                <td>
                                    <?php

                                    $statusOptions = [
                                        'confirmada' => 'btn-outline-success',
                                        'pendente' => 'btn-outline-warning',
                                        'em andamento' => 'btn-outline-primary',
                                        'finalizada' => 'btn-outline-info',
                                        'cancelada' => 'btn-outline-danger'
                                    ];
                                    $statusLower = strtolower($reservas['status']);
                                    $badgeClass = isset($statusOptions[$statusLower]) ?
                                        str_replace('btn-outline-', 'bg-', $statusOptions[$statusLower]) :
                                        'bg-secondary';
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?> text-white">
                                        <?= ucfirst($reservas['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($reservas['nome_usuario']) ?></td>
                                <td><?= htmlspecialchars(formatarDataBrasil($reservas['data_checkin'])) ?></td>
                                <td><?= htmlspecialchars(formatarDataBrasil($reservas['data_checkout'])) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary btn-icon"
                                            onclick="window.location.href='../acoes_reserva/servico_quarto.php'">
                                            <i class="fas fa-concierge-bell"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary btn-icon"
                                            onclick="window.location.href='../acoes_reserva/editar_reserva.php?id=<?= $reservas['id'] ?>'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning btn-icon"
                                            onclick="abrirModalAvaliacao(<?= $reservas['id'] ?>)">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning mb-0">Nenhuma reserva encontrada</div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        function abrirModalEdicao(id, numero, tipo, preco, checkin, checkout, status) {
            // Preenche os campos do modal
            document.getElementById('quarto_id').value = id;
            document.getElementById('name').value = "João Silva";
            document.getElementById('email').value = "joao.silva@email.com";
            document.getElementById('check_in').value = checkin;
            document.getElementById('check_out').value = checkout;
            document.getElementById('numero_quarto').value = numero;
            document.getElementById('tipo_quarto').value = tipo;
            document.getElementById('valorTotal').value = preco.replace(',', '');
            document.getElementById('status').value = status;

            // Remove a classe 'active' de todos os botões de status
            const buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach(button => button.classList.remove('active'));

            // Adiciona a classe 'active' ao botão correspondente ao status atual
            let statusButton;
            switch (status) {
                case 'Confirmada':
                    statusButton = document.querySelector('.btn-group .btn-outline-success');
                    break;
                case 'Pendente':
                    statusButton = document.querySelector('.btn-group .btn-outline-warning');
                    break;
                case 'Cancelada':
                    statusButton = document.querySelector('.btn-group .btn-outline-danger');
                    break;
                case 'Finalizada':
                    statusButton = document.querySelector('.btn-group .btn-outline-secondary');
                    break;
            }
            if (statusButton) {
                statusButton.classList.add('active');
            }

            // Abre o modal
            let reservaModal = new bootstrap.Modal(document.getElementById('reservaModal'));
            reservaModal.show();
        }

        function alterarStatus(status) {
            // Atualiza o valor do campo oculto
            document.getElementById('status').value = status;

            // Remove a classe 'active' de todos os botões
            const buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach(button => button.classList.remove('active'));

            // Adiciona a classe 'active' ao botão clicado
            event.target.classList.add('active');
        }

        function calcularRestante() {
            // Verifica se os elementos existem
            var valorTotalInput = document.getElementById("valorTotal");
            var valorPagoInput = document.getElementById("valorPago");

            if (!valorTotalInput || !valorPagoInput) {
                console.error("Os campos #valorTotal ou #valorPago não foram encontrados.");
                return;
            }

            // Pega os valores dos campos
            var valorTotal = parseFloat(valorTotalInput.value.replace(",", "."));
            var valorPago = parseFloat(valorPagoInput.value.replace(",", "."));

            // Verifica se os valores são válidos, caso contrário, define como 0
            if (isNaN(valorTotal)) valorTotal = 0;
            if (isNaN(valorPago)) valorPago = 0;

            // Calcula o valor restante
            var valorRestante = valorTotal - valorPago;

            // Atualiza o campo "Valor a ser pago"
            var valorRestanteInput = document.getElementById("valorRestante");
            if (valorRestanteInput) {
                valorRestanteInput.value = valorRestante.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                console.error("O campo #valorRestante não foi encontrado.");
            }
        }

        // Inicializa a função ao carregar a página
        window.onload = function() {
            calcularRestante();
        };

        document.querySelector('input[name="pesquisa"]').addEventListener('input', function(e) {
            // Verifica se é um valor monetário (começa com R$ ou números)
            if (/^[Rr]\$?\s*\d+[,.]?\d*$/.test(this.value) || /^\d+[,.]\d{2}$/.test(this.value)) {
                // Remove tudo que não é número ou vírgula
                let valor = this.value.replace(/[^\d,]/g, '').replace(',', '.');

                // Converte para float e formata novamente
                if (!isNaN(parseFloat(valor))) {
                    this.value = 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const checkinInput = document.getElementById('data_checkin');
            const checkoutInput = document.getElementById('data_checkout');

            // Sincroniza as datas mínimas
            checkinInput.addEventListener('change', function() {
                if (this.value) {
                    checkoutInput.min = this.value;
                    if (checkoutInput.value && checkoutInput.value < this.value) {
                        checkoutInput.value = this.value;
                    }
                }
            });

            // Validação antes de enviar o formulário
            document.querySelector('form').addEventListener('submit', function(e) {
                if (checkinInput.value && checkoutInput.value && checkinInput.value > checkoutInput.value) {
                    alert('A data de check-out não pode ser anterior à data de check-in');
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>