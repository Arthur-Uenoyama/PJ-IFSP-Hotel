<?php
include(__DIR__ . '/.././db/dbHotel.php');
if (!$pdo) {
  die("Erro ao conectar ao banco de dados.");
}
error_log("Formulário recebido: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['cadastro'])) {
    error_log("Bloco de cadastro executado.");
    try {
      // Recebendo os dados do formulário
      $nome_completo = $_POST['nome_completo'] ?? null;
      $email = $_POST['email'] ?? null;
      $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
      $telefone_fixo = $_POST['telefone_fixo'] ?? null;
      $telefone_celular = $_POST['telefone_celular'] ?? null;
      $data_nascimento = $_POST['data_nascimento'] ?? null;
      $sexo = $_POST['sexo'] ?? null;
      $profissao = $_POST['profissao'] ?? null;
      $nacionalidade = $_POST['nacionalidade'] ?? null;
      $tipo_documento = $_POST['tipo_documento'] ?? null;
      $cpf_cnpj = $_POST['documento'] ?? null;
      $documento_inde = $_POST['documento_identificacao'] ?? null;
      $cep = $_POST['cep'] ?? null;
      $logradouro = $_POST['logradouro'] ?? null;
      $numero = $_POST['numero'] ?? null;
      $complemento = $_POST['complemento'] ?? null;
      $bairro = $_POST['bairro'] ?? null;
      $cidade = $_POST['cidade'] ?? null;
      $estado = $_POST['estado'] ?? null;
      $empresa_trabalha = $_POST['empresa'] ?? null;

      // Verificação de campos obrigatórios
      $camposObrigatorios = [
        'nome_completo' => $nome_completo,
        'email' => $email,
        'senha' => $senha,
        'telefone_celular' => $telefone_celular,
        'data_nascimento' => $data_nascimento,
        'tipo_documento' => $tipo_documento,
        'cpf_cnpj' => $cpf_cnpj,
        'cep' => $cep,
        'logradouro' => $logradouro,
        'numero' => $numero,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'estado' => $estado
      ];

      foreach ($camposObrigatorios as $campo => $valor) {
        if (empty($valor)) {
          throw new Exception("<script>alert('O campo: " . $campo . " é obrigatório');</script>");
        }
      }

      // Preparando a query para inserção
      $stmt = $pdo->prepare("
      INSERT INTO usuarios (
          nome_completo, email, senha, telefone_fixo, telefone_celular, 
          data_nascimento, sexo, profissao, nacionalidade, tipo_documento, 
          cpf_cnpj, documento_Inde, cep, logradouro, numero, 
          complemento, bairro, cidade, estado, empresa_trabalha
      ) VALUES (
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?, ?
      )
  ");

      // Executando a query com os dados do formulário
      $stmt->execute([
        $nome_completo,
        $email,
        $senha,
        $telefone_fixo,
        $telefone_celular,
        $data_nascimento,
        $sexo,
        $profissao,
        $nacionalidade,
        $tipo_documento,
        $cpf_cnpj,
        $documento_inde,
        $cep,
        $logradouro,
        $numero,
        $complemento,
        $bairro,
        $cidade,
        $estado,
        $empresa_trabalha
      ]);

      if ($stmt->rowCount() > 0) {
        echo "<script>window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';
        alert('Cadastro realizado com sucesso!');
    </script>";
      }
      // Mensagem de sucesso
      $mensagemCadastro = "Cadastro realizado com sucesso! Faça login.";
    } catch (PDOException $e) {
      error_log("Erro no banco de dados: " . $e->getMessage());
      echo "<script>alert('Erro no cadastro: " . $e->getMessage() . "');</script>";
    } catch (Exception $e) {
      error_log("Erro de validação: " . $e->getMessage());
      echo "<script>alert('" . $e->getMessage() . "');</script>";
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verifica se é um cadastro simplificado
  $input = json_decode(file_get_contents('php://input'), true);
  if (isset($input['cadastro_simplificado']) && $input['cadastro_simplificado'] === true) {
    try {
      // Dados obrigatórios da primeira etapa
      $nome_completo = $input['nome_completo'] ?? null;
      $tipo_documento = $input['tipo_documento'] ?? null;
      $cpf_cnpj = $input['documento'] ?? null;
      $email = $input['email'] ?? null;
      $senha = isset($input['senha']) ? password_hash($input['senha'], PASSWORD_DEFAULT) : null;

      // Dados genéricos para os campos restantes
      $telefone_fixo = 'Não informado';
      $telefone_celular = 'Não informado';
      $data_nascimento = '2000-01-01';
      $sexo = 'Outro';
      $profissao = 'Não informado';
      $nacionalidade = 'Não informado';
      $cep = '00000-000';
      $logradouro = 'Não informado';
      $numero = '0';
      $complemento = 'Não informado';
      $bairro = 'Não informado';
      $cidade = 'Não informado';
      $estado = 'XX';
      $empresa_trabalha = 'Não informado';

      // Inserção no banco de dados
      $stmt = $pdo->prepare("
        INSERT INTO usuarios (
          nome_completo, email, senha, telefone_fixo, telefone_celular, 
          data_nascimento, sexo, profissao, nacionalidade, tipo_documento, 
          cpf_cnpj, cep, logradouro, numero, complemento, 
          bairro, cidade, estado, empresa_trabalha
        ) VALUES (
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?, ?, 
          ?, ?, ?, ?
        )
      ");

      $stmt->execute([
        $nome_completo,
        $email,
        $senha,
        $telefone_fixo,
        $telefone_celular,
        $data_nascimento,
        $sexo,
        $profissao,
        $nacionalidade,
        $tipo_documento,
        $cpf_cnpj,
        $cep,
        $logradouro,
        $numero,
        $complemento,
        $bairro,
        $cidade,
        $estado,
        $empresa_trabalha,
      ]);

      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      error_log("Erro no cadastro simplificado: " . $e->getMessage());
      echo json_encode(['success' => false, 'message' => 'Erro no banco de dados.']);
    }
    exit;
  }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="/StayEase-Solutionsv2/Hotel/css/components.css">

<!-- Modal de Cadastro -->
<form action="/StayEase-Solutionsv2/Hotel/components/cadastro.php" method="POST">
  <input type="hidden" name="cadastro" value="1">
  <div class="modal fade" id="cadastroCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Cadastro em 3 Etapas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Progresso -->
          <div class="progress mb-4" style="height: 5px;">
            <div class="progress-bar" role="progressbar" style="width: 33%" id="progressBar"></div>
          </div>

          <!-- Carrossel -->
          <div id="formCarousel" class="carousel slide" data-bs-interval="false">
            <div class="carousel-inner">

              <!-- Etapa 1: Dados de Login -->
              <div class="carousel-item active">
                <div class="step-content">
                  <h6 class="mb-4 text-success"><i class="fas fa-user-lock me-2"></i>Dados de Acesso</h6>
                  <div class="row g-3">
                    <div class="col-md-12">
                      <label class="form-label">Nome Completo</label>
                      <input type="text" class="form-control" name="nome_completo" placeholder="Nome completo" required>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        <div class="form-check form-check-inline me-3">
                          <input class="form-check-input" type="radio" name="tipo_documento" id="cpf" value="cpf"
                            checked>
                          <label class="form-check-label small" for="cpf">CPF</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="tipo_documento" id="cnpj" value="cnpj">
                          <label class="form-check-label small" for="cnpj">CNPJ</label>
                        </div>
                      </div>
                    </div>
                    <div>
                      <input type="text" class="form-control" name="documento" id="documento" placeholder="CPF/CNPJ"
                        required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">E-mail</label>
                      <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Senha</label>
                      <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha" required>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Etapa 2: Dados Pessoais -->
              <div class="carousel-item">
                <div class="step-content">
                  <h6 class="mb-4 text-success"><i class="fas fa-id-card me-2"></i>Dados Pessoais</h6>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Telefone Celular</label>
                      <input type="tel" class="form-control" name="telefone_celular" placeholder="Telefone Celular"
                        required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Telefone Fixo</label>
                      <input type="tel" class="form-control" name="telefone_fixo" placeholder="Telefone Fixo">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Data de Nascimento</label>
                      <input type="date" class="form-control" name="data_nascimento" min="1900-01-01"
                        max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Sexo</label>
                      <select class="form-select" name="sexo">
                        <option value="">Selecione...</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                        <option value="outro">Outro</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Profissão</label>
                      <input type="text" class="form-control" name="profissao" placeholder="Profissão">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Empresa</label>
                      <input type="text" class="form-control" name="empresa" placeholder="Empresa onde trabalha">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nacionalidade</label>
                      <input type="text" class="form-control" name="nacionalidade" placeholder="Nacionalidade">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Documento de Identificação</label>
                      <input type="text" class="form-control" name="documento_identificacao"
                        placeholder="RG, CNH, Passaporte">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Etapa 3: Endereço -->
              <div class="carousel-item">
                <div class="step-content">
                  <h6 class="mb-4 text-success"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">CEP</label>
                      <input type="text" class="form-control" name="cep" id="cep" placeholder="CEP" required>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Logradouro</label>
                      <input type="text" class="form-control" name="logradouro" id="logradouro"
                        placeholder="Rua/Avenida/etc" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número</label>
                      <input type="text" class="form-control" name="numero" id="numero" placeholder="Numero" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Complemento</label>
                      <input type="text" class="form-control" name="complemento" id="complemento"
                        placeholder="Apto 101, sobrado, etc.">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Bairro</label>
                      <input type="text" class="form-control" name="bairro" id="bairro" placeholder="Bairro" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Cidade</label>
                      <input type="text" class="form-control" name="cidade" id="cidade" placeholder="Cidade" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Estado</label>
                      <select class="form-select" name="estado" id="estado" required>
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
        </div>

        <!-- Footer com Controles -->
        <div class="modal-footer">
          <?php
          // Verifica se está no index.php para voltar para o login
          if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
            ?>
            <div class="me-auto"> <!-- Div para alinhar à esquerda -->
              <button id="backToLogin" type="button" class="btn text-success">
                <i class="fas fa-arrow-left me-2"></i>Voltar para Login
              </button>
            </div>
            <?php
          }
          ?>

          <!-- Botão de Cadastro Simplificado -->
          <?php if (strpos($_SERVER['REQUEST_URI'], 'index.php') === false && substr($_SERVER['REQUEST_URI'], -1) !== '#'): ?>
            <div class="me-auto"> <!-- Div para alinhar à esquerda -->
              <button type="button" class="btn btn-warning" id="simplifiedRegister">
                <i class="fas fa-user-check me-2"></i>Cadastro Simplificado
              </button>
            </div>
          <?php endif; ?>


          <button type="button" class="btn btn-outline-success" id="prevStep" disabled>
            <i class="fas fa-chevron-left me-2"></i>Voltar
          </button>
          <button type="button" class="btn btn-success" id="nextStep">
            Continuar<i class="fas fa-chevron-right ms-2"></i>
          </button>
          <button type="submit" class="btn btn-success" id="submitForm" style="display: none;">
            Finalizar Cadastro
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  $(document).ready(function () {
    let $documento = $("#documento");
    let $telefoneCelular = $("input[name='telefone_celular']");
    let $telefoneFixo = $("input[name='telefone_fixo']");
    let $cep = $("input[name='cep']");
    let $documentoIdentificacao = $("input[name='documento_identificacao']");

    $documentoIdentificacao.attr("maxlength", "10");

    $documentoIdentificacao.on("input", function () {
      let valor = $(this).val();
      // Remove caracteres que não sejam letras ou números
      $(this).val(valor.replace(/[^a-zA-Z0-9]/g, ""));
    });

    $documento.mask("000.000.000-00", {
      reverse: false
    });
    $documento.attr("maxlength", "14");

    $("input[name='tipo_documento']").change(function () {
      let tipo = $(this).val();

      if (tipo === "cpf") {
        $documento.mask("000.000.000-00", {
          reverse: false
        });
        $documento.attr("maxlength", "14");
      } else {
        $documento.mask("00.000.000/0000-00", {
          reverse: false
        });
        $documento.attr("maxlength", "18");
      }
    });

    // Máscara para telefone celular
    $telefoneCelular.mask("(00) 00000-0000", {
      reverse: false
    });
    $telefoneCelular.attr("maxlength", "15");

    // Máscara para telefone fixo
    $telefoneFixo.mask("(00) 0000-0000", {
      reverse: false
    });
    $telefoneFixo.attr("maxlength", "14");

    // Máscara para CEP
    $cep.mask("00000-000", {
      reverse: false
    });
    $cep.attr("maxlength", "9");

    // Busca automática de endereço via CEP
    $cep.on('blur', function () {
      const cep = $(this).val().replace(/\D/g, '');

      // Verifica se o CEP tem 8 dígitos
      if (cep.length !== 8) {
        return;
      }

      // Mostra um loader enquanto busca
      $(this).addClass('loading');

      // Faz a requisição para a API ViaCEP
      fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
          if (data.erro) {
            throw new Error('CEP não encontrado');
          }

          // Preenche os campos automaticamente
          $("#logradouro").val(data.logradouro || '');
          $("#bairro").val(data.bairro || '');
          $("#cidade").val(data.localidade || '');
          $("#estado").val(data.uf || '');

          // Foca no campo número para facilitar o preenchimento
          $("#numero").focus();
        })
        .catch(error => {
          console.error('Erro ao buscar CEP:', error);
          alert('CEP não encontrado. Por favor, preencha o endereço manualmente.');
        })
        .finally(() => {
          $(this).removeClass('loading');
        });
    });

    // Adiciona estilo para o loader
    const style = document.createElement('style');
    style.textContent = `
      input.loading {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="%23ccc" stroke-width="8" fill="none" stroke-dasharray="60 15" transform="rotate(0 50 50)"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50"/></circle></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px 20px;
      }
    `;
    document.head.appendChild(style);
  });

  document.addEventListener('DOMContentLoaded', () => {
    // Função de validação da idade
    function validarIdade(dataNascimento) {
      if (!dataNascimento) return false;

      const hoje = new Date();
      const nascimento = new Date(dataNascimento);
      let idade = hoje.getFullYear() - nascimento.getFullYear();
      const mes = hoje.getMonth() - nascimento.getMonth();

      // Ajusta a idade se o mês atual for antes do mês de nascimento
      if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
        idade--;
      }

      // Verifica se a idade está dentro do intervalo permitido
      if (idade < 18) {
        alert('Você deve ter pelo menos 18 anos.');
        return false;
      } else if (idade > 140) {
        alert('Idade máxima permitida é de 140 anos.');
        return false;
      }

      return true;
    }


    function validarCPF(cpf) {
      cpf = cpf.replace(/\D/g, ''); // Remove caracteres não numéricos
      if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

      let soma = 0,
        resto;
      for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) resto = 0;
      if (resto !== parseInt(cpf.substring(9, 10))) return false;

      soma = 0;
      for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) resto = 0;
      return resto === parseInt(cpf.substring(10, 11));
    }

    function validarCNPJ(cnpj) {
      cnpj = cnpj.replace(/\D/g, ''); // Remove caracteres não numéricos
      if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

      let tamanho = cnpj.length - 2;
      let numeros = cnpj.substring(0, tamanho);
      let digitos = cnpj.substring(tamanho);
      let soma = 0;
      let pos = tamanho - 7;

      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      if (resultado !== parseInt(digitos.charAt(0))) return false;

      tamanho++;
      numeros = cnpj.substring(0, tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      return resultado === parseInt(digitos.charAt(1));
    }

    const carousel = new bootstrap.Carousel('#formCarousel')
    const progressBar = document.getElementById('progressBar')
    const nextBtn = document.getElementById('nextStep')
    const prevBtn = document.getElementById('prevStep')
    const submitBtn = document.getElementById('submitForm')
    let currentStep = 0
    const totalSteps = 3

    function updateProgress() {
      const progress = ((currentStep + 1) / totalSteps) * 100
      progressBar.style.width = `${progress}%`
      prevBtn.disabled = currentStep === 0
      nextBtn.style.display = currentStep === totalSteps - 1 ? 'none' : 'inline-block'
      submitBtn.style.display = currentStep === totalSteps - 1 ? 'inline-block' : 'none'
    }

    nextBtn.addEventListener('click', () => {
      if (validateStep(currentStep)) {
        currentStep++
        carousel.next()
        updateProgress()
      }
    })

    prevBtn.addEventListener('click', () => {
      currentStep--
      carousel.prev()
      updateProgress()
    })


    function validateStep(step) {
      let isValid = true;
      switch (step) {
        case 0: // Validação Etapa 1 (Dados de Login)
          const nome = document.querySelector('[name="nome_completo"]').value.trim();
          const docType = document.querySelector('[name="tipo_documento"]:checked').value; // CPF ou CNPJ
          const docValue = document.querySelector('[name="documento"]').value.replace(/\D/g, ''); // Remove caracteres não numéricos
          const email = document.getElementById('email').value.trim();
          const senha = document.getElementById('senha').value.trim();
          const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

          if (!nome || nome.split(/\s+/).length < 2) {
            alert('Por favor, insira seu nome completo com pelo menos duas palavras.');
            isValid = false;
          } else if (docType === 'cpf' && !validarCPF(docValue)) {
            alert('CPF inválido.');
            isValid = false;
          } else if (docType === 'cnpj' && !validarCNPJ(docValue)) {
            alert('CNPJ inválido.');
            isValid = false;
          } else if (!emailRegex.test(email)) {
            alert('Por favor, insira um e-mail válido.');
            isValid = false;
          } else if (!senha || senha.length < 6) {
            alert('A senha deve ter pelo menos 6 caracteres.');
            isValid = false;
          }
          break;

        case 1: // Validação Etapa 2 (Dados Pessoais)
          const celular = document.querySelector('[name="telefone_celular"]').value;
          const fixo = document.querySelector('[name="telefone_fixo"]').value;
          const dataNascimento = document.querySelector('[name="data_nascimento"]').value.trim();

          if (!celular || celular.length !== 15) {
            alert('Telefone celular é obrigatório e deve ter 11 dígitos.');
            isValid = false;
          } else if (fixo && fixo.length !== 14) {
            alert('Telefone fixo deve ter 10 dígitos.');
            isValid = false;
          } else if (!dataNascimento) {
            alert('Data de nascimento é obrigatória.');
            isValid = false;
          } else if (!validarIdade(dataNascimento)) {
            isValid = false;
          }
          break;

        case 2: // Validação Etapa 3 (Endereço)
          const requiredFields = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
          for (let field of requiredFields) {
            if (!document.querySelector(`[name="${field}"]`).value.trim()) {
              alert('Preencha todos os campos obrigatórios do endereço');
              isValid = false;
              break;
            }
          }
          break;
      }
      return isValid;
    }
  })

  function abrirCadastro() {
    // Fecha o modal de login (W3.CSS)
    document.getElementById('loginModal').style.display = 'none';

    // Abre o modal de reserva (Bootstrap)
    var cadastroCliente = new bootstrap.Modal(document.getElementById('cadastroCliente'));
    cadastroCliente.show();
  }

  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('backToLogin');
    if (btn) { // Verifica se o botão existe
      btn.addEventListener('click', function (e) {
        e.preventDefault();

        // Fecha o modal de cadastro
        const regEl = document.getElementById('cadastroCliente');
        const regModal = bootstrap.Modal.getOrCreateInstance(regEl);
        regModal.hide();

        // Abre o modal de login
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
          loginModal.style.display = 'block';
        }
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const simplifiedRegisterBtn = document.getElementById('simplifiedRegister');
    const simplifiedRegisterContainer = document.getElementById('simplifiedRegisterContainer');
    const carousel = new bootstrap.Carousel('#formCarousel');
    const progressBar = document.getElementById('progressBar');
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const submitBtn = document.getElementById('submitForm');
    let currentStep = 0;
    const totalSteps = 3;

    // Atualiza a visibilidade do botão "Cadastro Simplificado"
    function updateSimplifiedRegisterVisibility() {
      if (simplifiedRegisterContainer) {
        simplifiedRegisterContainer.style.display = currentStep === 0 ? 'block' : 'none';
      }
    }

    // Atualiza a barra de progresso e os botões
    function updateProgress() {
      const progress = ((currentStep + 1) / totalSteps) * 100;
      progressBar.style.width = `${progress}%`;
      prevBtn.disabled = currentStep === 0;
      nextBtn.style.display = currentStep === totalSteps - 1 ? 'none' : 'inline-block';
      submitBtn.style.display = currentStep === totalSteps - 1 ? 'inline-block' : 'none';

      // Atualiza a visibilidade do botão "Cadastro Simplificado"
      updateSimplifiedRegisterVisibility();
    }

    // Avança para a próxima etapa
    nextBtn.addEventListener('click', () => {
      if (validateStep(currentStep)) {
        currentStep++;
        carousel.next();
        updateProgress();
      }
    });

    // Volta para a etapa anterior
    prevBtn.addEventListener('click', () => {
      currentStep--;
      carousel.prev();
      updateProgress();
    });

    // Inicializa a visibilidade do botão "Cadastro Simplificado"
    updateSimplifiedRegisterVisibility();

    // Evento para o botão "Cadastro Simplificado"
    if (simplifiedRegisterBtn) {
      simplifiedRegisterBtn.addEventListener('click', function () {
        // Captura os dados da primeira etapa
        const nomeCompleto = document.querySelector('[name="nome_completo"]').value.trim();
        const tipoDocumento = document.querySelector('[name="tipo_documento"]:checked').value;
        const documento = document.querySelector('[name="documento"]').value.trim();
        const email = document.querySelector('[name="email"]').value.trim();
        const senha = document.querySelector('[name="senha"]').value.trim();

        // Validação básica
        if (!nomeCompleto || !documento || !email || !senha) {
          alert('Por favor, preencha todos os campos obrigatórios da primeira etapa.');
          return;
        }

        // Envia os dados via AJAX
        fetch('/StayEase-Solutionsv2/Hotel/components/cadastro.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            cadastro_simplificado: true,
            nome_completo: nomeCompleto,
            tipo_documento: tipoDocumento,
            documento: documento,
            email: email,
            senha: senha,
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Erro na resposta do servidor.');
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              alert('Cadastro simplificado realizado com sucesso!');
              window.location.reload();
            } else {
              alert('Erro ao realizar o cadastro simplificado: ' + data.message);
            }
          })
          .catch((error) => {
            console.error('Erro no cadastro simplificado:', error);
            alert('Ocorreu um erro ao realizar o cadastro simplificado.');
          });
      });
    }
  });
</script>