<?php
include './db/dbHotel.php';

/*print_r(password_hash(123 ?? '', PASSWORD_DEFAULT));
echo"</br>";
print_r(password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT));die();*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados do formulário
    $nome_completo = $_POST['nome_completo'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT);
    $telefone_fixo = $_POST['telefone_fixo'] ?? null;
    $telefone_celular = $_POST['telefone_celular'] ?? null;
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $profissao = $_POST['profissao'] ?? null;
    $nacionalidade = $_POST['nacionalidade'] ?? null;
    $tipo_documento = $_POST['tipo_documento'] ?? '';
    $cpf_cnpj = $_POST['cpf_cnpj'] ?? null;
    $documento_Inde = $_POST['documento_Inde'] ?? null;
    $cep = $_POST['cep'] ?? '';
    $logradouro = $_POST['logradouro'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? null;
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $empresa_trabalha = $_POST['empresa_trabalha'] ?? '';

    // Prepara e executa o insert
    $sql = "INSERT INTO usuarios (
        nome_completo, email, senha, telefone_fixo, telefone_celular,
        data_nascimento, sexo, profissao, nacionalidade, tipo_documento,
        cpf_cnpj, documento_Inde, cep, logradouro, numero, complemento,
        bairro, cidade, estado, empresa_trabalha
    ) VALUES (
        :nome_completo, :email, :senha, :telefone_fixo, :telefone_celular,
        :data_nascimento, :sexo, :profissao, :nacionalidade, :tipo_documento,
        :cpf_cnpj, :documento_Inde, :cep, :logradouro, :numero, :complemento,
        :bairro, :cidade, :estado, :empresa_trabalha
    )";

    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':nome_completo' => $nome_completo,
            ':email' => $email,
            ':senha' => $senha,
            ':telefone_fixo' => $telefone_fixo,
            ':telefone_celular' => $telefone_celular,
            ':data_nascimento' => $data_nascimento,
            ':sexo' => $sexo,
            ':profissao' => $profissao,
            ':nacionalidade' => $nacionalidade,
            ':tipo_documento' => $tipo_documento,
            ':cpf_cnpj' => $cpf_cnpj,
            ':documento_Inde' => $documento_Inde,
            ':cep' => $cep,
            ':logradouro' => $logradouro,
            ':numero' => $numero,
            ':complemento' => $complemento,
            ':bairro' => $bairro,
            ':cidade' => $cidade,
            ':estado' => $estado,
            ':empresa_trabalha' => $empresa_trabalha
        ]);
        header("Location: ./index.php");
    } catch (PDOException $e) {
        echo "Erro ao cadastrar usuário: " . $e->getMessage();
    }
}
?>
