-- ============================================================
-- Migration: 001_criar_tabela_tecnicos.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Cria tabela de técnicos com controle de comissão,
--            data de validade e status ativo/inativo.
-- Dependências: Nenhuma (tabela independente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `tecnicos` (
  `idTecnicos` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) NOT NULL COMMENT 'Nome completo do técnico',
  `comissao_servico` decimal(10,2) DEFAULT '0.00' COMMENT 'Percentual de comissão sobre serviços (%)',
  `comissao_produto` decimal(10,2) DEFAULT '0.00' COMMENT 'Percentual de comissão sobre produtos (%)',
  `dataCadastro` date NOT NULL DEFAULT (CURRENT_DATE) COMMENT 'Data de cadastro no sistema',
  `dataExpiracao` date DEFAULT NULL COMMENT 'Data de expiração do contrato/registro',
  `ativo` char(1) NOT NULL DEFAULT '1' COMMENT '1=ativo, 0=inativo',
  PRIMARY KEY (`idTecnicos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cadastro de técnicos com controle de comissão';
