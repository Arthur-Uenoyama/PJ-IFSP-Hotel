<?php
session_start();
include('.././db/dbHotel.php');

// Verifica se o usuário está logado e tem permissão para adicionar ou editar quartos
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioTipo = $_SESSION['tipo'] ?? null; // Define a variável corretamente
$usuario = null; // Inicializa a variável para evitar erros

// Obtém os dados do usuário logado
$usuarioId = $_SESSION['id'];

try {
    $stmt = $pdo->prepare("SELECT nome_completo, email FROM usuarios WHERE id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Erro ao buscar informações do usuário: " . $e->getMessage();
}

// Verifica se foi passada uma requisição GET com um parâmetro 'id'
$editar = isset($_GET['id']) ? $_GET['id'] : null;
$quarto = null;

if ($editar) {
    $query = "SELECT * FROM quartos WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$editar]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Corrigido para usar 'imagem' em vez de 'imagens'
    $imagens = !empty($quarto['imagem']) ? json_decode($quarto['imagem'], true) : [];

    $regras = !empty($quarto['regras']) ? json_decode($quarto['regras'], true) : [];

    if (!$quarto) {
        $erro = "Quarto não encontrado!";
    }
}

// Exibe mensagens de erro de upload
if (!empty($errosUpload)) {
    foreach ($errosUpload as $erroUpload) {
        echo "<div class='alert alert-warning'>$erroUpload</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $quarto ? 'Editar Quarto' : 'Adicionar Novo Quarto' ?> - Apê Pousada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/StayEase-Solutionsv2/Hotel/css/cadastro_quarto.css">
</head>

<body class="bg-light">

    <?php include("../components/navbar.php"); ?>

    <div class="container mt-5 container-main bg-light">
        <div class="form-section">
            <h1 class="mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-bed" style="font-size: 1em;"></i>
                <?= $quarto ? 'Editar Quarto' : 'Cadastrar Novo Quarto' ?>
            </h1>

            <form id="formQuarto" method="POST" action="../funcoes.php" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="<?= $quarto ? 'editar' : 'cadastrar' ?>">
                <input type="hidden" name="tabela" value="quartos">
                <input type="hidden" name="origem" value="./cadastros/cadastro_quarto.php">
                <?= $quarto ? '<input type="hidden" name="id" value="' . $quarto['id'] . '">' : '' ?>
                <div class="card-section">
                    <h5 class="text-green mb-4"><i class="bi bi-info-circle me-2"></i>Informações Básicas</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número do Quarto</label>
                            <input type="text" name="numero" class="form-control" value="<?= $quarto ? $quarto['numero'] : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Preço por Noite (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" id="preco" name="preco" class="form-control"
                                    step="0.01" min="0" value="<?= $quarto ? number_format($quarto['preco'], 2, ',', '.') : '' ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuração de quantidade de camas -->
                <div class="card-section">
                    <h5 class="text-green mb-4"><i class="bi bi-layers me-2"></i>Configuração de Camas</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Configuração de Camas</label>
                            <div class="card p-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fas fa-bed"></i>
                                            </span>
                                            <input type="number" name="camas_solteiro" class="form-control" value="<?= $quarto ? $quarto['camas_solteiro'] : '' ?>" placeholder="Camas de solteiro" min="0" step="1">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">
                                                <img src="https://img.icons8.com/?size=25&id=20629&format=png&color=FFFFFF" alt="">
                                            </span>
                                            <input type="number" name="beliches" class="form-control" value="<?= $quarto ? $quarto['beliches'] : '' ?>" placeholder="Beliches" min="0" step="1">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white">
                                                <img src="https://img.icons8.com/?size=25&id=59736&format=png&color=FFFFFF" alt="">
                                            </span>
                                            <input type="number" name="camas_casal" class="form-control" value="<?= $quarto ? $quarto['camas_casal'] : '' ?>" placeholder="Camas de casal" min="0" step="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção de Descrição -->
                <div class="card-section">
                    <h5 class="text-green mb-4"><i class="bi bi-text-paragraph me-2"></i>Descrição Detalhada</h5>
                    <textarea name="descricao" class="form-control" rows="3"
                        placeholder="Descreva as características do quarto..." required><?= $quarto ? htmlspecialchars($quarto['descricao']) : '' ?></textarea>
                </div>

                <!-- Seção de Imagem - Atualizada -->
                <div class="card-section">
                    <h5 class="text-green mb-4"><i class="bi bi-image me-2"></i>Imagens do Quarto</h5>

                    <?php if ($editar && !empty($imagens)): ?>
                        <div class="mb-4">
                            <h6>Imagens Atuais</h6>
                            <div class="d-flex flex-wrap gap-3 mb-3" id="container-imagens-existente">
                                <?php foreach ($imagens as $index => $imagem): ?>
                                    <?php if (!empty($imagem)): ?>
                                        <div class="image-preview-container position-relative" data-imagem="<?= htmlspecialchars($imagem) ?>">
                                            <img src="../uploads/quartos/<?= htmlspecialchars($imagem) ?>"
                                                class="img-thumbnail"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                            <button type="button"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                onclick="removerImagemExistente(this, '<?= htmlspecialchars($imagem) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <input type="hidden" name="imagens_existentes[]" value="<?= htmlspecialchars($imagem) ?>">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Upload de novas imagens -->
                    <div class="file-upload position-relative">
                        <input type="file" name="imagem[]" class="form-control form-hidden" accept="image/*" id="fileInput" multiple <?= !$editar ? 'required' : '' ?>>
                        <label for="fileInput" class="btn btn-outline-success w-100">
                            <i class="bi bi-cloud-upload me-2"></i>Adicionar Novas Imagens
                        </label>
                        <div class="mt-2 text-muted small" id="fileName">Nenhum arquivo selecionado</div>
                        <div class="preview-container mt-3" style="display: none;" id="container-novas-imagens"></div>
                    </div>
                </div>

                <!-- Seção de Regras -->
                <div class="card-section">
                    <h5 class="text-green mb-4"><i class="bi bi-list-check me-2"></i>Regras do Quarto</h5>
                    <button type="button" class="btn btn-outline-success mb-3" onclick="adicionarRegra()">
                        <i class="bi bi-plus-circle me-2"></i>Adicionar Regra
                    </button>
                    <div id="regrasSecundarios">
                        <?php if (!empty($regras)): ?>
                            <?php foreach ($regras as $regra): ?>
                                <div class="row g-3 align-items-center regra-item mb-2">
                                    <div class="col">
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($regra) ?>"
                                            name="regras[]"
                                            placeholder="Digite uma regra">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-danger" onclick="removerRegra(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seção de Ações -->
                <div class="card-section">
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="../consultas/consulta_quartos.php" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-2"></i>Salvar Quarto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Controle de exibição do nome do arquivo
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileList = Array.from(e.target.files).map(file => file.name).join(', ');
            document.getElementById('fileName').textContent = fileList || 'Nenhum arquivo selecionado';
        });

        // Função para adicionar regras
        function adicionarRegra() {
            const container = document.getElementById('regrasSecundarios');
            const novaRegra = document.createElement('div');
            novaRegra.className = 'row g-3 align-items-center regra-item mb-2';
            novaRegra.innerHTML = `
                <div class="col">
                <input type="text" class="form-control" 
                       name="regras[]" 
                       placeholder="Digite uma regra" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger" onclick="removerRegra(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(novaRegra);
        }

        // Função para remover regras
        function removerRegra(botao) {
            botao.closest('.regra-item').remove();
        }

        function removerImagemExistente(botao, caminhoImagem) {
            if (confirm('Tem certeza que deseja remover esta imagem?')) {
                // Cria um input hidden para marcar a imagem para remoção
                const inputRemocao = document.createElement('input');
                inputRemocao.type = 'hidden';
                inputRemocao.name = 'imagens_remover[]';
                inputRemocao.value = caminhoImagem;
                document.getElementById('formQuarto').appendChild(inputRemocao);

                // Remove o container da imagem da visualização
                botao.closest('.image-preview-container').remove();
            }
        }

        // Função para remover novas imagens selecionadas (ainda não enviadas)
        function removerNovaImagem(botao) {
            botao.closest('.nova-imagem-preview').remove();
            atualizarContagemArquivos();
        }

        // Atualiza o contador de arquivos selecionados
        function atualizarContagemArquivos() {
            const inputArquivos = document.getElementById('fileInput');
            const contador = document.getElementById('fileName');

            // Conta quantas pré-visualizações de imagens temos
            const previews = document.querySelectorAll('.nova-imagem-preview').length;

            if (previews > 0) {
                contador.textContent = `${previews} arquivo(s) selecionado(s)`;
            } else {
                contador.textContent = 'Nenhum arquivo selecionado';
            }
        }

        // Preview das novas imagens selecionadas
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('container-novas-imagens');
            previewContainer.innerHTML = '';
            previewContainer.style.display = 'none';

            if (this.files.length > 0) {
                previewContainer.style.display = 'flex';
                previewContainer.style.flexWrap = 'wrap';
                previewContainer.style.gap = '10px';

                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'nova-imagem-preview position-relative';
                        previewDiv.style.width = '150px';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '5px';

                        const btnRemover = document.createElement('button');
                        btnRemover.type = 'button';
                        btnRemover.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1';
                        btnRemover.innerHTML = '<i class="bi bi-trash"></i>';
                        btnRemover.onclick = function() {
                            removerNovaImagem(this);
                        };

                        previewDiv.appendChild(img);
                        previewDiv.appendChild(btnRemover);
                        previewContainer.appendChild(previewDiv);
                    };

                    reader.readAsDataURL(file);
                });

                atualizarContagemArquivos();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const precoInput = document.getElementById('preco');

            // Mude o tipo para text para permitir formatação
            precoInput.type = 'text';

            precoInput.addEventListener('input', function(e) {
                // Remove tudo que não é dígito
                let value = this.value.replace(/\D/g, '');

                // Adiciona os centavos (divide por 100)
                value = (value / 100).toFixed(2);

                // Formata como moeda brasileira
                this.value = formatarMoeda(value);
            });

            // Função auxiliar para formatar como moeda
            function formatarMoeda(valor) {
                return Number(valor).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).replace('R$', '').trim();
            }

            // Para enviar o valor correto ao formulário
            document.getElementById('formQuarto').addEventListener('submit', function(e) {
                // Validação para novos cadastros
                const isEdit = <?= $editar ? 'true' : 'false' ?>;
                const fileInput = document.getElementById('fileInput');
                const existingImages = document.querySelectorAll('[name="imagens_existentes[]"]').length;

                if (!isEdit && fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Por favor, adicione pelo menos uma imagem do quarto');
                    fileInput.focus();
                    return false;
                }

                // Continua com o processamento do preço (código existente)
                const precoInput = document.getElementById('preco');
                const valorNumerico = parseFloat(precoInput.value.replace(/\./g, '').replace(',', '.'));
                precoInput.value = valorNumerico;
            });
        });
    </script>
</body>

</html>
