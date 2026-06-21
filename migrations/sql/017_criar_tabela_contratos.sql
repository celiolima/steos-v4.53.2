-- ============================================================
-- Migration: 017_criar_tabela_contratos.sql
-- Projeto: steosSteos v4.53.2
-- ============================================================

CREATE TABLE IF NOT EXISTS `contratos` (
  `idContratos` int(11) NOT NULL AUTO_INCREMENT,
  `dataInicial` date DEFAULT NULL,
  `dataFinal` date DEFAULT NULL,
  `nomeContratos` varchar(255) NOT NULL,
  `descricaoContratos` text DEFAULT NULL,
  `valorContrato` decimal(10,2) DEFAULT 0.00,
  `valorDesconto` decimal(10,2) DEFAULT 0.00,
  `valorTotal` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`idContratos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
