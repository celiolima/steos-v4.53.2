-- ============================================================
-- Migration: 010_criar_tabela_marcas.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Cadastro de marcas de equipamentos para uso
--            como seleção na ficha de equipamentos.
-- Dependências: Nenhuma (tabela independente — referenciada por equipamentos)
-- ============================================================

CREATE TABLE IF NOT EXISTS `marcas` (
  `idMarcas` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL COMMENT 'Nome da marca do equipamento',
  PRIMARY KEY (`idMarcas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Marcas de equipamentos para seleção no cadastro';

-- Dados iniciais básicos para marcas comuns
INSERT IGNORE INTO `marcas` (`nome`) VALUES
  ('Samsung'),
  ('LG'),
  ('Brastemp'),
  ('Electrolux'),
  ('Consul'),
  ('Philips'),
  ('Sony'),
  ('Panasonic'),
  ('Whirlpool'),
  ('Outra');
