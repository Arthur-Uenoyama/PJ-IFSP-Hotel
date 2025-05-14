<?php
session_start();
include './db/dbHotel.php';

header('Content-Type: application/json'); // Definindo o tipo de resposta como JSON

$response = ['success' => false, 'message' => ''];

// Validação dos campos de entrada
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validações básicas
if (empty($email) || empty($senha)) {
    $response['message'] = 'Por favor, preencha todos os campos.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Por favor, insira um e-mail válido.';
    echo json_encode($response);
    exit;
}

$conn = $pdo ?? $GLOBALS['pdo'];

try {
    // Tenta autenticar como FUNCIONÁRIO primeiro
    $sqlFunc = "SELECT id, nome, email, cargo, senha FROM funcionarios WHERE email = ?";
    $stmtFunc = $conn->prepare($sqlFunc);
    $stmtFunc->execute([$email]);
    $funcionario = $stmtFunc->fetch(PDO::FETCH_ASSOC);

    if ($funcionario) {
        if (password_verify($senha, $funcionario['senha']) || hash('sha256', $senha) === $funcionario['senha']) {
            // Login bem-sucedido - funcionário
            $_SESSION['id'] = $funcionario['id'];
            $_SESSION['nome'] = $funcionario['nome'];
            $_SESSION['email'] = $funcionario['email'];
            $_SESSION['cargo'] = $funcionario['cargo'];
            $_SESSION['tipo'] = 'funcionario';
            
            $response['success'] = true;
            $response['redirect'] = 'home.php';
            echo json_encode($response);
            exit;
        }
    }

    // Tenta como USUÁRIO/CLIENTE
    $sqlUser = "SELECT id, nome_completo as nome, email, senha FROM usuarios WHERE email = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->execute([$email]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha']) || hash('sha256', $senha) === $usuario['senha']) {
            // Login bem-sucedido - cliente
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['tipo'] = 'cliente';
            
            $response['success'] = true;
            $response['redirect'] = './hospedes/minhas_reservas_cliente.php';
            echo json_encode($response);
            exit;
        }
    }

    // Credenciais inválidas (genérico por segurança)
    $response['message'] = 'E-mail ou senha incorretos.';
    echo json_encode($response);
    exit;

} catch (PDOException $e) {
    error_log('Erro de login: ' . $e->getMessage());
    $response['message'] = 'Ocorreu um erro inesperado. Tente novamente.';
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    error_log('Erro geral: ' . $e->getMessage());
    $response['message'] = 'Erro inesperado. Contate o suporte.';
    echo json_encode($response);
    exit;
}
?>
