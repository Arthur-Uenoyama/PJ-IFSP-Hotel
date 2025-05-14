<?php
include(__DIR__ . '/.././db/dbHotel.php');

define('BASE_URL', '/StayEase-Solutionsv2/Hotel/');


/*if (session_status() == PHP_SESSION_NONE) {
  session_start();
}*/

$_SESSION['id'] = 1;

if (!isset($_SESSION['id'])) {
  header("Location: ../index.php");
  exit;
}

/*$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Buscar informações do usuário
try {
  $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE Id = ?");
  $stmt->execute([$usuarioId]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Erro ao carregar usuário: " . $e->getMessage();
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo']; // 'cliente' ou 'hotel'

// Buscar informações do usuário
try {
  $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE Id = ?");
  $stmt->execute([$usuarioId]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Erro ao carregar usuário: " . $e->getMessage();
}

$quartos = [];
$termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

if ($usuarioTipo === 'hotel') {
  $sql = "SELECT q.id AS QuartoId, q.numero, q.tipo, q.preco, q.status, 
                   u.Nome AS ClienteNome, r.data_checkin, r.data_checkout
            FROM Quartos q
            LEFT JOIN Reservas r ON q.id = r.quarto_id
            LEFT JOIN Usuarios u ON r.usuario_id = u.id";

  if (!empty($termo)) {
    $sql .= " WHERE u.Nome LIKE :termo";
  }

  try {
    $stmt = $pdo->prepare($sql);
    if (!empty($termo)) {
      $stmt->bindValue(':termo', "%$termo%");
    }
    $stmt->execute();
    $quartos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die("Erro ao buscar quartos: " . $e->getMessage());
  }
}*/

//$_SESSION['tipo'] = "outro";
?>

<!-- Importação de fontes e Bootstrap -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link rel="stylesheet" href="/StayEase-Solutionsv2/Hotel/css/components.css">

