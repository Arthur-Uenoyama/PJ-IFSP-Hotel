<?php
session_start();
require_once "conexao.php"; // ajuste conforme seu arquivo de conexão

$erroLogin = "";
$erroCadastro = "";

// VERIFICAR se foi enviado POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // PROCESSAR LOGIN
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Verificar na tabela 'usuarios'
        $stmtCliente = $pdo->prepare("SELECT id, nome_completo, email, senha FROM usuarios WHERE email = ?");
        $stmtCliente->execute([$email]);
        $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

        // Verificar na tabela 'funcionarios'
        $stmtFuncionario = $pdo->prepare("SELECT id, nome, email, senha, cargo FROM funcionarios WHERE email = ?");
        $stmtFuncionario->execute([$email]);
        $funcionario = $stmtFuncionario->fetch(PDO::FETCH_ASSOC);

        if ($cliente && password_verify($senha, $cliente['senha'])) {
            $_SESSION['clienteId'] = $cliente['id'];
            $_SESSION['clienteNome'] = $cliente['nome_completo'];
            $_SESSION['usuarioTipo'] = 'cliente';
            header("Location: home.php");
            exit;
        } elseif ($funcionario && password_verify($senha, $funcionario['senha'])) {
            $_SESSION['clienteId'] = $funcionario['id'];
            $_SESSION['clienteNome'] = $funcionario['nome'];
            $_SESSION['usuarioTipo'] = 'funcionario';
            header("Location: painel_funcionario.php");
            exit;
        } else {
            $erroLogin = "Email ou senha inválidos.";
        }
    }

    // CADASTRO DE USUÁRIO
    elseif (isset($_POST['cadastro'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $telefone_fixo = $_POST['telefone_fixo'] ?? null;
        $telefone_celular = $_POST['telefone_celular'] ?? null;
        $data_nascimento = $_POST['data_nascimento'];
        $sexo = $_POST['sexo'];
        $profissao = $_POST['profissao'] ?? null;
        $nacionalidade = $_POST['nacionalidade'] ?? null;
        $tipo_documento = $_POST['tipo_documento'];
        $cpf_cnpj = $_POST['cpf_cnpj'];
        $documento_inde = $_POST['documento_inde'] ?? null;
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'] ?? null;
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $empresa_trabalha = $_POST['empresa_trabalha'];

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios 
                (nome_completo, email, senha, telefone_fixo, telefone_celular, data_nascimento, sexo, profissao, nacionalidade, tipo_documento, cpf_cnpj, documento_inde, cep, logradouro, numero, complemento, bairro, cidade, estado, empresa_trabalha)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nome, $email, $senha, $telefone_fixo, $telefone_celular, $data_nascimento, $sexo,
                $profissao, $nacionalidade, $tipo_documento, $cpf_cnpj, $documento_inde, $cep,
                $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $empresa_trabalha
            ]);
            $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Faça login.";
        } catch (PDOException $e) {
            $erroCadastro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}

// SE ESTIVER LOGADO
if (isset($_SESSION['clienteId'])) {
    $clienteId = $_SESSION['clienteId'];
    $usuarioTipo = $_SESSION['usuarioTipo'];

    try {
        if ($usuarioTipo === 'cliente') {
            $stmt = $pdo->prepare("SELECT id, nome_completo, email FROM usuarios WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("SELECT id, nome, email FROM funcionarios WHERE id = ?");
        }
        $stmt->execute([$clienteId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao carregar dados: " . $e->getMessage();
    }

    // CARREGAR QUARTOS (se funcionário)
    $quartos = [];
    $termo = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

    if ($usuarioTipo === 'funcionario') {
        $sql = "SELECT q.id AS QuartoId, q.numero, q.preco, q.status, 
                       u.nome_completo AS ClienteNome, r.data_checkin, r.data_checkout
                FROM quartos q
                LEFT JOIN reservas r ON q.id = r.quarto_id
                LEFT JOIN usuarios u ON r.usuario_id = u.id";

        if (!empty($termo)) {
            $sql .= " WHERE u.nome_completo LIKE :termo";
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
    }
}
?>
