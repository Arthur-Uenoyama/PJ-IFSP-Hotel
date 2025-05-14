<?php
header('Content-Type: application/json');

require_once 'insert_dados.php'; // Inclui o arquivo com a classe Cadastros

$input = file_get_contents('php://input');
$dados = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'status' => false,
        'mensagem' => 'Erro ao decodificar JSON: ' . json_last_error_msg()
    ]);
    exit;
}

try {
    // Instancia a classe Cadastros
    $cadastros = new Cadastros();
    
    // Define a tabela de destino (ajuste conforme sua necessidade)
    $tabela = 'quartos'; // ou outra tabela conforme seu caso
    
    // Chama o método cadastrarViaJson com os dados recebidos
    $resultado = $cadastros->cadastrarViaJson($tabela, $dados);
    
    // Verifica o resultado
    if ($resultado['status']) {
        $resposta = [
            'status' => true,
            'mensagem' => $resultado['mensagem'],
            'id_cadastro' => $resultado['id'],
            'metadados' => [
                'metodo' => $_SERVER['REQUEST_METHOD'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    } else {
        $resposta = [
            'status' => false,
            'mensagem' => $resultado['mensagem'],
            'dados_recebidos' => $dados // Opcional: incluir dados para debug
        ];
    }
    
} catch (Exception $e) {
    $resposta = [
        'status' => false,
        'mensagem' => 'Erro no sistema: ' . $e->getMessage(),
        'dados_recebidos' => $dados // Opcional: incluir dados para debug
    ];
}

// Log simplificado (opcional)
file_put_contents('debug_recebimento.log', 
    date('Y-m-d H:i:s')." - ".json_encode([
        'dados' => $dados,
        'resultado' => $resultado ?? null
    ]).PHP_EOL, 
    FILE_APPEND);

echo json_encode($resposta, JSON_PRETTY_PRINT);
?>
