-- ============================================================
-- Migration: 011_criar_tabela_classificacao_financeira.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Categorias financeiras para classificação de
--            lançamentos por grupo, centro de gastos e tipo.
-- Dependências: Nenhuma (tabela independente)
-- NOTA: Esta tabela é referenciada por lancamentos.classificacao_fin
--       (coluna adicionada na migration 014)
-- ============================================================

CREATE TABLE IF NOT EXISTS `classificacao_financeira` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_financeiro` varchar(255) NOT NULL COMMENT 'Grupo financeiro (ex: Receitas Operacionais, Despesas Fixas)',
  `classificacao_fin` varchar(255) NOT NULL COMMENT 'Subclassificação (ex: Serviços, Produtos, Aluguel)',
  `centro_de_gastos` varchar(255) DEFAULT NULL COMMENT 'Centro de custo/gastos (ex: Administrativo, Técnico)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Categorias para classificação de lançamentos financeiros';
