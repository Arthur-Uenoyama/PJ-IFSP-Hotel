CREATE DATABASE hotel;
USE hotel;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone_fixo VARCHAR(20) DEFAULT NULL,
    telefone_celular VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('Masculino', 'Feminino', 'Outro') DEFAULT NULL,
    profissao VARCHAR(100) DEFAULT NULL,
    nacionalidade VARCHAR(50) DEFAULT NULL,
    tipo_documento ENUM('CPF', 'CNPJ') NOT NULL,
    cpf_cnpj VARCHAR(20) NOT NULL,
    documento_Inde VARCHAR(255) DEFAULT NULL,
    cep VARCHAR(10) NOT NULL,
    logradouro VARCHAR(150) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(50) DEFAULT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(5) NOT NULL,
    empresa_trabalha VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    salario DECIMAL(10, 2) NOT NULL,  -- Adicionando coluna para salário
    data_ultimo_periodo_ferias DATE    -- Adicionando coluna para a data do último período de férias
);


CREATE TABLE quartos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) NOT NULL UNIQUE,
    camas_solteiro INT DEFAULT 0,
    beliches INT DEFAULT 0,
    camas_casal INT DEFAULT 0,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT NOT NULL,
    regras TEXT DEFAULT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    status ENUM('Disponível', 'Ocupado', 'Manutenção') NOT NULL DEFAULT 'Disponível'
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    quarto_id INT NOT NULL,
    cpf_cnpj VARCHAR(20) NOT NULL, 
    data_checkin DATE NOT NULL,
    hora_checkin TIME NOT NULL,
    data_checkout DATE NOT NULL,
    hora_checkout TIME NOT NULL,
    tipo_camas ENUM('Solteiro', 'Casal', 'Beliche') NOT NULL,
    valor_reserva DECIMAL(10,2) NOT NULL,  
    tipo_pensao ENUM('Sem Pensão', 'Café da Manhã', 'Meia Pensão', 'Pensão Completa') NOT NULL DEFAULT 'Café da Manhã',
    forma_pagamento ENUM('Dinheiro', 'Cartão de Crédito', 'Cartão de Debito', 'TED', 'Pix', 'Boleto') NOT NULL,
    status ENUM('pendente', 'confirmada', 'cancelada', 'finalizada') NOT NULL DEFAULT 'pendente',
    observacoes TEXT DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
);

CREATE TABLE hospedes_secundarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cpf_cnpj VARCHAR(20) NOT NULL,
    quarto_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
);

CREATE TABLE pedidos_servico_quarto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,  
    descricao TEXT NOT NULL,   
    status ENUM('pendente', 'em andamento', 'concluido', 'cancelado') NOT NULL DEFAULT 'pendente',  
    prioridade ENUM('Normal', 'Urgente') NOT NULL DEFAULT 'Normal', 
    tipoServico ENUM('Limpeza', 'Manutenção', 'Alimentação', 'Outro') NOT NULL DEFAULT 'Outro', 
    valor DECIMAL(10,2) DEFAULT NULL,
    horarioPreferencial TIME DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE  
);


CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    metodo ENUM('Cartão de Credito', 'Cartão de Debito', 'TED', 'pix', 'Cheque', 'Boleto') NOT NULL,
    status ENUM('pendente', 'aprovado', 'recusado') NOT NULL DEFAULT 'pendente',
    data_pagamento TIMESTAMP NULL DEFAULT NULL,
    data_baixa TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
);

CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    quarto_id INT NOT NULL,
    nota INT NOT NULL,
    comentario TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
);



INSERT INTO funcionarios (nome, email, senha, cargo, telefone)
VALUES ('Mazin', 'pousada@mazin.com', SHA2('Pousa-mazin', 256), 'Gerente', '0000-0000');


