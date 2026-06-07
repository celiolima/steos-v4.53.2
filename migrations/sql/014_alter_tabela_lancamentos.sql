-- ============================================================
-- Migration: 014_alter_tabela_lancamentos.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Adiciona 4 colunas à tabela `lancamentos` existente.
--            Habilita: centro de gastos, classificação financeira,
--            grupo financeiro e forma de pagamento nos lançamentos.
-- Dependências: lancamentos (tabela existente)
-- RISCO: ALTO — tabela financeira de produção
--
-- ⚠️ NOTA IMPORTANTE: O steos tinha o typo "grupo_finaceiro" (sem 'n').
--    Esta migration usa o nome CORRETO: "grupo_financeiro".
--    Todos os arquivos PHP foram atualizados para usar o nome correto.
-- ============================================================

-- Adiciona coluna: centro_de_gastos
ALTER TABLE `lancamentos`
  ADD COLUMN IF NOT EXISTS `centro_de_gastos` VARCHAR(100) NULL DEFAULT NULL
    COMMENT 'Centro de custo ou gastos do lançamento'
    AFTER `tipo`;

-- Adiciona coluna: classificacao_fin (classificação financeira)
ALTER TABLE `lancamentos`
  ADD COLUMN IF NOT EXISTS `classificacao_fin` VARCHAR(100) NULL DEFAULT NULL
    COMMENT 'Subclassificação financeira do lançamento'
    AFTER `centro_de_gastos`;

-- Adiciona coluna: grupo_financeiro (TYPO CORRIGIDO: era "grupo_finaceiro" no steos)
ALTER TABLE `lancamentos`
  ADD COLUMN IF NOT EXISTS `grupo_financeiro` VARCHAR(100) NULL DEFAULT NULL
    COMMENT 'Grupo financeiro macro do lançamento (ex: Receitas, Despesas)'
    AFTER `classificacao_fin`;

-- Adiciona coluna: forma_pgto (forma de pagamento)
ALTER TABLE `lancamentos`
  ADD COLUMN IF NOT EXISTS `forma_pgto` VARCHAR(50) NULL DEFAULT NULL
    COMMENT 'Forma de pagamento (ex: Dinheiro, PIX, Cartão, Boleto)'
    AFTER `grupo_financeiro`;

-- Índices para facilitar filtros e relatórios
CREATE INDEX IF NOT EXISTS `idx_lancamentos_centro` ON `lancamentos` (`centro_de_gastos`);
CREATE INDEX IF NOT EXISTS `idx_lancamentos_class` ON `lancamentos` (`classificacao_fin`);
CREATE INDEX IF NOT EXISTS `idx_lancamentos_grupo` ON `lancamentos` (`grupo_financeiro`);
