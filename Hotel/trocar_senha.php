<?php
include './db/dbHotel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuarioId'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senhaAtual = trim($_POST['senhaAtual']);
    $novaSenha = trim($_POST['novaSenha']);
    $confirmarSenha = trim($_POST['confirmarSenha']);

    // Verifica campos obrigatórios
    if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
        $_SESSION['mensagem'] = "Todos os campos são obrigatórios!";
        header("Location: home.php");
        exit;
    }

    // Verifica tamanho da nova senha
    if (strlen($novaSenha) < 8) {
        $_SESSION['mensagem'] = "A nova senha deve ter no mínimo 8 caracteres!";
        header("Location: home.php");
        exit;
    }

    // Verifica se nova senha confere com a confirmação
    if ($novaSenha !== $confirmarSenha) {
        $_SESSION['mensagem'] = "A confirmação da nova senha não confere!";
        header("Location: home.php");
        exit;
    }

    try {
        // Recupera a senha atual do banco
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['mensagem'] = "Usuário não encontrado.";
            header("Location: home.php");
            exit;
        }

        // Verifica se a senha atual digitada está correta
        if (!password_verify($senhaAtual, $usuario['senha'])) {
            $_SESSION['mensagem'] = "Senha atual incorreta!";
            header("Location: home.php");
            exit;
        }

        // Garante que a nova senha seja diferente da atual
        if (password_verify($novaSenha, $usuario['senha'])) {
            $_SESSION['mensagem'] = "A nova senha não pode ser igual à senha atual!";
            header("Location: home.php");
            exit;
        }

        // Atualiza a senha com hash seguro
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$novaSenhaHash, $usuarioId]);

        $_SESSION['mensagem'] = "Senha alterada com sucesso!";
        header("Location: home.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao atualizar a senha: " . $e->getMessage();
        header("Location: home.php");
        exit;
    }
}
?>
