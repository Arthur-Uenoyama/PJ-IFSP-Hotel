<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

include(__DIR__ . '/.././db/dbHotel.php');

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Reserva - Apê Pousada</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/cadastro_reserva.css">
</head>

<body class="bg-light">
    <?php include("../components/navbar.php"); ?>

    <div class="container mt-5 container-main bg-light">
        <h1 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Nova Reserva</h1>

        <form method="POST" action="./funcoes.php" onsubmit="return validarFormulario(event)">
            <!-- Seção de Dados do Cliente -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="fas fa-user-check me-2"></i>Dados do Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="suggestions-container">
                            <input type="hidden" id="cliente_id" name="cliente_id">
                            <input type="hidden" name="tabela" value="reserva">
                            <input type="hidden" id="quartos_selecionados" name="quartos_selecionados">
                            <input type="hidden" id="valores_selecionados" name="valores_selecionados">
                            <input type="hidden" id="quartos_hospedes" name="quartos_hospedes" value="">
                            <input type="hidden" id="quarto_principal_selecionado" name="quarto_principal" value="">
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <div id="clienteSelecionadoContainer"
                                    class="d-none align-items-center px-3 bg-light rounded">
                                    <span id="nomeClienteSelecionado"></span>
                                    <input type="hidden" id="documento_real" name="documento">
                                </div>
                                <input type="text" class="form-control" id="documento_visual"
                                    placeholder="Digite o CPF/CNPJ do cliente" oninput="buscarSugestoes(this.value)"
                                    required>
                                <button class="btn btn-outline-secondary" type="button" onclick="limparCliente()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="suggestionsDropdown" class="suggestions-dropdown"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#cadastroCliente">
                            <i class="fas fa-user-plus me-2"></i>Novo Cliente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Seção de Datas -->
            <div class="card-section">
    <h5 class="text-green mb-4"><i class="fas fa-calendar-alt me-2"></i>Período da Reserva</h5>
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Check-in</label>
            <input type="date" class="form-control" id="data_checkin" name="data_checkin"
                min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+2 years')); ?>"
                required onchange="definirPeriodoCheckout()">
        </div>
        <div class="col-md-4">
            <label class="form-label">Check-out</label>
            <input type="date" class="form-control" id="data_checkout" name="data_checkout" disabled required>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-success w-100" onclick="verificarDisponibilidade()">
                <i class="fas fa-search me-2"></i>Verificar Disponibilidade
            </button>
        </div>
    </div>
