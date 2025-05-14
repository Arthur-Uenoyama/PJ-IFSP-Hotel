<?php

$host = "localhost";
$dbname = "hotel";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Função para verificar/criar tabelas
    //verificarTabelas($pdo);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

if (!function_exists('verificarTabelas')) {
    function verificarTabelas($pdo)
    {
        try {
            // Criar o banco de dados 'hotel' caso não exista
            $pdo->exec("CREATE DATABASE IF NOT EXISTS hotel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            //print_r("Banco de dados 'hotel' verificado/criado com sucesso!<br>");

            // Usar o banco de dados 'hotel'
            $pdo->exec("USE hotel");

            //  Lista de tabelas e estruturas
            $tabelas = [
                'usuarios' => "
                CREATE TABLE usuarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome_completo VARCHAR(100) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    senha VARCHAR(255) NOT NULL,
                    telefone_fixo VARCHAR(20) DEFAULT NULL,
                    telefone_celular VARCHAR(20) DEFAULT NULL,
                    data_nascimento DATE NOT NULL,
                    sexo ENUM('Masculino', 'Feminino', 'Outro') NOT NULL,
                    profissao VARCHAR(100) DEFAULT NULL,
                    nacionalidade VARCHAR(50) DEFAULT NULL,
                    tipo_documento ENUM('CPF', 'CNPJ') NOT NULL,
                    cpf_cnpj VARCHAR(20) DEFAULT NULL,
                    documento_Inde VARCHAR(255) DEFAULT NULL,
                    cep VARCHAR(10) NOT NULL,
                    logradouro VARCHAR(150) NOT NULL,
                    numero VARCHAR(10) NOT NULL,
                    complemento VARCHAR(50) DEFAULT NULL,
                    bairro VARCHAR(100) NOT NULL,
                    cidade VARCHAR(100) NOT NULL,
                    estado VARCHAR(5) NOT NULL,
                    empresa_trabalha VARCHAR(255)NOT NULL,
                    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
             ",

                'funcionarios' => "
                CREATE TABLE funcionarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    senha VARCHAR(255) NOT NULL,
                    cargo VARCHAR(50) NOT NULL,
                    telefone VARCHAR(20) NOT NULL,
                    salario DECIMAL(10, 2) NOT NULL,  
                    dt_inicio_ferias DATE,
                    dt_final_ferias DATE   
                )
             ",

                'quartos' => "
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
                )
             ",

                "reservas" => "
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
                    status ENUM('pendente', 'confirmada', 'cancelada', 'finalizada', 'em andamento') NOT NULL DEFAULT 'pendente',
                    observacoes TEXT DEFAULT NULL,
                    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
                )
             ",

                "hospedes_secundarios" => "
                CREATE TABLE hospedes_secundarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    reserva_id INT NOT NULL,
                    nome VARCHAR(255) NOT NULL,
                    cpf_cnpj VARCHAR(20) NOT NULL,
                    quarto_id INT NOT NULL,
                    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
                    FOREIGN KEY (quarto_id) REFERENCES quartos(id) ON DELETE CASCADE
                )
             ",

                "pedidos_servico_quarto" => "
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
                )
             ",

                "pagamentos" => "
                CREATE TABLE pagamentos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    reserva_id INT NOT NULL,
                    valor DECIMAL(10,2) NOT NULL,
                    metodo ENUM('cartão', 'pix', 'boleto') NOT NULL,
                    status ENUM('pendente', 'aprovado', 'recusado') NOT NULL DEFAULT 'pendente',
                    data_pagamento TIMESTAMP NULL DEFAULT NULL,
                    data_baixa TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
                )
             ",

                "avaliacoes" => "
                CREATE TABLE avaliacoes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    reserva_id INT NOT NULL,
                    nota INT NOT NULL,
                    comentario TEXT NOT NULL,
                    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
                )
             ",
            ];

            //  Verificar e criar cada tabela
            foreach ($tabelas as $nomeTabela => $sql) {
                //  Verificar se a tabela existe
                $consulta = $pdo->query("SHOW TABLES LIKE '$nomeTabela'");
                if ($consulta->rowCount() == 0) {
                    $pdo->exec($sql);
                    //print_r("Tabela $nomeTabela criada com sucesso!<br>");
                }
            }

            //  Inserir funcionário padrão se não existir
            $verificaFuncionario = $pdo->query("SELECT id FROM funcionarios WHERE email = 'pousada@mazin.com'");
            if ($verificaFuncionario->rowCount() == 0) {
                $pdo->exec("INSERT INTO funcionarios (nome, email, senha, cargo, telefone)
                        VALUES ('Mazin', 'pousada@mazin.com', SHA2('123', 256), 'Gerente', '0000-0000')");
                //print_r("Funcionário padrão criado!<br>");
            }

            // Inserir usuário de teste se não existir
            $verificaUsuario = $pdo->query("SELECT id FROM usuarios WHERE email = 'a@a.com'");
            if ($verificaUsuario->rowCount() == 0) {
                $stmt = $pdo->prepare("INSERT INTO usuarios (
                    nome_completo, email, senha, telefone_fixo, telefone_celular,
                    data_nascimento, sexo, profissao, nacionalidade,
                    tipo_documento, cpf_cnpj, documento_Inde,
                    cep, logradouro, numero, complemento, bairro,
                    cidade, estado, empresa_trabalha
                ) VALUES (
                    :nome_completo, :email, SHA2(:senha, 256), :telefone_fixo, :telefone_celular,
                    :data_nascimento, :sexo, :profissao, :nacionalidade,
                    :tipo_documento, :cpf_cnpj, :documento_Inde,
                    :cep, :logradouro, :numero, :complemento, :bairro,
                    :cidade, :estado, :empresa_trabalha
                )");

                $stmt->execute([
                    ':nome_completo' => 'Usuário Teste',
                    ':email' => 'a@a.com',
                    ':senha' => '123',
                    ':telefone_fixo' => '1122223333',
                    ':telefone_celular' => '11999998888',
                    ':data_nascimento' => '1990-01-01',
                    ':sexo' => 'Outro',
                    ':profissao' => 'Tester',
                    ':nacionalidade' => 'Brasileiro',
                    ':tipo_documento' => 'CPF',
                    ':cpf_cnpj' => '000.000.000-00',
                    ':documento_Inde' => 'doc_teste.jpg',
                    ':cep' => '12345-678',
                    ':logradouro' => 'Rua de Teste',
                    ':numero' => '123',
                    ':complemento' => 'Ap 1',
                    ':bairro' => 'Centro',
                    ':cidade' => 'Testópolis',
                    ':estado' => 'SP',
                    ':empresa_trabalha' => 'Empresa Teste'
                ]);
                //print_r("Usuário de teste criado!<br>");
            }

            // Verifica se os quartos já existem
        $verificaQuartos = $pdo->query("SELECT id FROM quartos WHERE numero IN ('101', '201', '301')");
        if ($verificaQuartos->rowCount() == 0) {
            // Quarto 1: Individual
            $stmt = $pdo->prepare("INSERT INTO quartos (
                numero, camas_solteiro, camas_casal, preco, descricao, regras, status
            ) VALUES (
                :numero, :camas_solteiro, :camas_casal, :preco, :descricao, :regras, :status
            )");
            $stmt->execute([
                ':numero' => '101',
                ':camas_solteiro' => 1,
                ':camas_casal' => 0,
                ':preco' => 120.00,
                ':descricao' => 'Quarto individual aconchegante com cama de solteiro, ideal para viajantes solo.',
                ':regras' => 'Proibido fumar no quarto. Check-out até as 11h.',
                ':status' => 'Disponível'
            ]);

            // Quarto 2: Beliches
            $stmt = $pdo->prepare("INSERT INTO quartos (
                numero, beliches, preco, descricao, regras, imagem, status
            ) VALUES (
                :numero, :beliches, :preco, :descricao, :regras, :imagem, :status
            )");
            $stmt->execute([
                ':numero' => '201',
                ':beliches' => 2,
                ':preco' => 180.00,
                ':descricao' => 'Quarto espaçoso com dois beliches (total de 4 camas), perfeito para grupos ou famílias.',
                ':regras' => 'Máximo de 4 hóspedes. Uso de cozinha compartilhada permitido.',
                ':imagem' => 'quarto_beliche.jpg',
                ':status' => 'Disponível'
            ]);

            // Quarto 3: Casal Premium
            $stmt = $pdo->prepare("INSERT INTO quartos (
                numero, camas_casal, preco, descricao, regras, imagem, status
            ) VALUES (
                :numero, :camas_casal, :preco, :descricao, :regras, :imagem, :status
            )");
            $stmt->execute([
                ':numero' => '301',
                ':camas_casal' => 1,
                ':preco' => 250.00,
                ':descricao' => 'Quarto premium com cama de casal king-size, TV de tela plana e vista para o mar.',
                ':regras' => 'Check-in após 14h. Animais de estimação permitidos com taxa adicional.',
                ':imagem' => 'quarto_casal_premium.jpg',
                ':status' => 'Ocupado'
            ]);
        }

        } catch (PDOException $e) {
            print_r("Erro nas tabelas: " . $e->getMessage());
        }
    }
}

verificarTabelas($pdo);
