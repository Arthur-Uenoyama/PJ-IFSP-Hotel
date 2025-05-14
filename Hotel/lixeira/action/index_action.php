<?php
session_start();
require_once "conexao.php"; // Certifique-se de que este arquivo conecta ao banco e define $pdo

$erroLogin = "";
$erroCadastro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['login'])) {
    // Processar Login
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT id, nome_completo, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
      $_SESSION['clienteId'] = $usuario['id'];
      $_SESSION['clienteNome'] = $usuario['nome_completo'];
      $_SESSION['usuarioTipo'] = 'cliente';
      header("Location: home.php");
      exit;
    } else {
      $erroLogin = "Email ou senha inválidos.";
    }
  }

  elseif (isset($_POST['cadastro'])) {
    // Processar Cadastro
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $cpf_cnpj = $_POST['cpf_cnpj'];
    $documento_inde = $_POST['documento_inde'] ?? null;
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $empresa_trabalha = $_POST['empresa_trabalha'];

    $stmt = $pdo->prepare("INSERT INTO usuarios 
      (nome_completo, email, senha, telefone_celular, data_nascimento, sexo, cpf_cnpj, documento_inde, cep, logradouro, numero, bairro, cidade, estado, empresa_trabalha)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $nome, $email, $senha, $telefone, $data_nascimento, $sexo, $cpf_cnpj, $documento_inde,
      $cep, $logradouro, $numero, $bairro, $cidade, $estado, $empresa_trabalha
    ]);

    $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Faça login.";
  }
}
?>
