-- ============================================================
--  PDV Master — Schema + Dados Iniciais
--  MySQL / MariaDB (XAMPP)
--  Execute no MySQL Workbench ou phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS pdv_master
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pdv_master;

-- ------------------------------------------------------------
-- 1. USUÁRIOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id        INT UNSIGNED              NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(60)               NOT NULL UNIQUE,
    senha     VARCHAR(255)              NOT NULL,
    perfil    ENUM('admin','caixa')     NOT NULL DEFAULT 'caixa',
    ativo     TINYINT(1)                NOT NULL DEFAULT 1,
    criado_em DATETIME                  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Usuários padrão — senha: 123
INSERT IGNORE INTO usuarios (nome, senha, perfil) VALUES
    ('admin', '$2y$12$2gd3IRFC0ZRZWxWHje3a1u/tbA3F86zHIpvC2YenBgmo.6ERyRsK.', 'admin'),
    ('caixa', '$2y$12$2gd3IRFC0ZRZWxWHje3a1u/tbA3F86zHIpvC2YenBgmo.6ERyRsK.', 'caixa');

-- ------------------------------------------------------------
-- 2. PRODUTOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id         VARCHAR(20)   NOT NULL,
    nome       VARCHAR(120)  NOT NULL,
    preco      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantidade INT           NOT NULL DEFAULT 0,
    criado_em  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Dados migrados de produtos.json
INSERT IGNORE INTO produtos (id, nome, preco, quantidade) VALUES
    ('1',  'água',        12.00, 23),
    ('2',  'Cerveja',      3.50, 42),
    ('11', 'coca cola 2L', 1.00,  1);

-- ------------------------------------------------------------
-- 3. CLIENTES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clientes (
    cpf           CHAR(14)      NOT NULL,
    nome          VARCHAR(120)  NOT NULL,
    endereco      VARCHAR(200)  NOT NULL DEFAULT '',
    telefone      VARCHAR(20)   NOT NULL DEFAULT '',
    total_compras DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    criado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cpf)
) ENGINE=InnoDB;

-- Dados migrados de clientes.json
INSERT IGNORE INTO clientes (cpf, nome, endereco, telefone, total_compras) VALUES
    ('229.873.678-21', 'Edson Salame', 'Rua pica pau, 67', '(42) 98982-9192', 3.50);

-- ------------------------------------------------------------
-- 4. FECHAMENTOS DE CAIXA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fechamentos_caixa (
    id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    operador        VARCHAR(60)   NOT NULL,
    data            DATE          NOT NULL,
    hora_abertura   TIME          NOT NULL,
    hora_fechamento TIME          NOT NULL,
    valor_abertura  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    saldo_sistema   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    valor_informado DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    diferenca       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    observacao      TEXT,
    criado_em       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. MOVIMENTAÇÕES DE CAIXA (sangria / suprimento)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS movimentacoes_caixa (
    id            INT UNSIGNED                 NOT NULL AUTO_INCREMENT,
    fechamento_id INT UNSIGNED                 NULL,
    tipo          ENUM('sangria','suprimento') NOT NULL,
    valor         DECIMAL(12,2)                NOT NULL,
    observacao    VARCHAR(255)                 NOT NULL DEFAULT '',
    hora          DATETIME                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (fechamento_id)
        REFERENCES fechamentos_caixa(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. VENDAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vendas (
    id          VARCHAR(32)   NOT NULL,
    operador    VARCHAR(60)   NOT NULL,
    cliente_cpf CHAR(14)      NULL,
    total       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    criado_em   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (cliente_cpf)
        REFERENCES clientes(cpf) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Dados migrados de vendas.json
INSERT IGNORE INTO vendas (id, operador, cliente_cpf, total, criado_em) VALUES
    ('venda_69e1259953f5e', 'admin', NULL,             1.00,  '2026-04-16 20:08:25'),
    ('venda_69e125c442118', 'admin', NULL,             1.00,  '2026-04-16 20:09:08'),
    ('venda_69e126a046b63', 'admin', NULL,             2.00,  '2026-04-16 20:12:48'),
    ('venda_6a09409707aa6', 'admin', NULL,             1.00,  '2026-05-17 06:14:15'),
    ('venda_6a0940b6c2263', 'admin', NULL,             2.00,  '2026-05-17 06:14:46'),
    ('venda_6a0941438c783', 'caixa', NULL,            25.00,  '2026-05-17 06:17:07'),
    ('venda_6a0bd5958808f', 'caixa', NULL,             3.50,  '2026-05-19 05:14:29'),
    ('venda_6a0bd5e3a41f0', 'admin', NULL,             3.50,  '2026-05-19 05:15:47'),
    ('venda_6a0bd7f5bfb25', 'caixa', NULL,             1.00,  '2026-05-19 05:24:37'),
    ('venda_6a0bdca763259', 'caixa', '229.873.678-21', 3.50,  '2026-05-19 05:44:39');

-- ------------------------------------------------------------
-- 7. ITENS DA VENDA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS venda_itens (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    venda_id   VARCHAR(32)   NOT NULL,
    produto_id VARCHAR(20)   NOT NULL,
    nome       VARCHAR(120)  NOT NULL,
    preco      DECIMAL(10,2) NOT NULL,
    quantidade INT           NOT NULL,
    total      DECIMAL(12,2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (venda_id)
        REFERENCES vendas(id)   ON DELETE CASCADE,
    FOREIGN KEY (produto_id)
        REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Itens migrados de vendas.json
INSERT IGNORE INTO venda_itens (venda_id, produto_id, nome, preco, quantidade, total) VALUES
    ('venda_69e1259953f5e', '11', 'coca cola 2L',  1.00,  1,  1.00),
    ('venda_69e125c442118', '11', 'coca cola 2L',  1.00,  1,  1.00),
    ('venda_69e126a046b63', '11', 'coca cola 2L',  1.00,  2,  2.00),
    ('venda_6a09409707aa6', '11', 'coca cola 2L',  1.00,  1,  1.00),
    ('venda_6a0940b6c2263', '11', 'coca cola 2L',  1.00,  2,  2.00),
    ('venda_6a0941438c783', '11', 'coca cola 2L',  1.00, 25, 25.00),
    ('venda_6a0bd5958808f', '2',  'Cerveja',        3.50,  1,  3.50),
    ('venda_6a0bd5e3a41f0', '2',  'Cerveja',        3.50,  1,  3.50),
    ('venda_6a0bd7f5bfb25', '11', 'coca cola 2L',  1.00,  1,  1.00),
    ('venda_6a0bdca763259', '2',  'Cerveja',        3.50,  1,  3.50);