</div>

            <!-- Seção de Quartos -->
            <div class="card-section" id="secao-quartos" style="display: none;">
                <h5 class="text-green mb-4"><i class="fas fa-bed me-2"></i>Quartos Disponíveis</h5>
                <div class="quartos-container" id="quartos-container"></div>
            </div>

            <!-- Seção de Valores -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-cash-coin me-2"></i>Valores</h5>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Valor Total</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="valor_total" name="valor_total" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valor Já Pago</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="valor_pago" name="valor_pago">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select class="form-select" id="pagamento" name="pagamento" required
                            onchange="toggleParcelas()">
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartaoCredito">Cartão de Crédito</option>
                            <option value="ted">TED/Transferencia</option>
                            <option value="cartaoDebito">Cartão de debito</option>
                            <option value="boleto">Boleto</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="parcelasDiv" style="display: none;">
                        <label class="form-label">Parcelas</label>
                        <input type="number" class="form-control" id="parcelas" name="parcelas" min="1" max="12"
                            value="1" oninput="calcularParcelas()">
                    </div>
                    <div class="col-md-3" id="valorParcelaDiv" style="display: none;">
                        <label class="form-label">Valor por Parcela</label>
                        <input type="text" class="form-control" id="valor_parcela" readonly
                            style="background-color: #f8f9fa;">
                    </div>
                </div>
            </div>

            <!-- Seção de Hóspedes Secundários -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="bi bi-people-fill me-2"></i>Hóspedes Secundários</h5>
                <button type="button" class="btn btn-outline-success w-100" onclick="adicionarHospede()">
                    <i class="bi bi-plus-circle me-2"></i>Adicionar Hóspede
                </button>
                <div class="list-group mb-3" id="hospedesList">
                    <!-- Itens serão adicionados dinamicamente -->
                </div>
            </div>

            <!-- Seção de Observações -->
            <div class="card-section">
                <h5 class="text-green mb-4"><i class="fas fa-comment-alt me-2"></i>Observações</h5>
                <textarea class="form-control" name="obs" rows="3"
                    placeholder="Informações adicionais sobre a reserva..."></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-4">
                <i class="fas fa-check-circle me-2"></i>Confirmar Reserva
            </button>
        </form>
    </div>

    <?php include("../components/footer.php"); ?>

    <script>
    // Variáveis globais
    let quartosSelecionados = [];
    let todosQuartosDisponiveis = [];
    let quartoPrincipalSelecionado = null;

    // Funções de Cliente
    function buscarSugestoes(valor) {
        if (valor.length < 3) {
            document.getElementById('suggestionsDropdown').style.display = 'none';
            return;
        }

        fetch(`buscar_clientes.php?termo=${encodeURIComponent(valor)}`)
            .then(response => response.json())
            .then(data => exibirSugestoes(data))
            .catch(error => {
                console.error('Erro na busca:', error);
                document.getElementById('suggestionsDropdown').style.display = 'none';
            });
    }

    function exibirSugestoes(clientes) {
        const dropdown = document.getElementById('suggestionsDropdown');
        dropdown.innerHTML = '';

        if (!Array.isArray(clientes)) {
            dropdown.style.display = 'none';
            return;
        }

        clientes.forEach(cliente => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            const nome = cliente.nome_completo || '[Nome não disponível]';
            const doc = cliente.cpf_cnpj ? ` (${cliente.cpf_cnpj})` : '';

            item.innerHTML = `<div><strong>${nome}</strong></div><div class="text-muted small">${doc}</div>`;
            item.addEventListener('click', () => selecionarCliente(cliente));
            dropdown.appendChild(item);
        });

        dropdown.style.display = 'block';
    }

    function selecionarCliente(cliente) {
        document.getElementById('documento_visual').classList.add('d-none');
        document.getElementById('nomeClienteSelecionado').textContent = cliente.nome_completo || 'Cliente Selecionado';
        document.getElementById('cliente_id').value = cliente.id;
        document.getElementById('documento_real').value = cliente.cpf_cnpj || '';
        document.getElementById('clienteSelecionadoContainer').classList.remove('d-none');
        document.getElementById('suggestionsDropdown').style.display = 'none';

        const btnLimpar = document.querySelector('.input-group button');
        btnLimpar.classList.remove('btn-outline-secondary');
        btnLimpar.classList.add('btn-danger');
        btnLimpar.innerHTML = '<i class="fas fa-times me-1"></i> Limpar';
    }

    function limparCliente() {
        document.getElementById('documento_visual').classList.remove('d-none');
        document.getElementById('documento_visual').value = '';
        document.getElementById('clienteSelecionadoContainer').classList.add('d-none');
        document.getElementById('cliente_id').value = '';
        document.getElementById('documento_real').value = '';

        const btnLimpar = document.querySelector('.input-group button');
        btnLimpar.classList.remove('btn-danger');
        btnLimpar.classList.add('btn-outline-secondary');
        btnLimpar.innerHTML = '<i class="fas fa-times"></i>';

        document.getElementById('suggestionsDropdown').style.display = 'none';
    }

    function definirPeriodoCheckout() {
    const checkinInput = document.getElementById('data_checkin');
    const checkoutInput = document.getElementById('data_checkout');
    
    if (checkinInput.value) {
        // Habilita o campo check-out
        checkoutInput.disabled = false;
        
        // Calcula as datas mínima e máxima
        const checkinDate = new Date(checkinInput.value);
        
        // Data MÍNIMA (dia seguinte ao check-in)
        const minDate = new Date(checkinDate);
        minDate.setDate(minDate.getDate() + 1);
        
        // Data MÁXIMA (1 mês após check-in)
        const maxDate = new Date(checkinDate);
        maxDate.setMonth(maxDate.getMonth() + 1);
        
        // Formata para YYYY-MM-DD
        checkoutInput.min = minDate.toISOString().split('T')[0];
        checkoutInput.max = maxDate.toISOString().split('T')[0];
        
        // Define o valor inicial como a data mínima (dia seguinte)
        checkoutInput.value = checkoutInput.min;
        
        // Foca automaticamente no campo check-out para melhor UX
        checkoutInput.focus();
    } else {
        // Desabilita se não houver check-in
        checkoutInput.disabled = true;
        checkoutInput.value = '';
    }
}


    // Funções de Quartos - Versão Corrigida
    function verificarDisponibilidade() {
        // Resetar seleções ao verificar disponibilidade
        quartosSelecionados = [];
        quartoPrincipalSelecionado = null;
        document.getElementById('quarto_principal_selecionado').value = "";

        // Remover classes de seleção de todos os quartos
        document.querySelectorAll('.quarto').forEach(quarto => {
            quarto.classList.remove('selecionado', 'por-clique', 'quarto-principal-selecionado');
        });

        const btn = document.querySelector('button[onclick="verificarDisponibilidade()"]');
        const originalText = btn.innerHTML;

        const checkin = document.getElementById('data_checkin');
    const checkout = document.getElementById('data_checkout');
    
    if (!checkin.value) {
        alert('Por favor, selecione a data de check-in primeiro');
        checkin.focus();
        return false;
    }
    
    if (!checkout.value) {
        alert('Por favor, selecione a data de check-out');
        checkout.focus();
        return false;
    }
    
    // Verifica se o checkout está dentro do período permitido
    const checkinDate = new Date(checkin.value);
    const checkoutDate = new Date(checkout.value);
    const maxDate = new Date(checkinDate);
    maxDate.setMonth(maxDate.getMonth() + 1);
    
    if (checkoutDate < checkinDate) {
        alert('A data de check-out não pode ser anterior ao check-in');
        checkout.focus();
        return false;
    }
    
    if (checkoutDate > maxDate) {
        alert('O período máximo de reserva é de 1 mês a partir da data de check-in');
        checkout.focus();
        return false;
    }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Buscando...';
        btn.disabled = true;

        fetch(`buscar_quartos.php?checkin=${checkin}&checkout=${checkout}`)
            .then(response => response.json())
            .then(data => {
                if (!data.quartos || data.quartos.length === 0) {
                    throw new Error("Nenhum quarto disponível");
                }

                // Atualiza a lista de quartos disponíveis
                todosQuartosDisponiveis = data.quartos.map(quarto => ({
                    id: quarto.id || quarto.numero,
                    numero: quarto.numero,
                    preco: parseFloat(quarto.preco)
                }));

                // Mantém os quartos já selecionados que ainda estão disponíveis
                const quartosClique = quartosSelecionados.filter(q => q.origem === "clique");
                if (quartosClique.length > 1) {
                    // Remove os quartos por clique adicionais
                    quartosSelecionados = quartosSelecionados.filter(q => q.origem !== "clique");
                    quartosSelecionados.push(quartosClique[0]); // Mantém apenas o primeiro
                }

                criarQuadradosQuartos(todosQuartosDisponiveis);
                document.getElementById('secao-quartos').style.display = 'block';
                atualizarValorTotal();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert(error.message);
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
            console.log('Verificando disponibilidade de', checkin, 'até', checkout);
    }

    function criarQuadradosQuartos(quartos) {
        const container = document.getElementById('quartos-container');
        container.innerHTML = '';

        quartos.forEach(quarto => {
            const div = document.createElement('div');
            div.className = 'quarto disponivel';
            div.dataset.id = quarto.id;
            div.dataset.numero = quarto.numero;
            div.dataset.preco = quarto.preco;
            div.innerHTML = `<div>${quarto.numero}</div>`;

            // Verifica se o quarto já está selecionado
            if (quartosSelecionados.some(q => q.id === quarto.id)) {
                div.classList.add('selecionado');
            }

            div.addEventListener('click', function() {
                toggleSelecaoQuarto(this);
            });

            container.appendChild(div);
        });
    }

    function toggleSelecaoQuarto(quartoElement) {
        const quartoId = quartoElement.dataset.id;

        // Se já está selecionado como principal, desseleciona
        if (quartoPrincipalSelecionado === quartoId) {
            quartoElement.classList.remove('selecionado', 'por-clique', 'quarto-principal-selecionado');
            quartosSelecionados = quartosSelecionados.filter(q => q.id !== quartoId);
            quartoPrincipalSelecionado = null;
            document.getElementById('quarto_principal_selecionado').value = "";
        }
        // Se é uma nova seleção
        else {
            // Remove a seleção anterior se existir
            if (quartoPrincipalSelecionado) {
                const quartoAnterior = document.querySelector(`.quarto[data-id="${quartoPrincipalSelecionado}"]`);
                if (quartoAnterior) {
                    quartoAnterior.classList.remove('selecionado', 'por-clique', 'quarto-principal-selecionado');
                }
                quartosSelecionados = quartosSelecionados.filter(q => q.id !== quartoPrincipalSelecionado);
            }

            // Adiciona a nova seleção
            quartoPrincipalSelecionado = quartoId;
            quartosSelecionados.push({
                id: quartoId,
                numero: quartoElement.dataset.numero,
                preco: parseFloat(quartoElement.dataset.preco),
                origem: "clique"
            });

            // Aplica estilos visuais
            quartoElement.classList.add('selecionado', 'por-clique', 'quarto-principal-selecionado');
            document.getElementById('quarto_principal_selecionado').value = quartoId;
        }

        atualizarCamposOcultos();
        atualizarValorTotal();
        atualizarDropdownsHospedes();
    }

    function atualizarDropdownsHospedes() {
        document.querySelectorAll('.select-quarto').forEach(select => {
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                if (option.value) {
                    // Habilita todas as opções primeiro
                    option.disabled = false;

                    // Desabilita quartos já selecionados (exceto o próprio)
                    if (quartosSelecionados.some(q => q.id === option.value) && option.value !==
                        currentValue) {
                        option.disabled = true;
                    }
                }
            });
        });
    }

    function atualizarCamposOcultos() {
        // Quarto principal
        const quartoPrincipal = quartosSelecionados.find(q => q.origem === "clique");
        document.getElementById('quartos_selecionados').value = quartoPrincipal ? quartoPrincipal.numero : "";

        // Quartos dos hóspedes
        const quartosHospedes = quartosSelecionados
            .filter(q => q.origem === "select")
            .map(q => q.numero);
        document.getElementById('quartos_hospedes').value = quartosHospedes.join(',');

        // Valores
        const valores = quartosSelecionados.map(q => q.preco.toFixed(2));
        document.getElementById('valores_selecionados').value = valores.join(',');

        // Campo do quarto principal (novo)
        document.getElementById('quarto_principal_selecionado').value = quartoPrincipal ? quartoPrincipal.id : "";
    }

    function atualizarValorTotal() {
        const valorTotal = quartosSelecionados.reduce((total, quarto) => total + quarto.preco, 0);

        document.getElementById('valor_total').value = valorTotal.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

        if (document.getElementById('parcelasDiv').style.display !== 'none') {
            calcularParcelas();
        }
    }

    // Funções de Hóspedes - Versão Corrigida
    function adicionarHospede() {
        const index = document.querySelectorAll('.hospede-item').length;

        const novoHospede = `
<div class="list-group-item hospede-item"data-quarto-selecionado="">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-4">
            <input type="text" class="form-control" name="hospedes[${index}][nome]" placeholder="Nome completo" required>
        </div>
        <div class="col-12 col-md-3">
            <input type="text" class="form-control" name="hospedes[${index}][documento]" placeholder="Documento" required>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select select-quarto" name="hospedes[${index}][quarto]" required onchange="atualizarQuartosFromHospede(this)">
                <option value="">Selecione o quarto</option>
                ${todosQuartosDisponiveis.map(quarto => {
                    const isDisabled = quartosSelecionados.some(q => q.id === quarto.id);
                    return `
                    <option value="${quarto.id}" ${isDisabled ? 'disabled' : ''}>
                        Quarto ${quarto.numero} (R$ ${quarto.preco})
                    </option>`;
                }).join('')}
            </select>
        </div>
        <div class="col-12 col-md-2 text-center">
            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removerHospede(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>`;

        document.getElementById('hospedesList').insertAdjacentHTML('beforeend', novoHospede);
        atualizarDropdownsHospedes();
    }

    function atualizarQuartosFromHospede(selectElement) {
        const quartoId = selectElement.value;
        const hospedeItem = selectElement.closest('.hospede-item');
        const quartoAnteriorId = hospedeItem.dataset.quartoSelecionado;

        // Remove o quarto anterior da seleção e destaque visual
        if (quartoAnteriorId && quartoAnteriorId !== quartoId) {
            // Remove da lista de quartos selecionados
            const index = quartosSelecionados.findIndex(q => q.id === quartoAnteriorId && q.origem === "select");
            if (index !== -1) {
                quartosSelecionados.splice(index, 1);
            }

            // Remove o destaque visual
            const quartoAnteriorDiv = document.querySelector(`.quarto[data-id="${quartoAnteriorId}"]`);
            if (quartoAnteriorDiv) {
                quartoAnteriorDiv.classList.remove('selecionado', 'por-select');
            }
        }

        // Armazena o novo quarto selecionado no elemento do hóspede
        hospedeItem.dataset.quartoSelecionado = quartoId;

        if (quartoId) {
            const index = quartosSelecionados.findIndex(q => q.id === quartoId);

            if (index === -1) {
                const quarto = todosQuartosDisponiveis.find(q => q.id === quartoId);
                if (quarto) {
                    quartosSelecionados.push({
                        id: quarto.id,
                        numero: quarto.numero,
                        preco: quarto.preco,
                        origem: "select"
                    });

                    const quartoDiv = document.querySelector(`.quarto[data-id="${quarto.id}"]`);
                    if (quartoDiv) {
                        quartoDiv.classList.add('selecionado', 'por-select');
                    }
                }
            }

            atualizarCamposOcultos();
            atualizarValorTotal();
            atualizarDropdownsHospedes();
        }
    }

    function removerHospede(botao) {
        const item = botao.closest('.hospede-item');
        const quartoSelect = item.querySelector('.select-quarto');
        const quartoId = quartoSelect ? quartoSelect.value : null;

        item.remove();

        if (quartoId) {
            // Remove apenas se for um quarto de hóspede
            const index = quartosSelecionados.findIndex(q => q.id === quartoId && q.origem === "select");
            if (index !== -1) {
                quartosSelecionados.splice(index, 1);

                const quartoDiv = document.querySelector(`.quarto[data-id="${quartoId}"]`);
                if (quartoDiv) {
                    quartoDiv.classList.remove('selecionado', 'por-select');
                }

                // Atualiza o campo hidden se estava usando esse quarto como principal
                if (document.getElementById('quarto_principal_selecionado').value === quartoId) {
                    document.getElementById('quarto_principal_selecionado').value = "";
                    quartoPrincipalSelecionado = null;
                }
            }
        }

        atualizarCamposOcultos();
        atualizarValorTotal();
        atualizarDropdownsHospedes();
    }

    // Funções de Pagamento
    function calcularParcelas() {
        try {
            const valorTotalStr = document.getElementById('valor_total').value;
            const valorTotal = parseFloat(valorTotalStr.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

            const valorPagoStr = document.getElementById('valor_pago').value;
            const valorPago = parseFloat(valorPagoStr.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

            const saldoParcelar = valorTotal - valorPago;

            if (saldoParcelar < 0) {
                alert('O valor pago não pode ser maior que o valor total!');
                document.getElementById('valor_pago').value = '';
                document.getElementById('valor_parcela').value = '';
                return;
            }

            const parcelas = parseInt(document.getElementById('parcelas').value) || 1;
            const valorParcelaInput = document.getElementById('valor_parcela');

            if (parcelas > 1 && saldoParcelar > 0) {
                const valorParcela = saldoParcelar / parcelas;
                valorParcelaInput.value = valorParcela.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            } else {
                valorParcelaInput.value = '';
            }
        } catch (error) {
            console.error('Erro ao calcular parcelas:', error);
            document.getElementById('valor_parcela').value = '';
        }
    }

    function toggleParcelas() {
        const formaPagamento = document.getElementById('pagamento').value;
        const parcelasDiv = document.getElementById('parcelasDiv');
        const valorParcelaDiv = document.getElementById('valorParcelaDiv');

        if (formaPagamento === 'cartaoCredito') {
            parcelasDiv.style.display = 'block';
            valorParcelaDiv.style.display = 'block';
            calcularParcelas();
        } else {
            parcelasDiv.style.display = 'none';
            valorParcelaDiv.style.display = 'none';
            document.getElementById('parcelas').value = '1';
            document.getElementById('valor_parcela').value = '';
        }
    }

    function verificarQuartoPrincipal() {
        const temQuartoPrincipal = quartosSelecionados.some(q => q.origem === "clique");
        const container = document.getElementById('quartos-container');

        if (!temQuartoPrincipal && quartosSelecionados.length > 0) {
            container.classList.add('quarto-container-invalido');
        } else {
            container.classList.remove('quarto-container-invalido');
        }
    }

    // Função para validar o formulário antes do envio
    function validarFormulario(event) {

        console.log('aqui');
        event.preventDefault(); // Impede o envio padrão

        // Validações básicas
        if (!document.getElementById('cliente_id').value) {
            alert('Por favor, selecione um cliente');
            return false;
        }

        const checkin = document.getElementById('data_checkin').value;
        const checkout = document.getElementById('data_checkout').value;

        if (!checkin || !checkout) {
            alert('Por favor, preencha as datas de check-in e check-out');
            return false;
        }

        if (new Date(checkout) <= new Date(checkin)) {
            alert('A data de check-out deve ser posterior à data de check-in');
            return false;
        }

        if (quartosSelecionados.length === 0) {
            alert('Por favor, selecione pelo menos um quarto');
            return false;
        }

        // Verifica se há um quarto principal selecionado
        if (!quartoPrincipalSelecionado) {
            alert('Por favor, selecione um quarto principal (clicando em um quarto)');
            return false;
        }

        // Validação do valor pago
        const valorTotalStr = document.getElementById('valor_total').value;
        const valorTotal = parseFloat(valorTotalStr.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
        const valorPagoStr = document.getElementById('valor_pago').value;
        const valorPago = parseFloat(valorPagoStr.replace(/[^\d,]/g, '').replace(',', '.')) || 0;

        if (valorPago > valorTotal) {
            alert('O valor pago não pode ser maior que o valor total');
            return false;
        }

        // Validação dos hóspedes secundários
        const hospedesItems = document.querySelectorAll('.hospede-item');
        for (const item of hospedesItems) {
            const nome = item.querySelector('input[name*="[nome]"]').value;
            const documento = item.querySelector('input[name*="[documento]"]').value;
            const quarto = item.querySelector('.select-quarto').value;

            if (!nome || !documento || !quarto) {
                alert('Por favor, preencha todos os campos dos hóspedes secundários');
                return false;
            }
        }

        // Se todas as validações passarem, envia o formulário
        event.target.submit();
    }

    // Modifique o evento de submit do formulário
    document.querySelector('form').addEventListener('submit', validarFormulario);

    // Inicialização
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('parcelas').addEventListener('input', calcularParcelas);
        document.getElementById('pagamento').addEventListener('change', toggleParcelas);
        document.getElementById('valor_pago').addEventListener('input', calcularParcelas);
        toggleParcelas();

        // Máscara de valor monetário
        document.getElementById('valor_pago').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            e.target.value = value;
            calcularParcelas();
        });
    });
    </script>
</body>

</html>