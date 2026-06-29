-- ============================================================
-- Migration: 017_criar_tabela_contratos.sql
-- Projeto: Steos v4.53.2
-- ============================================================

CREATE TABLE IF NOT EXISTS `contratos` (
  `idContratos` int(11) NOT NULL AUTO_INCREMENT,
  `clientes_id` int(11) NOT NULL,
  `tipoContrato` varchar(100) NOT NULL,
  `dataInicial` date DEFAULT NULL,
  `dataFinal` date DEFAULT NULL,
  `nomeContratos` varchar(255) NOT NULL,
  `descricaoContratos` text DEFAULT NULL,
  `valorContrato` decimal(10,2) DEFAULT 0.00,
  `valorDesconto` decimal(10,2) DEFAULT 0.00,
  `valorTotal` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `faturado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`idContratos`),
  KEY `fk_contratos_clientes1_idx` (`clientes_id`),
  CONSTRAINT `fk_contratos_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