<style>
  .navbar {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
  }

  .navbar-brand {
    font-weight: bold;
    font-size: 22px;
  }

  .navbar-nav .nav-link {
    font-size: 16px;
    color: white !important;
    padding: 10px 15px;
    transition: 0.3s ease-in-out;
  }

  .navbar-nav .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
  }

  .accordion-button:not(.collapsed) {
    background-color: #2e7d32;
    color: white;
  }

  .accordion-button:focus {
    box-shadow: none;
    border-color: #2e7d32;
  }

  .accordion-body {
    background-color: #f8f9fa;
  }

  .form-floating label {
    padding-left: 35px;
  }

  .form-control {
    transition: all 0.3s ease;
  }

  .form-control:focus {
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
  }

  .progress {
    background-color: #e9ecef;
    border-radius: 4px;
  }

  #forcaSenha {
    transition: width 0.3s ease;
  }

  /* Garante que o modal tenha bordas arredondadas consistentes */
  .modal-content {
    border-radius: 12px !important;
    /* Define bordas arredondadas consistentes */
    overflow: hidden;
    /* Garante que os elementos internos respeitem as bordas */
    border: 1px solid #dee2e6;
    /* Adiciona uma borda consistente */
  }

  /* Garante que o primeiro accordion-item tenha bordas arredondadas superiores */
  .accordion-item:first-child {
    border-top-left-radius: 12px !important;
    /* Arredonda o canto superior esquerdo */
    border-top-right-radius: 12px !important;
    /* Arredonda o canto superior direito */
    overflow: hidden;
    /* Garante que os elementos internos respeitem as bordas */
  }

  /* Garante que o último accordion-item tenha bordas arredondadas inferiores */
  .accordion-item:last-child {
    border-bottom-left-radius: 12px !important;
    /* Arredonda o canto inferior esquerdo */
    border-bottom-right-radius: 12px !important;
    /* Arredonda o canto inferior direito */
    overflow: hidden;
    /* Garante que os elementos internos respeitem as bordas */
  }

  /* Remove bordas internas do accordion-collapse */
  .accordion-collapse {
    border: none !important;
    /* Remove bordas internas */
  }

  /* Remove o border-radius padrão do botão para evitar sobreposição */
  .accordion-button {
    border-radius: 0 !important;
    /* Remove bordas arredondadas do botão */
  }

  /* Garante que o botão do primeiro accordion-item respeite o border-radius */
  .accordion-item:first-child .accordion-button {
    border-top-left-radius: 12px !important;
    /* Arredonda o canto superior esquerdo */
    border-top-right-radius: 12px !important;
    /* Arredonda o canto superior direito */
  }

  /* Remove bordas duplicadas entre os accordion-items */
  .accordion-item+.accordion-item {
    border-top: none !important;
    /* Remove a borda superior entre os itens */
  }

  /* Corrige o comportamento ao abrir o último accordion-item */
  .accordion-item:last-child .accordion-collapse {
    border-bottom-left-radius: 12px !important;
    /* Arredonda o canto inferior esquerdo */
    border-bottom-right-radius: 12px !important;
    /* Arredonda o canto inferior direito */
    overflow: hidden;
    /* Garante que os elementos internos respeitem as bordas */
  }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <!-- Logo e nome da pousada -->
    <a class="navbar-brand d-flex align-items-center" href="<?= $_SESSION['tipo'] === 'cliente' ? './hospedes/minhas_reservas_cliente.php' : '../home.php' ?>">
      <i class="bi bi-building fs-4 me-2"></i>Apê Pousada
    </a>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <?php if ($_SESSION['tipo'] === 'cliente'): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>hospedes/reserva_quartos_cliente.php"><i class="fas fa-bed"></i> Fazer Reserva</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>hospedes/minhas_reservas_cliente.php"><i class="fas fa-hotel"></i> Minhas Reservas</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>home.php"><i class="fas fa-home"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>consultas/consulta_reservas.php"><i class="fas fa-calendar-check"></i> Gerenciar Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>consultas/consulta_quartos.php"><i class="fas fa-hotel"></i> Gerenciar Quartos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>acoes_reserva/relatorio_servico_quarto.php"><i class="fas fa-concierge-bell"></i> Serviços de Quarto</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>consultas/consulta_clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>consultas/consulta_funcionarios.php"><i class="fas fa-user-group"></i> Funcionários</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>financeiro/baixas_pagamento.php"><i class="fas fa-money-check-alt"></i> Pagamentos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>financeiro/relatorio_financeiro.php"><i class="fas fa-chart-line"></i> Financeiro</a></li>
        <?php endif; ?>
      </ul>
      <!-- icone do usuario -->
      <div class="d-flex align-items-center justify-content-end" style="width: 165.590px;">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#editarCliente">
          <i class="fas fa-user-circle fa-lg"></i>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Modal de Perfil -->
