CREATE DATABASE hotel;
USE hotel;

-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'hotel') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tablea Funcionarios
CREATE TABLE funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

-- Tabela de Quartos
CREATE TABLE quartos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) NOT NULL UNIQUE,
    tipo ENUM('Simples', 'Duplo', 'Luxo') NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,  -- Caminho da imagem do quarto
    status ENUM('Disponível', 'Ocupado', 'Manutenção') NOT NULL DEFAULT 'disponível'
);

-- Tabela de Reservas
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    quarto_id INT NOT NULL,
    data_checkin DATE NOT NULL,
    data_checkout DATE NOT NULL,
    status ENUM('pendente', 'confirmada', 'cancelada', 'finalizada') NOT NULL DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
);

-- Tabela de Pedidos de Serviço de Quarto
CREATE TABLE pedidos_servico_quarto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,  -- Relacionamento com a reserva
    descricao TEXT NOT NULL,   -- Descrição do pedido (ex: "Limpeza extra", "Pedido de toalhas", etc)
    status ENUM('pendente', 'em andamento', 'concluido', 'cancelado') NOT NULL DEFAULT 'pendente',  -- Status do pedido
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Data de criação do pedido
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE  -- Relacionamento com a reserva
);


-- Tabela de Pagamentos (Caso queira gerenciar pagamentos)
CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    metodo ENUM('cartão', 'pix', 'boleto') NOT NULL,
    status ENUM('pendente', 'aprovado', 'recusado') NOT NULL DEFAULT 'pendente',
    data_pagamento TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
);

-- Tabela de Avaliações (Para permitir que clientes avaliem os quartos)
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


