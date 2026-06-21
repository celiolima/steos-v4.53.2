-- ============================================================
-- Migration: 016_criar_tabela_assinatura.sql
-- Projeto: steosSteos v4.53.2
-- ============================================================

CREATE TABLE IF NOT EXISTS `assinatura` (
  `idAssinatura` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nameAssinatura` varchar(85) NOT NULL,
  `assinatura` text NOT NULL,
  `os_id` int(11) NOT NULL,
  `doc` varchar(20) DEFAULT NULL,
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idAssinatura`),
  KEY `fk_os1` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
