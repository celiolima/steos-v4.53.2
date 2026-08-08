-- ============================================================
-- Migration: 023_create_forma_pagamento.sql
-- Projeto: steosSteos v4.53.2
-- ============================================================

CREATE TABLE IF NOT EXISTS `forma_pagamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `forma_pagamento` (`nome`) VALUES 
('Dinheiro'), 
('Cartão de Crédito'), 
('Cartão de Débito'), 
('Boleto'), 
('Pix'), 
('Cheque'), 
('Transferência Bancária');

TRUNCATE TABLE `grupo_financeiro`;
INSERT IGNORE INTO `grupo_financeiro` (`nome`) VALUES 
('FIXO INDIRETO'), 
('FIXO DIRETO'), 
('VARIAVEL DIRETO'), 
('VARIAVEL INDIRETO'), 
('RECEITA');