<div class="modal fade" id="editarCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title text-success"><i class="fas fa-user-edit me-2"></i>Editar Cadastro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="index.php" method="POST">
          <!-- Accordion de Seções -->
          <div class="accordion" id="perfilAccordion">

            <!-- Seção Dados Pessoais -->
            <div class="accordion-item border-success mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button bg-success text-white" type="button"
                  data-bs-toggle="collapse" data-bs-target="#perfil_dadosPessoais"
                  aria-expanded="true" aria-controls="perfil_dadosPessoais">
                  <i class="fas fa-user-circle me-2"></i>Dados Pessoais
                </button>
              </h2>
              <div id="perfil_dadosPessoais" class="accordion-collapse collapse show"
                data-bs-parent="#perfilAccordion">
                <div class="accordion-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Nome Completo</label>
                      <input type="text" class="form-control" name="perfil_nome_completo" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">E-mail</label>
                      <input type="email" class="form-control" name="perfil_email" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Telefone Celular</label>
                      <input type="text" class="form-control" name="perfil_telefone_celular">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Telefone Fixo</label>
                      <input type="text" class="form-control" name="perfil_telefone_fixo">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Data de Nascimento</label>
                      <input type="date" class="form-control" name="perfil_data_nascimento" min="1900-01-01" max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Sexo</label>
                      <select class="form-select" name="perfil_sexo">
                        <option value="">Selecione...</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                        <option value="outro">Outro</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Seção Dados Adicionais -->
            <div class="accordion-item border-success mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button bg-success text-white collapsed" type="button"
                  data-bs-toggle="collapse" data-bs-target="#perfil_dadosAdicionais"
                  aria-expanded="false" aria-controls="perfil_dadosAdicionais">
                  <i class="fas fa-id-card me-2"></i>Dados Adicionais
                </button>
              </h2>
              <div id="perfil_dadosAdicionais" class="accordion-collapse collapse"
                data-bs-parent="#perfilAccordion">
                <div class="accordion-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Profissão</label>
                      <input type="text" class="form-control" name="perfil_profissao">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Empresa</label>
                      <input type="text" class="form-control" name="perfil_empresa">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nacionalidade</label>
                      <input type="text" class="form-control" name="perfil_nacionalidade">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Documento de Identificação</label>
                      <input type="text" class="form-control" name="perfil_documento_identificacao" disabled>
                    </div>
                    <div class="col-md-12">
                      <div class="border p-3 rounded">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="perfil_tipo_documento"
                            id="perfil_cpf" value="cpf" checked disabled>
                          <label class="form-check-label" for="perfil_cpf">CPF</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="perfil_tipo_documento"
                            id="perfil_cnpj" value="cnpj" disabled>
                          <label class="form-check-label" for="perfil_cnpj">CNPJ</label>
                        </div>
                        <input type="text" class="form-control mt-2"
                          name="perfil_documento" id="perfil_documento" disabled>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Seção Endereço -->
            <div class="accordion-item border-success mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button bg-success text-white collapsed" type="button"
                  data-bs-toggle="collapse" data-bs-target="#perfil_endereco"
                  aria-expanded="false" aria-controls="perfil_endereco">
                  <i class="fas fa-map-marker-alt me-2"></i>Endereço
                </button>
              </h2>
              <div id="perfil_endereco" class="accordion-collapse collapse"
                data-bs-parent="#perfilAccordion">
                <div class="accordion-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">CEP</label>
                      <input type="text" class="form-control" name="perfil_cep">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Logradouro</label>
                      <input type="text" class="form-control" name="perfil_logradouro">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número</label>
                      <input type="text" class="form-control" name="perfil_numero">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Complemento</label>
                      <input type="text" class="form-control" name="perfil_complemento">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Bairro</label>
                      <input type="text" class="form-control" name="perfil_bairro">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Cidade</label>
                      <input type="text" class="form-control" name="perfil_cidade">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Estado</label>
                      <select class="form-select" name="perfil_estado">
                        <option value="">Selecione seu estado...</option>
                        <option value="AC">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amapá</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Ceará</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Espírito Santo</option>
                        <option value="GO">Goiás</option>
                        <option value="MA">Maranhão</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Pará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PR">Paraná</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piauí</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rondônia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">São Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botões de Ação -->
          <div class="d-flex justify-content-center gap-3 mt-4">
            <button type="submit" class="btn btn-success px-5">
              <i class="fas fa-save me-2"></i>Salvar Alterações
            </button>
            <button type="button" class="btn btn-warning px-5"
              data-bs-toggle="modal" data-bs-target="#perfil_trocarSenhaModal">
              <i class="fas fa-lock me-2"></i>Trocar Senha
            </button>
            <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-danger px-5">
              <i class="fas fa-sign-out-alt me-2"></i>Sair
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Troca de Senha -->
<div class="modal fade" id="trocarSenhaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fas fa-lock me-2"></i>Alterar Senha
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form action="trocar_senha.php" method="POST">
          <!-- Senha Atual -->
          <div class="form-floating mb-4">
            <input type="password"
              class="form-control border-success"
              name="senha_atual"
              id="senha_atual"
              placeholder=" "
              required>
            <label for="senha_atual" class="text-muted">
              <i class="fas fa-key me-2 text-success"></i>Senha Atual
            </label>
          </div>

          <!-- Nova Senha -->
          <div class="form-floating mb-3">
            <input type="password"
              class="form-control border-success"
              name="nova_senha"
              id="nova_senha"
              placeholder=" "
              required
              oninput="validarForcaSenha(this.value)">
            <label for="nova_senha" class="text-muted">
              <i class="fas fa-lock me-2 text-success"></i>Nova Senha
            </label>
          </div>

          <!-- Indicador de Força da Senha -->
          <div class="progress mb-4" style="height: 5px;">
            <div id="forcaSenha" class="progress-bar" role="progressbar"></div>
          </div>

          <!-- Confirmar Senha -->
          <div class="form-floating mb-4">
            <input type="password"
              class="form-control border-success"
              name="confirmar_senha"
              id="confirmar_senha"
              placeholder=" "
              required
              oninput="validarSenhas()">
            <label for="confirmar_senha" class="text-muted">
              <i class="fas fa-check-circle me-2 text-success"></i>Confirmar Nova Senha
            </label>
          </div>

          <!-- Mensagem de Erro -->
          <div id="senhaError" class="alert alert-danger d-none">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span id="mensagemErro"></span>
          </div>

          <!-- Botões -->
          <div class="d-grid gap-2">
            <button type="submit"
              class="btn btn-success btn-lg py-3"
              id="submitSenha"
              disabled>
              <i class="fas fa-sync-alt me-2"></i>Atualizar Senha
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function validarSenhas() {
    const novaSenha = document.getElementById('nova_senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    const errorDiv = document.getElementById('senhaError');
    const submitBtn = document.getElementById('submitSenha');

    if (novaSenha !== confirmarSenha && confirmarSenha !== '') {
      errorDiv.classList.remove('d-none');
      document.getElementById('mensagemErro').textContent = 'As senhas não coincidem';
      submitBtn.disabled = true;
    } else {
      errorDiv.classList.add('d-none');
      submitBtn.disabled = !(novaSenha.length >= 8 && confirmarSenha.length >= 8);
    }
  }

  function validarForcaSenha(senha) {
    const forcaSenha = document.getElementById('forcaSenha');
    let strength = 0;

    if (senha.length >= 8) strength += 25;
    if (senha.match(/[A-Z]/)) strength += 25;
    if (senha.match(/[0-9]/)) strength += 25;
    if (senha.match(/[^A-Za-z0-9]/)) strength += 25;

    forcaSenha.style.width = strength + '%';
    forcaSenha.classList.remove('bg-danger', 'bg-warning', 'bg-success');

    if (strength < 50) {
      forcaSenha.classList.add('bg-danger');
    } else if (strength < 75) {
      forcaSenha.classList.add('bg-warning');
    } else {
      forcaSenha.classList.add('bg-success');
    }

    validarSenhas();
  }

  // Mostrar/Ocultar Senha
  document.querySelectorAll('.form-floating').forEach((div, index) => {
    const eye = document.createElement('span');
    eye.className = 'position-absolute top-50 end-0 translate-middle-y pe-3';
    eye.innerHTML = '<i class="fas fa-eye-slash text-success cursor-pointer"></i>';
    eye.onclick = () => {
      const input = div.querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
      eye.innerHTML = input.type === 'password' ?
        '<i class="fas fa-eye-slash text-success"></i>' :
        '<i class="fas fa-eye text-success"></i>';
    };
    div.appendChild(eye);
  });
</script>

<!-- Modal de Cadastro -->
<?php include("cadastro.php"); ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
