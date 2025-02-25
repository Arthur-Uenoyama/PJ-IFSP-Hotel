<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quartos Disponíveis</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-padding-32">
    <h2 class="w3-center">Quartos Disponíveis</h2>
</div>

<div class="w3-row-padding">
    <?php
    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hotel";

    $conexao = new mysqli($servername, $username, $password, $dbname);

    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Buscar quartos disponíveis
    $sql = "SELECT * FROM quartos WHERE status = 'disponível'";
    $result = $conexao->query($sql);

    if ($result->num_rows > 0):
        while ($quarto = $result->fetch_assoc()):
    ?>
        <div class="w3-third w3-container w3-margin-bottom">
            <div class="w3-card w3-white w3-round">
                <img src="<?php echo isset($quarto['imagem']) ? $quarto['imagem'] : 'img/quarto_padrao.jpg'; ?>" 
                     style="width:100%" class="w3-round">
                <div class="w3-container">
                    <h3><?php echo htmlspecialchars($quarto['tipo']); ?></h3>
                    <p><b>Número:</b> <?php echo htmlspecialchars($quarto['numero']); ?></p>
                    <p><b>Preço:</b> R$ <?php echo number_format($quarto['preco'], 2, ',', '.'); ?></p>
                    <p><b>Descrição:</b> <?php echo htmlspecialchars($quarto['descricao']); ?></p>
                    <a href="reserva.php?quarto=<?php echo $quarto['id']; ?>" class="w3-button w3-green w3-round w3-margin-bottom">Reservar</a>
                </div>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p class='w3-center'>Nenhum quarto disponível no momento.</p>";
    endif;

    // Fechar conexão
    $conexao->close();
    ?>
</div>

</body>
</html>
