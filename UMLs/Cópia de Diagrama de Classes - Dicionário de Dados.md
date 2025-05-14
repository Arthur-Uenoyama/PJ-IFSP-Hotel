## **Dicionário de Dados**

### **Cliente**

Entidade que representa os clientes do sistema.

* **id** (int; obrigatório): Identificador único do cliente.  
* **nomeCompleto** (String; obrigatório): Nome completo do cliente.  
* **email** (String; obrigatório): Endereço de e-mail do cliente (deve ser único).  
* **senhaHash** (String; obrigatório): Hash da senha para autenticação.  
* **telefoneFixo** (String; opcional): Número de telefone fixo do cliente.  
* **telefoneCelular** (String; opcional): Número de telefone celular do cliente.  
* **dataNascimento** (Date; obrigatório): Data de nascimento do cliente.  
* **sexo** (Sexo; obrigatório): Gênero do cliente (MASCULINO, FEMININO, OUTRO).  
* **profissao** (String; opcional): Profissão do cliente.  
* **nacionalidade** (String; opcional): Nacionalidade do cliente.  
* **tipoDocumento** (TipoDocumento; obrigatório): Tipo de documento do cliente (CPF, CNPJ).  
* **documento** (String; obrigatório): Número do documento (CPF ou CNPJ) do cliente.  
* **endereco** (Endereco; obrigatório): Endereço do cliente.  
* **criadoEm** (DateTime; obrigatório): Data e hora de criação do registro do cliente.

### **Endereco**

Objeto de valor que representa o endereço de um cliente.

* **cep** (String; obrigatório): Código postal.  
* **logradouro** (String; obrigatório): Nome da rua, avenida, etc.  
* **numero** (String; obrigatório): Número do imóvel.  
* **complemento** (String; opcional): Informação adicional (bloco, apartamento, etc.).  
* **bairro** (String; obrigatório): Nome do bairro.  
* **cidade** (String; obrigatório): Nome da cidade.  
* **estado** (String; obrigatório): Sigla da unidade federativa.

### **Funcionario**

Entidade que representa os funcionários do sistema.

* **id** (int; obrigatório): Identificador único do funcionário.  
* **nome** (String; obrigatório): Nome completo do funcionário.  
* **email** (String; obrigatório): Endereço de e-mail corporativo do funcionário (deve ser único).  
* **senhaHash** (String; obrigatório): Hash da senha do funcionário.  
* **cargo** (String; obrigatório): Cargo do funcionário.  
* **telefone** (String; obrigatório): Telefone de contato do funcionário.  
* **criadoEm** (DateTime; obrigatório): Data e hora de criação do registro do funcionário.

### **Quarto**

Entidade que representa os quartos disponíveis.

* **id** (int; obrigatório): Identificador único do quarto.  
* **numero** (String; obrigatório): Número ou código do quarto (deve ser único).  
* **camasSolteiro** (int; obrigatório): Quantidade de camas de solteiro no quarto.  
* **beliches** (int; obrigatório): Quantidade de beliches no quarto.  
* **camasCasal** (int; obrigatório): Quantidade de camas de casal no quarto.  
* **precoNoite** (double; obrigatório): Preço por noite do quarto.  
* **descricao** (String; obrigatório): Descrição detalhada do quarto e suas amenidades.  
* **status** (StatusQuarto; obrigatório): Estado do quarto (DISPONIVEL, OCUPADO, MANUTENCAO).  
* **imagem** (String; opcional): URL ou caminho para a imagem ilustrativa do quarto.

### **Reserva**

Entidade que representa as reservas de quartos.

* **id** (int; obrigatório): Identificador único da reserva.  
* **dataReserva** (DateTime; obrigatório): Data e hora em que a reserva foi criada.  
* **dataCheckin** (Date; obrigatório): Data prevista de check-in.  
* **horaInicioCheckin** (Time; obrigatório): Horário de início permitido para check-in.  
* **horaLimiteCheckin** (Time; obrigatório): Horário máximo para check-in.  
* **dataCheckout** (Date; obrigatório): Data prevista de check-out.  
* **horaInicioCheckout** (Time; obrigatório): Horário de início permitido para check-out.  
* **horaLimiteCheckout** (Time; obrigatório): Horário máximo para check-out.  
* **tipoCama** (TipoCama; obrigatório): Tipo de cama solicitado na reserva (SOLTEIRO, CASAL, BELICHE).  
* **valorReserva** (double; obrigatório): Valor total da reserva.  
* **tipoPensao** (TipoPensao; obrigatório): Regime de pensão da reserva (SEM\_PENSAO, CAFE\_MANHA, MEIA\_PENSAO, PENSAO\_COMPLETA).  
* **formaPagamento** (FormaPagamento; obrigatório): Método de pagamento da reserva (DINHEIRO, CARTAO\_CREDITO, CARTAO\_DEBITO, BOLETO\_BANCARIO, PIX).  
* **status** (StatusReserva; obrigatório): Situação da reserva (PENDENTE, CONFIRMADA, CANCELADA, FINALIZADA).  
* **observacoes** (String; opcional): Comentários adicionais do cliente sobre a reserva.  
* **criadoEm** (DateTime; obrigatório): Data e hora de criação do registro da reserva.

