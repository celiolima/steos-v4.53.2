-- ============================================================
-- Migration: 022_rollback_financeiro_aux.sql
-- Projeto: steosSteos v4.53.2
-- Descrição: Rollback das tabelas auxiliares centro_gastos e grupo_financeiro
-- ============================================================

DROP TABLE IF EXISTS `centro_gastos`;
DROP TABLE IF EXISTS `grupo_financeiro`;
DROP TABLE IF EXISTS `forma_pagamento`;
