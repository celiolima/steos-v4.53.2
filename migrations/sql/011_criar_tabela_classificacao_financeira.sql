-- ============================================================
-- Migration: 011_criar_tabela_classificacao_financeira.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Categorias financeiras para classificação de
--            lançamentos por grupo, centro de gastos e tipo.
-- Dependências: Nenhuma (tabela independente)
-- NOTA: Esta tabela é referenciada por lancamentos.classificacao_fin
--       (coluna adicionada na migration 014)
-- ============================================================

CREATE TABLE IF NOT EXISTS `classificacao_financeira` (
  `idClassFin` int(11) NOT NULL AUTO_INCREMENT,
  `nomeClassFin` varchar(255) NOT NULL,
  `grupoFinaceiro` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idClassFin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Categorias para classificação de lançamentos financeiros';