### **HospedeSecundario**

Entidade que representa os hóspedes acompanhantes em uma reserva.

* **id** (int; obrigatório): Identificador único do hóspede acompanhante.  
* **nome** (String; obrigatório): Nome completo do hóspede acompanhante.  
* **documento** (String; obrigatório): CPF ou CNPJ do hóspede acompanhante.  
* **criadoEm** (DateTime; obrigatório): Data e hora de inclusão do hóspede acompanhante no sistema.

### **ServicoQuarto**

Entidade que representa os serviços de quarto oferecidos.

* **id** (int; obrigatório): Identificador único do serviço de quarto.  
* **nomeServico** (String; obrigatório): Nome do serviço de quarto (ex: "Café da Manhã").  
* **descricao** (String; obrigatório): Detalhes sobre o serviço de quarto.  
* **preco** (double; obrigatório): Preço unitário do serviço de quarto.

### **PedidoServico**

Entidade que representa os pedidos de serviços de quarto feitos pelos hóspedes.

* **id** (int; obrigatório): Identificador único do pedido de serviço.  
* **descricao** (String; obrigatório): Descrição detalhada do pedido de serviço.  
* **quantidade** (int; obrigatório): Número de unidades solicitadas do serviço.  
* **valorUnitario** (double; obrigatório): Preço por unidade do serviço solicitado.  
* **status** (StatusPedidoServico; obrigatório): Situação do pedido de serviço (PENDENTE, EM\_ANDAMENTO, CONCLUIDO, CANCELADO).  
* **criadoEm** (DateTime; obrigatório): Data e hora de criação do pedido de serviço.

### **Pagamento**

Entidade que representa os pagamentos efetuados.

* **id** (int; obrigatório): Identificador único do pagamento.  
* **valorPagamento** (double; obrigatório): Valor pago na transação.  
* **forma** (FormaPagamento; obrigatório): Meio de pagamento utilizado.  
* **status** (StatusPagamento; obrigatório): Estado do pagamento (PENDENTE, APROVADO, RECUSADO).  
* **dataPagamento** (DateTime; opcional): Data e hora em que o pagamento foi efetuado.  
* **dataBaixa** (DateTime; opcional): Data de baixa/compensação financeira.

### **Avaliacao**

Entidade que representa as avaliações feitas pelos clientes.

* **id** (int; obrigatório): Identificador único da avaliação.  
* **nota** (int; obrigatório): Classificação numérica da avaliação (ex: 1 a 5).  
* **comentario** (String; obrigatório): Comentário em texto livre da avaliação.  
* **criadoEm** (DateTime; obrigatório): Data e hora de criação da avaliação.

### **RegraQuarto**

Entidade que representa as regras aplicáveis a um quarto.

* **descricao** (String; obrigatório): Texto que descreve uma regra aplicável ao quarto (ex: “Não é permitido fumar”).

### **Enums**

Lista dos valores enumerados utilizados no sistema.

* **Sexo**: MASCULINO, FEMININO, OUTRO  
* **TipoDocumento**: CPF, CNPJ  
* **StatusQuarto**: DISPONIVEL, OCUPADO, MANUTENCAO  
* **TipoCama**: SOLTEIRO, CASAL, BELICHE  
* **TipoPensao**: SEM\_PENSAO, CAFE\_MANHA, MEIA\_PENSAO, PENSAO\_COMPLETA  
* **FormaPagamento**: DINHEIRO, CARTAO\_CREDITO, CARTAO\_DEBITO, BOLETO\_BANCARIO, PIX  
* **StatusReserva**: PENDENTE, CONFIRMADA, CANCELADA, FINALIZADA  
* **StatusPagamento**: PENDENTE, APROVADO, RECUSADO  
* **StatusPedidoServico**: PENDENTE, EM\_ANDAMENTO, CONCLUIDO, CANCELADO

