<?php
include './db/dbHotel.php';

$conn = $pdo ?? $GLOBALS['pdo'];

//echo"<pre>";print_r($_POST);echo"<hr>";print_r($_FILES);die();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acao = $_POST['acao'] ?? '';
        $tabela = $_POST['tabela'] ?? '';
        
        $tabelasPermitidas = ['hoteis', 'quartos', 'funcionarios', 'usuarios', 'reservas'];
        if (!in_array($tabela, $tabelasPermitidas)) {
            throw new Exception('Tabela não permitida');
        }

        cadastroOuUpdate($conn, $tabela, $_POST, $_FILES);
    } catch (Exception $e) {
        error_log("Erro no processamento: " . $e->getMessage());
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?erro=1");
        exit;
    }
}

function processarImagens($dados, $arquivos) {
    $imagens = [];

    if (!empty($dados['imagens_existentes']) && is_array($dados['imagens_existentes'])) {
        foreach ($dados['imagens_existentes'] as $img) {
            $nome = basename($img);
            if (file_exists("../Hotel/uploads/quartos/$nome")) {
                $imagens[] = $nome;
            }
        }
    }

    if (!empty($arquivos['imagem']['name'][0])) {
        $uploadDir = './uploads/quartos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($arquivos['imagem']['tmp_name'] as $i => $tmp) {
            if ($arquivos['imagem']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $mime = mime_content_type($tmp);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) continue;

            $ext = strtolower(pathinfo($arquivos['imagem']['name'][$i], PATHINFO_EXTENSION));
            $novoNome = 'quartos_' . uniqid() . '.' . $ext;

            if (move_uploaded_file($tmp, $uploadDir . $novoNome)) {
                $imagens[] = $novoNome;
            }
        }
    }

    return $imagens;
}

function cadastroOuUpdate($conn, $tabela, $postData) {
    $id = $postData['id'] ?? null;
    $arquivos = $_FILES ?? [];

    unset($postData['acao'], $postData['tabela'], $postData['origem'], $postData['imagens_existentes'], $postData['imagens_remover']);

    if ($tabela === 'quartos') {
        $imagens = processarImagens($postData, $arquivos);
        $postData['imagem'] = !empty($imagens) ? json_encode($imagens, JSON_UNESCAPED_UNICODE) : null;
    }

    foreach ($postData as $key => &$value) {
        if (is_array($value)) {
            if ($key === 'regras' || $key === 'comodidades') {
                $value = json_encode(array_values(array_filter($value, 'trim')), JSON_UNESCAPED_UNICODE);
            } else {
                $value = implode(',', $value);
            }
        }

        if ($key === 'senha' && !empty($value)) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }

        if ($key === 'salario' || $key === 'preco') {
            $value = formatarSalarioParaBanco($value);
        }
    }
    unset($value);

    $params = [];
    foreach ($postData as $key => $value) {
        $params[":$key"] = $value;
    }

    if (!empty($id)) {
        $postData['id'] = $id;
        $params[':id'] = $id;

        $setParts = [];
        foreach ($postData as $key => $value) {
            if ($key !== 'id') {
                $setParts[] = "$key = :$key";
            }
        }
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE $tabela SET $setClause WHERE id = :id";
    } else {
        $columns = implode(', ', array_keys($postData));
        $placeholders = ':' . implode(', :', array_keys($postData));
        $sql = "INSERT INTO $tabela ($columns) VALUES ($placeholders)";
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (PDOException $e) {
        echo "<pre>";
        echo "Erro PDO: " . $e->getMessage() . "\n";
        echo "SQL: $sql\n";
        echo "Params: " . print_r($params, true);
        echo "</pre>";
        exit;
    }
}

function processarUploadImagens($imagens, $tabela) {
    $destino = "../Hotel/uploads/$tabela/";
    $arquivosSalvos = [];

    if (!file_exists($destino)) {
        mkdir($destino, 0755, true);
    }

    foreach ($imagens['tmp_name'] as $index => $tmp) {
        if ($imagens['error'][$index] !== UPLOAD_ERR_OK) continue;

        $mime = mime_content_type($tmp);
        if (!in_array($mime, ['image/jpeg', 'image/png'])) continue;

        $ext = strtolower(pathinfo($imagens['name'][$index], PATHINFO_EXTENSION));
        $nomeArquivo = $tabela . '_' . uniqid() . '.' . $ext;

        if (move_uploaded_file($tmp, $destino . $nomeArquivo)) {
            $arquivosSalvos[] = $nomeArquivo;
            error_log("Imagem salva: $nomeArquivo");
        }
    }

    return $arquivosSalvos;
}

function formatarSalarioParaBanco($valor) {
    if (is_numeric($valor)) return (float)$valor;
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (float)$valor;
}
