-- ============================================================
-- Migration: 009_criar_tabela_modelos.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Templates reutilizáveis de documentos com texto rico
--            e referência ao usuário criador.
-- Dependências: usuarios (tabela existente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `modelos` (
  `idModelos` int(11) NOT NULL AUTO_INCREMENT,
  `dataModelo` date DEFAULT NULL,
  `refModelo` varchar(15) DEFAULT NULL,
  `textoModelo` mediumtext DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idModelos`),
  KEY `idx_modelos_usuario` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Templates reutilizáveis de documentos com texto rico';
