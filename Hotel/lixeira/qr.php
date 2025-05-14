<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Reserva</title>
    <!-- Adicionar o Bootstrap para o modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Adicionar a biblioteca QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
</head>
<body class="bg-light">

    <!-- Botão para abrir o modal -->
    <button type="button" class="btn btn-danger" onclick="abrirModalFinalizacao()">Finalizar Reserva</button>

    <!-- Modal de Finalizar Reserva -->
    <div class="modal fade" id="finalizarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Finalização da Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Você tem certeza que deseja finalizar esta reserva?</p>
                    <p><strong>Nome do Cliente:</strong> <span id="clienteNome"></span></p>
                    <p><strong>Data de Check-out:</strong> <span id="checkOutData"></span></p>
                    <p><strong>Quarto:</strong> <span id="numeroQuarto"></span></p>

                    <!-- Div para o QR Code Pix -->
                    <div id="qrcode" style="text-align: center; padding: 20px;"></div>
                    <p style="text-align: center; margin-top: 10px;">Escaneie o QR Code para realizar o pagamento via Pix</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarFinalizacao" onclick="finalizarReserva()">Finalizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adicionar o Bootstrap e o JavaScript para o funcionamento do modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Função para abrir o modal e gerar o QR Code com dados estáticos
        function abrirModalFinalizacao() {
            // Dados estáticos para o teste
            var nomeCliente = 'João Silva';
            var dataCheckOut = '10/03/2025';
            var numeroQuarto = '101';
            var chavePix = 'seu@pix.com.br'; // Substitua pela sua chave Pix
            var valor = 150.00;  // Valor da reserva, em R$
            var descricao = 'Pagamento reserva hotel';  // Descrição do pagamento
            var nomeBeneficiario = 'Hotel XYZ';  // Nome do beneficiário
            var cidade = 'Cidade ABC';  // Cidade do beneficiário

            // Preencher os dados no modal com informações estáticas
            document.getElementById('clienteNome').textContent = nomeCliente;
            document.getElementById('checkOutData').textContent = dataCheckOut;
            document.getElementById('numeroQuarto').textContent = numeroQuarto;

            // Montar o código Pix (Payload)
            var payload = `00020126580014BR.GOV.BCB.PIX0114${chavePix}520400005303986540${valor.toFixed(2).replace('.', '')}5802BR5915${nomeBeneficiario}6009${cidade}62130505${descricao}6304`; 

            // Verificar se o elemento do QR Code existe antes de tentar gerar o QR Code
            var qrcodeContainer = document.getElementById('qrcode');
            if (qrcodeContainer) {
                // Limpar qualquer QR Code anterior
                qrcodeContainer.innerHTML = ""; 

                // Gerar o QR Code no canvas
                var canvas = document.createElement('canvas'); // Criando o canvas para o QR Code
                qrcodeContainer.appendChild(canvas); // Adicionar o canvas ao container

                QRCode.toCanvas(canvas, payload, { width: 300, margin: 2 }, function(error) {
                    if (error) {
                        console.error("Erro ao gerar QR Code:", error);
                    } else {
                        console.log("QR Code gerado com sucesso!");
                    }
                });
            } else {
                console.error("Elemento para QR Code não encontrado!");
            }

            // Abrir o modal
            var modalElement = document.getElementById('finalizarModal');
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        // Função para finalizar a reserva
        function finalizarReserva() {
            // Aqui você pode adicionar a lógica para finalizar a reserva
            alert('Reserva finalizada com sucesso!');
            
            // Fechar o modal após finalizar
            var modalElement = document.getElementById('finalizarModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        }
    </script>

</body>
</html>
