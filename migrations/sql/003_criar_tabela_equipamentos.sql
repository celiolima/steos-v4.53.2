-- ============================================================
-- Migration: 003_criar_tabela_equipamentos.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Cadastro de equipamentos dos clientes com
--            tipo, modelo, marca, série, cor, voltagem, potência.
-- Dependências: marcas (010) - nota: criar marcas antes se usar FK
-- ============================================================

CREATE TABLE IF NOT EXISTS `equipamentos` (
  `idEquipamentos` int(11) NOT NULL AUTO_INCREMENT,
  `equipamento` varchar(255) NOT NULL COMMENT 'Tipo/nome do equipamento',
  `descricao` varchar(255) DEFAULT NULL COMMENT 'Descrição adicional',
  `modelo` varchar(255) DEFAULT NULL COMMENT 'Modelo do equipamento',
  `marcas` varchar(100) DEFAULT NULL COMMENT 'Marca do equipamento',
  `num_serie` varchar(100) DEFAULT NULL COMMENT 'Número de série',
  `cor` varchar(50) DEFAULT NULL COMMENT 'Cor do equipamento',
  `voltagem` varchar(20) DEFAULT NULL COMMENT 'Voltagem (ex: 110V, 220V, Bivolt)',
  `potencia` varchar(20) DEFAULT NULL COMMENT 'Potência do equipamento',
  PRIMARY KEY (`idEquipamentos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cadastro de equipamentos dos clientes';
