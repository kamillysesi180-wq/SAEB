-- =====================================================
-- Sistema de Assistencia Tecnica - SAEP
-- Script de criacao e populacao do banco de dados
-- =====================================================

CREATE DATABASE IF NOT EXISTS saep_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE saep_db;

DROP TABLE IF EXISTS ordens_servico;
DROP TABLE IF EXISTS equipamentos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

-- =====================================================
-- Tabela: usuarios
-- =====================================================
CREATE TABLE usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL UNIQUE,
    senha       VARCHAR(255) NOT NULL,
    perfil      VARCHAR(30)  NOT NULL DEFAULT 'Tecnico',
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: clientes
-- Dados sensiveis (cpf, telefone) protegidos com hash
-- =====================================================
CREATE TABLE clientes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120) NOT NULL,
    cpf         VARCHAR(64)  NOT NULL,
    telefone    VARCHAR(64)  NOT NULL,
    email       VARCHAR(120),
    endereco    VARCHAR(200),
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: equipamentos
-- =====================================================
CREATE TABLE equipamentos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT NOT NULL,
    tipo            VARCHAR(80)  NOT NULL,
    marca           VARCHAR(80)  NOT NULL,
    modelo          VARCHAR(100) NOT NULL,
    numero_serie    VARCHAR(80),
    descricao       TEXT,
    criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equip_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: ordens_servico
-- =====================================================
CREATE TABLE ordens_servico (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id          INT NOT NULL,
    equipamento_id      INT NOT NULL,
    data_abertura       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_fechamento     DATETIME NULL,
    status              ENUM('Aberta','Em andamento','Concluida','Cancelada') NOT NULL DEFAULT 'Aberta',
    descricao_problema  TEXT NOT NULL,
    solucao             TEXT,
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_os_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_os_equipamento
        FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Dados iniciais (minimo 3 registros por tabela)
-- Senha de todos os usuarios: 123456
-- =====================================================

INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador Sistema', 'admin@saep.local', '$2y$10$jcjRcEh1brRZy3mDfQBUbOcFB5UxAeBJIGrXmDbxIs4LD4KJm8F2.', 'Administrador'),
('Maria Oliveira',        'maria@saep.local', '$2y$10$jcjRcEh1brRZy3mDfQBUbOcFB5UxAeBJIGrXmDbxIs4LD4KJm8F2.', 'Tecnico'),
('Joao Pedro Santos',     'joao@saep.local',  '$2y$10$jcjRcEh1brRZy3mDfQBUbOcFB5UxAeBJIGrXmDbxIs4LD4KJm8F2.', 'Tecnico');

INSERT INTO clientes (nome, cpf, telefone, email, endereco) VALUES
('Carlos Eduardo Lima',   SHA2('123.456.789-00', 256), SHA2('(11) 98765-4321', 256), 'carlos.lima@email.com',   'Rua das Flores, 120 - Sao Paulo/SP'),
('Ana Beatriz Costa',     SHA2('987.654.321-00', 256), SHA2('(21) 91234-5678', 256), 'ana.costa@email.com',     'Av. Atlantica, 450 - Rio de Janeiro/RJ'),
('Roberto Almeida Souza', SHA2('456.789.123-00', 256), SHA2('(31) 99876-5432', 256), 'roberto.souza@email.com', 'Rua Minas Gerais, 78 - Belo Horizonte/MG');

INSERT INTO equipamentos (cliente_id, tipo, marca, modelo, numero_serie, descricao) VALUES
(1, 'Notebook',   'Dell',     'Inspiron 15 3000', 'SN-DELL-001234', 'Notebook usado para trabalho remoto'),
(1, 'Desktop',    'HP',       'ProDesk 400 G7',  'SN-HP-005678',   'Computador de mesa do escritorio'),
(2, 'Notebook',   'Lenovo',   'IdeaPad 3',       'SN-LEN-009876',  'Notebook pessoal com defeito na tela'),
(3, 'Impressora', 'Epson',    'EcoTank L3250',   'SN-EPS-004321',  'Impressora multifuncional'),
(2, 'Desktop',    'Dell',     'OptiPlex 7090',   'SN-DELL-007890', 'Desktop corporativo');

INSERT INTO ordens_servico (cliente_id, equipamento_id, data_abertura, status, descricao_problema, solucao) VALUES
(1, 1, '2026-10-20 09:15:00', 'Concluida',    'Notebook nao liga. Possivel problema na fonte.', 'Fonte substituida e testada. Equipamento liberado.'),
(2, 3, '2026-10-22 14:30:00', 'Em andamento', 'Tela do notebook com manchas e linhas verticais.', NULL),
(3, 4, '2026-10-25 11:00:00', 'Aberta',       'Impressora nao reconhece cartuchos de tinta.', NULL),
(1, 2, '2026-10-26 08:45:00', 'Aberta',       'Desktop reinicia sozinho apos alguns minutos de uso.', NULL),
(2, 5, '2026-10-27 10:20:00', 'Em andamento', 'Computador lento e com mensagens de erro de memoria.', NULL);
