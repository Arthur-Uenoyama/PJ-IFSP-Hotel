<?php
session_start();
$_SESSION['logado'] = false;
session_destroy(); // Destroi todas as sessões
header("Location: index.php"); // Redireciona para a página inicial
exit;
?>
