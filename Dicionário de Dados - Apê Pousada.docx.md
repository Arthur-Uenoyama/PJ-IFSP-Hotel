# Dicionário de dados \- Apê Pousada

## Tabela: `usuarios`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do usuário. |
| `nome_completo` | VARCHAR(100) | NOT NULL | Nome completo do usuário. |
| `email` | VARCHAR(100) | UNIQUE NOT NULL | Endereço de e-mail único do usuário. |
| `senha` | VARCHAR(255) | NOT NULL | Senha do usuário (criptografada). |
| `telefone_fixo` | VARCHAR(20) | DEFAULT NULL | Número de telefone fixo do usuário (opcional). |
| `telefone_celular` | VARCHAR(20) | DEFAULT NULL | Número de telefone celular do usuário (opcional). |
| `data_nascimento` | DATE | NOT NULL | Data de nascimento do usuário. |
| `sexo` | ENUM | ('Masculino', 'Feminino', 'Outro') NOT NULL | Sexo do usuário. |
| `profissao` | VARCHAR(100) | DEFAULT NULL | Profissão do usuário (opcional). |
| `nacionalidade` | VARCHAR(50) | DEFAULT NULL | Nacionalidade do usuário (opcional). |
| `tipo_documento` | ENUM | ('CPF', 'CNPJ') NOT NULL | Tipo de documento do usuário (CPF ou CNPJ). |
| `cpf_cnpj` | VARCHAR(20) | DEFAULT NULL | Número do CPF ou CNPJ do usuário (dependendo do `tipo_documento`). |
| `documento_Inde` | VARCHAR(255) | DEFAULT NULL | Outro documento de identificação do usuário (opcional). |
| `cep` | VARCHAR(10) | NOT NULL | Código de Endereçamento Postal do usuário. |
| `logradouro` | VARCHAR(150) | NOT NULL | Logradouro do endereço do usuário. |
| `numero` | VARCHAR(10) | NOT NULL | Número do endereço do usuário. |
| `complemento` | VARCHAR(50) | DEFAULT NULL | Complemento do endereço do usuário (opcional). |
| `bairro` | VARCHAR(100) | NOT NULL | Bairro do endereço do usuário. |
| `cidade` | VARCHAR(100) | NOT NULL | Cidade do endereço do usuário. |
| `estado` | VARCHAR(5) | NOT NULL | Estado (UF) do endereço do usuário. |
| `empresa_trabalha` | VARCHAR(255) | NOT NULL | Empresa onde o usuário trabalha. |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT\_TIMESTAMP | Data e hora de criação do registro. |

## Tabela: `funcionarios`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do funcionário. |
| `nome` | VARCHAR(100) | NOT NULL | Nome do funcionário. |
| `email` | VARCHAR(100) | UNIQUE NOT NULL | Endereço de e-mail único do funcionário. |
| `senha` | VARCHAR(255) | NOT NULL | Senha do funcionário (criptografada). |
| `cargo` | VARCHAR(50) | NOT NULL | Cargo do funcionário. |
| `telefone` | VARCHAR(20) | NOT NULL | Número de telefone do funcionário. |

## Tabela: `quartos`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do quarto. |
| `numero` | VARCHAR(10) | NOT NULL UNIQUE | Número único do quarto. |
| `camas_solteiro` | INT | DEFAULT 0 | Número de camas de solteiro no quarto. |
| `beliches` | INT | DEFAULT 0 | Número de beliches no quarto. |
| `camas_casal` | INT | DEFAULT 0 | Número de camas de casal no quarto. |
| `preco` | DECIMAL(10,2) | NOT NULL | Preço da diária do quarto. |
| `descricao` | TEXT | NOT NULL | Descrição detalhada do quarto. |
| `regras` | TEXT | DEFAULT NULL | Regras específicas do quarto (opcional). |
| `imagem` | VARCHAR(255) | DEFAULT NULL | Caminho ou nome do arquivo de imagem do quarto (opcional). |
| `status` | ENUM | ('Disponível', 'Ocupado', 'Manutenção') NOT NULL DEFAULT 'Disponível' | Status atual do quarto (Disponível, Ocupado, Manutenção). |

