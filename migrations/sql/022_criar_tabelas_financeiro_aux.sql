-- ============================================================
-- Migration: 022_criar_tabelas_financeiro_aux.sql
-- Projeto: steosSteos v4.53.2
-- Descrição: Cria as tabelas auxiliares centro_gastos e grupo_financeiro
--            e popula com os dados padrão.
-- ============================================================

CREATE TABLE IF NOT EXISTS `centro_gastos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `grupo_financeiro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forma_pagamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados padrão (Retrocompatibilidade)
INSERT IGNORE INTO `centro_gastos` (`nome`) VALUES 
('SERVICOS'), 
('VENDAS'), 
('OPERACIONAIS'), 
('RH'), 
('ADMINISTRATIVO'), 
('MARKETING'), 
('GASTOS FINANCEIROS'), 
('INVESTIMENTOS');

INSERT IGNORE INTO `grupo_financeiro` (`nome`) VALUES 
('FIXO INDIRETO'), 
('FIXO DIRETO'), 
('VARIAVEL DIRETO'), 
('VARIAVEL INDIRETO'), 
('RECEITA');

INSERT IGNORE INTO `forma_pagamento` (`nome`) VALUES 
('Dinheiro'), 
('Cartão de Crédito'), 
('Cartão de Débito'), 
('Boleto'), 
('Pix'), 
('Cheque'), 
('Transferência Bancária');

