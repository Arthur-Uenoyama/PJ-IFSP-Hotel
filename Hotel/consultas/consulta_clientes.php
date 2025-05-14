<?php
session_start();
include '.././db/dbHotel.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Função para pesquisar clientes
function buscarClientes($pesquisa = '')
{
    $conn = $GLOBALS['pdo'];

    if ($pesquisa) {
        $pesquisa = '%' . trim($pesquisa) . '%';

        $stmt = $conn->prepare("
            SELECT id, nome_completo, email, telefone_celular, tipo_documento 
            FROM usuarios
            WHERE CAST(id AS CHAR) LIKE :pesquisa
            OR nome_completo LIKE :pesquisa
            OR email LIKE :pesquisa
            OR telefone_celular LIKE :pesquisa
            OR tipo_documento LIKE :pesquisa
        ");
        $stmt->bindValue(':pesquisa', $pesquisa);
        $stmt->execute();
    } else {
        $stmt = $conn->query("SELECT id, nome_completo, email, telefone_celular, tipo_documento FROM usuarios");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Captura da pesquisa
$pesquisa = $_GET['pesquisa'] ?? '';
$clientes = buscarClientes($pesquisa);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/consulta_clientes.css">
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <div class="container mt-4 container-main bg-light">
        <div class="title-card mb-4">
            <h1><i class="fas fa-users me-2"></i>Gerenciamento de Clientes</h1>
            <button class="btn btn-success" onclick="abrirModalCliente()">
                <i class="fas fa-plus me-2"></i>Novo Cliente
            </button>
        </div>

        <div class="card-section">
            <form method="GET" action="./consulta_clientes.php" class="row g-3">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="pesquisa"
                            placeholder="Pesquisar por ID, nome, email ou telefone..."
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
            <?php if (count($clientes) > 0): ?>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Completo</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['id'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($cliente['nome_completo'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($cliente['email'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone_celular'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($cliente['tipo_documento'], ENT_QUOTES) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="abrirModalCliente(
                                        '<?= $cliente['id'] ?>',
                                        '<?= htmlspecialchars($cliente['nome_completo'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($cliente['email'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($cliente['telefone_celular'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($cliente['tipo_documento'], ENT_QUOTES) ?>'
                                    )">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning mb-0">Nenhum cliente encontrado</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <div class="modal fade" id="modalCliente" tabindex="-1"
        aria-labelledby="modalClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalClienteLabel">Cadastrar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../funcoes.php" id="formCliente">
                        <input type="hidden" name="acao" value="cadastrar" id="formAcao">
                        <input type="hidden" name="tabela" value="usuarios">
                        <input type="hidden" name="id" id="idCliente">

                        <div class="row g-3">
                            <!-- Nome Completo -->
                            <div class="col-md-6">
                                <label for="nomeCliente" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nomeCliente" name="nome_completo"
                                    placeholder="Digite o nome completo" required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="emailCliente" class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailCliente" name="email"
                                    placeholder="Digite o email" required>
                            </div>

                            <!-- Telefone -->
                            <div class="col-md-6">
                                <label for="telefoneCliente" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefoneCliente" name="telefone_celular"
                                    placeholder="Digite o telefone" required>
                            </div>

                            <!-- Tipo de Documento -->
                            <div class="col-md-6">
                                <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="tipoDocumento" name="tipo_documento" required>
                                    <option value="">Selecione...</option>
                                    <option value="CPF">CPF</option>
                                    <option value="RG">RG</option>
                                    <option value="Passaporte">Passaporte</option>
                                </select>
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
        // Máscara para telefone
        $('#telefoneCliente').mask('(00) 00000-0000');

        // Função global para abrir modal
        window.abrirModalCliente = function(
            id = '', 
            nome = '', 
            email = '', 
            telefone = '', 
            tipoDocumento = ''
        ) {
            console.log('Dados recebidos:', {id, nome, email, telefone, tipoDocumento});
            
            // Verifica se o modal existe
            const modalEl = document.getElementById('modalCliente');
            if (!modalEl) {
                console.error('Elemento modal não encontrado!');
                return;
            }

            // Cria instância do modal
            const modal = new bootstrap.Modal(modalEl);
            
            // Preenche os campos
            $('#idCliente').val(id);
            $('#nomeCliente').val(nome);
            $('#emailCliente').val(email);
            $('#telefoneCliente').val(telefone);
            $('#tipoDocumento').val(tipoDocumento);
            
            // Configura ação e título
            $('#formAcao').val(id ? 'editar' : 'cadastrar');
            $('#modalClienteLabel').text(id ? 'Editar Cliente' : 'Cadastrar Cliente');

            // Exibe o modal
            modal.show();
        };

        // Botão "Novo Cliente"
        $('[onclick="abrirModalCliente()"]').click(function() {
            // Limpa todos os campos
            $('#formCliente')[0].reset();
            $('#formAcao').val('cadastrar');
            $('#modalClienteLabel').text('Cadastrar Cliente');
        });

        // Limpeza ao fechar o modal
        $('#modalCliente').on('hidden.bs.modal', function() {
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
