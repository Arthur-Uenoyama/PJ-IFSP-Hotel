<?php
session_start();
include '.././db/dbHotel.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Função para pesquisar funcionários
function buscarFuncionarios($pesquisa = '')
{
    $conn = $GLOBALS['pdo'];

    if ($pesquisa) {
        $pesquisa = '%' . trim($pesquisa) . '%';

        $stmt = $conn->prepare("
            SELECT id, nome, email, cargo, telefone, salario, dt_inicio_ferias, dt_final_ferias
            FROM funcionarios
            WHERE CAST(id AS CHAR) LIKE :pesquisa
            OR nome LIKE :pesquisa
            OR email LIKE :pesquisa
            OR cargo LIKE :pesquisa
            OR telefone LIKE :pesquisa
        ");
        $stmt->bindValue(':pesquisa', $pesquisa);
        $stmt->execute();
    } else {
        $stmt = $conn->query("SELECT id, nome, email, cargo, telefone, salario, dt_inicio_ferias, dt_final_ferias FROM funcionarios");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Captura da pesquisa
$pesquisa = $_GET['pesquisa'] ?? '';
$funcionarios = buscarFuncionarios($pesquisa);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/consulta_funcionarios.css">
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <div class="container mt-4 container-main">
        <div class="title-card mb-4">
            <h1><i class="fas fa-user-tie me-2"></i>Gerenciamento de Funcionários</h1>
            <button class="btn btn-success" onclick="abrirModalEdicao()">
                <i class="fas fa-plus me-2"></i>Novo Funcionário
            </button>
        </div>

        <div class="card-section">
            <form method="GET" action="./consulta_funcionarios.php" class="row g-3">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="pesquisa"
                            placeholder="Pesquisar por ID, nome, email ou cargo..."
                            value="<?= htmlspecialchars($pesquisa, ENT_QUOTES) ?>">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <button class="btn btn-success w-100" type="submit">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <div class="card-section table-responsive">
            <?php if (count($funcionarios) > 0): ?>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?= htmlspecialchars($funcionario['id'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($funcionario['nome'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($funcionario['email'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($funcionario['cargo'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($funcionario['telefone'], ENT_QUOTES) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="abrirModalEdicao(
                                '<?= $funcionario['id'] ?>',
                                '<?= htmlspecialchars($funcionario['nome'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['email'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['cargo'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['telefone'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['salario'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['dt_inicio_ferias'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($funcionario['dt_final_ferias'], ENT_QUOTES) ?>'
                            )">
                                <i class="fas fa-edit me-1"></i>Editar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-warning mb-0">Nenhum funcionário encontrado</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <div class="modal fade" id="modalCadastrarFuncionario" tabindex="-1"
        aria-labelledby="modalCadastrarFuncionarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalCadastrarFuncionarioLabel">Cadastrar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../funcoes.php" id="formFuncionario">
                        <input type="hidden" name="acao" value="cadastrar" id="formAcao">
                        <input type="hidden" name="tabela" value="funcionarios">
                        <input type="hidden" name="id" id="idFuncionario">

                        <div class="row g-3">
                            <!-- Nome -->
                            <div class="col-md-6">
                                <label for="nomeFuncionario" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nomeFuncionario" name="nome"
                                    placeholder="Digite o nome completo" required>
                            </div>

                            <!-- Telefone -->
                            <div class="col-md-6">
                                <label for="telefoneFuncionario" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefoneFuncionario" name="telefone"
                                    placeholder="Digite o telefone" required>
                            </div>

                            <!-- Cargo -->
                            <div class="col-md-6">
                                <label for="cargoFuncionario" class="form-label">Cargo</label>
                                <input type="text" class="form-control" id="cargoFuncionario" name="cargo"
                                    placeholder="Digite o cargo" required>
                            </div>

                            <!-- Salario -->
                            <div class="col-md-6">
                                <label for="salarioFuncionario" class="form-label">Salário</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="salarioFuncionario" name="salario"
                                        placeholder="Digite o salário" required>
                                </div>
                            </div>

                            <div class="row g-3 align-items-center">
                                <!-- Data início férias -->
                                <div class="col-md-4">
                                    <label for="dtInicioFerias" class="form-label">Data início férias</label>
                                    <input type="date" class="form-control" id="dtInicioFerias" name="dt_inicio_ferias"
                                        min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>">
                                </div>

                                <!-- Data final férias -->
                                <div class="col-md-4">
                                    <label for="dtFinalFerias" class="form-label">Data final férias</label>
                                    <input type="date" class="form-control" id="dtFinalFerias" name="dt_final_ferias"
                                        min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>">
                                </div>

                                <!-- Dias utilizados -->
                                <div class="col-md-4">
                                    <div class="h-auto d-flex mt-4">
                                        <span class="me-2">Dias restantes de férias:</span>
                                        <span id="diasUtilizados">30</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="emailFuncionario" class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailFuncionario" name="email"
                                    placeholder="Digite o email" required>
                            </div>

                            <!-- Senha -->
                            <div class="col-md-6">
                                <label for="senhaFuncionario" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senhaFuncionario" name="senha"
                                    placeholder="Deixe em branco para manter a atual">
                            </div>
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Fechar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Máscaras para formulário
        $('#telefoneFuncionario').mask('(00) 00000-0000');
        $('#salarioFuncionario').mask('000.000.000.000.000,00', {reverse: true});

        // Função global para abrir modal
        window.abrirModalEdicao = function(
            id = '', 
            nome = '', 
            email = '', 
            cargo = '', 
            telefone = '', 
            salario = '', 
            dt_inicio_ferias = '', 
            dt_final_ferias = ''
        ) {
            console.log('Dados recebidos:', {id, nome, email, cargo, telefone, salario, dt_inicio_ferias, dt_final_ferias});
            
            // Verifica se o modal existe
            const modalEl = document.getElementById('modalCadastrarFuncionario');
            if (!modalEl) {
                console.error('Elemento modal não encontrado!');
                return;
            }

            // Cria instância do modal
            const modal = new bootstrap.Modal(modalEl);
            
            // Preenche os campos
            $('#idFuncionario').val(id);
            $('#nomeFuncionario').val(nome);
            $('#emailFuncionario').val(email);
            $('#cargoFuncionario').val(cargo);
            $('#telefoneFuncionario').val(telefone);
            $('#salarioFuncionario').val(salario ? parseFloat(salario).toFixed(2) : '');
            $('#dtInicioFerias').val(dt_inicio_ferias);
            $('#dtFinalFerias').val(dt_final_ferias);
            $('#senhaFuncionario').val(''); // Sempre limpa a senha
            
            // Configura ação e título
            $('#formAcao').val(id ? 'editar' : 'cadastrar');
            $('#modalCadastrarFuncionarioLabel').text(id ? 'Editar Funcionário' : 'Cadastrar Funcionário');

            // Calcula dias de férias
            calcularDiasFerias();
            
            // Exibe o modal
            modal.show();
        };

        // Função para calcular dias de férias
        function calcularDiasFerias() {
            const inicio = $('#dtInicioFerias').val();
            const fim = $('#dtFinalFerias').val();
            
            if (inicio && fim) {
                try {
                    const diffTime = new Date(fim) - new Date(inicio);
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    $('#diasUtilizados').text(Math.max(0, 30 - diffDays));
                } catch (e) {
                    console.error('Erro no cálculo de dias:', e);
                    $('#diasUtilizados').text('30');
                }
            } else {
                $('#diasUtilizados').text('30');
            }
        }

        // Eventos para cálculo automático de férias
        $('#dtInicioFerias, #dtFinalFerias').change(calcularDiasFerias);

        // Botão "Novo Funcionário"
        $('[onclick="abrirModalEdicao()"]').click(function() {
            // Limpa todos os campos
            $('#formFuncionario')[0].reset();
            $('#diasUtilizados').text('30');
            $('#formAcao').val('cadastrar');
            $('#modalCadastrarFuncionarioLabel').text('Cadastrar Funcionário');
        });

        // Limpeza ao fechar o modal
        $('#modalCadastrarFuncionario').on('hidden.bs.modal', function() {
            // Remove o backdrop manualmente se necessário
            $('.modal-backdrop').remove();
            
            // Restaura o estado do body
            $('body').removeClass('modal-open');
            $('body').css({
                'overflow': 'auto',
                'padding-right': '0'
            });
        });
    });
    </script>
</body>
</html>