## Tabela: `reservas`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único da reserva. |
| `usuario_id` | INT | NOT NULL, FOREIGN KEY (usuarios(id)) ON DELETE CASCADE | Identificador do usuário que fez a reserva (chave estrangeira). |
| `quarto_id` | INT | NOT NULL, FOREIGN KEY (quartos(id)) ON DELETE CASCADE | Identificador do quarto reservado (chave estrangeira). |
| `cpf_cnpj` | VARCHAR(20) | NOT NULL | CPF ou CNPJ do titular da reserva. |
| `data_checkin` | DATE | NOT NULL | Data de check-in da reserva. |
| `hora_checkin` | TIME | NOT NULL | Hora de check-in da reserva. |
| `data_checkout` | DATE | NOT NULL | Data de check-out da reserva. |
| `hora_checkout` | TIME | NOT NULL | Hora de check-out da reserva. |
| `tipo_camas` | ENUM | ('Solteiro', 'Casal', 'Beliche') NOT NULL | Tipo de cama principal da reserva. |
| `valor_reserva` | DECIMAL(10,2) | NOT NULL | Valor total da reserva. |
| `tipo_pensao` | ENUM | ('Sem Pensão', 'Café da Manhã', 'Meia Pensão', 'Pensão Completa') NOT NULL DEFAULT 'Café da Manhã' | Tipo de pensão incluída na reserva. |
| `forma_pagamento` | ENUM | ('Dinheiro', 'Cartão', 'Pix', 'Boleto') NOT NULL | Forma de pagamento da reserva. |
| `status` | ENUM | ('pendente', 'confirmada', 'cancelada', 'finalizada') NOT NULL DEFAULT 'pendente' | Status atual da reserva. |
| `observacoes` | TEXT | DEFAULT NULL | Observações adicionais sobre a reserva (opcional). |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT\_TIMESTAMP | Data e hora de criação do registro. |

## Tabela: `hospedes_secundarios`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do hóspede secundário. |
| `reserva_id` | INT | NOT NULL, FOREIGN KEY (reservas(id)) ON DELETE CASCADE | Identificador da reserva à qual o hóspede está associado (chave estrangeira). |
| `nome` | VARCHAR(255) | NOT NULL | Nome do hóspede secundário. |
| `cpf_cnpj` | VARCHAR(20) | NOT NULL | CPF ou CNPJ do hóspede secundário. |
| `quarto_id` | INT | NOT NULL, FOREIGN KEY (quartos(id)) ON DELETE CASCADE | Identificador do quarto em que o hóspede está hospedado (chave estrangeira). |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT\_TIMESTAMP | Data e hora de criação do registro. |

## Tabela: `pedidos_servico_quarto`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do pedido de serviço de quarto. |
| `reserva_id` | INT | NOT NULL, FOREIGN KEY (reservas(id)) ON DELETE CASCADE | Identificador da reserva à qual o pedido está associado (chave estrangeira). |
| `descricao` | TEXT | NOT NULL | Descrição do serviço solicitado. |
| `status` | ENUM | ('pendente', 'em andamento', 'concluido', 'cancelado') NOT NULL DEFAULT 'pendente' | Status atual do pedido de serviço. |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT\_TIMESTAMP | Data e hora de criação do registro. |
| `valor` | DECIMAL(10,2) | NOT NULL | Valor do serviço solicitado. |

## Tabela: `pagamentos`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único do pagamento. |
| `reserva_id` | INT | NOT NULL, FOREIGN KEY (reservas(id)) ON DELETE CASCADE | Identificador da reserva referente ao pagamento (chave estrangeira). |
| `valor` | DECIMAL(10,2) | NOT NULL | Valor do pagamento. |
| `metodo` | ENUM | ('cartão', 'pix', 'boleto') NOT NULL | Método de pagamento utilizado. |
| `status` | ENUM | ('pendente', 'aprovado', 'recusado') NOT NULL DEFAULT 'pendente' | Status atual do pagamento. |
| `data_pagamento` | TIMESTAMP | DEFAULT NULL | Data e hora em que o pagamento foi efetuado (opcional). |
| `data_baixa` | TIMESTAMP | DEFAULT NULL | Data e hora em que o pagamento foi confirmado/baixado (opcional). |

## Tabela: `avaliacoes`

| Coluna | Tipo | Atributos | Descrição |
| :---- | :---- | :---- | :---- |
| `id` | INT | AUTO\_INCREMENT PRIMARY KEY | Identificador único da avaliação. |
| `usuario_id` | INT | NOT NULL, FOREIGN KEY (usuarios(id)) ON DELETE CASCADE | Identificador do usuário que fez a avaliação (chave estrangeira). |
| `quarto_id` | INT | NOT NULL, FOREIGN KEY (quartos(id)) ON DELETE CASCADE | Identificador do quarto avaliado (chave estrangeira). |
| `nota` | INT | NOT NULL | Nota da avaliação (geralmente em uma escala). |
| `comentario` | TEXT | NOT NULL | Comentário do usuário sobre o quarto. |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT\_TIMESTAMP | Data e hora de criação do registro. |

