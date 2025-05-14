<?php

include('.././db/dbHotel.php');

class Cadastros
{

  /**
 * Função para cadastrar dados no banco a partir de um array de dados
 * 
 * @param string $tabela Nome da tabela onde os dados serão inseridos
 * @param array $dados Array com os dados a serem inseridos
 * @param PDO|null $pdo Conexão PDO opcional (pega a global se não informada)
 * 
 * @return array Retorna um array com ['status' => bool, 'mensagem' => string, 'id' => int|null]
 */
function cadastrarViaJson($tabela, $dados, $pdo = null)
{
    // Usa a conexão global se nenhuma for passada
    $conn = $pdo ?? $GLOBALS['pdo'];

    try {
        // Prepara os campos e placeholders
        $campos = implode(', ', array_keys($dados));
        $placeholders = ':' . implode(', :', array_keys($dados));

        // Prepara e executa a query
        $sql = "INSERT INTO $tabela ($campos) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        //die("teste query: " . $stmt);
        $stmt->execute($dados);

        // Obtém o ID do último registro inserido
        $id = $conn->lastInsertId();

        return [
            'status' => true,
            'mensagem' => 'Cadastro realizado com sucesso',
            'id' => $id
        ];

    } catch (PDOException $e) {
        return [
            'status' => false,
            'mensagem' => 'Erro no cadastro: ' . $e->getMessage(),
            'id' => null
        ];
    }
}



}
