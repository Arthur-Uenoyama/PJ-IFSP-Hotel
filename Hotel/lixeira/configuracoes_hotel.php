<?php
session_start();

if (!isset($_SESSION['usuarioId'])) {
    header("Location: ../index.php");
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$usuarioTipo = $_SESSION['usuarioTipo'];

if ($usuarioTipo !== 'hotel') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar Hotel - Apê Pousada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar Customization */
        #mySidebar {
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #343a40;
            color: white;
            transition: 0.3s;
            padding-top: 20px;
            z-index: 1050;
        }
        #mySidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
        }
        #mySidebar a:hover {
            background-color: #495057;
            color: #f8f9fa;
        }
        #sidebarClose {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            padding: 10px;
        }
        #sidebarClose:hover {
            background-color: #495057;
            color: #f8f9fa;
            border-radius: 5px;
        }
        /* Main Content */
        .container {
            margin-top: 80px;
        }

        .btn-outline-dark {
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            border-radius: 20px;
            padding: 10px 30px;
            font-weight: bold;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #343a40;
            font-weight: bold;
        }
        hr {
            border-top: 1px solid #ddd;
        }
        /* Responsiveness */
        @media (max-width: 768px) {
            #mySidebar {
                width: 200px;
            }
            .container {
                margin-top: 20px;
            }
        }
        /* Reduced input field size */
        .form-group .form-control {
            max-width: 300px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-light">
    <?php include("./components/navbar.php"); ?>

    <div class="container">
        <h1 class="text-center mb-4">Cadastro do Hotel</h1>
        <div class="card p-4">
            <form method="POST" action="processar_hotel.php">
                <div class="mb-3">
                    <label for="nome_hotel" class="form-label">Nome do Hotel</label>
                    <input type="text" class="form-control" id="nome_hotel" name="nome_hotel" required>
                </div>
                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição do Hotel</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="servicos" class="form-label">Serviços Oferecidos</label>
                    <textarea class="form-control" id="servicos" name="servicos" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="politica" class="form-label">Política do Hotel</label>
                    <textarea class="form-control" id="politica" name="politica" rows="3" required></textarea>
                </div>
                <h2 class="text-center mt-4">Formas de Pagamento</h2>
                <hr>
                <div class="mb-3">
                    <label for="pagamento" class="form-label">Detalhes das Formas de Pagamento</label>
                    <textarea class="form-control" id="pagamento" name="pagamento" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="chave_pix" class="form-label">Chave PIX</label>
                    <input type="text" class="form-control" id="chave_pix" name="chave_pix" required>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100 mt-4">Cadastrar Hotel</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function w3_openSidebar() {
            document.getElementById("mySidebar").style.left = "0";
        }

        function w3_closeSidebar() {
            document.getElementById("mySidebar").style.left = "-250px";
        }
    </script>
</body>
</html>
