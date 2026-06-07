-- ============================================================
-- Migration: 007_criar_tabela_veiculos.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Controle de frota com odômetro, alertas de
--            abastecimento e troca de óleo.
-- Dependências: Nenhuma (tabela independente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `veiculos` (
  `idVeiculos` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL COMMENT 'Identificação do veículo (placa, nome)',
  `saldoAtualVeic` int(11) NOT NULL DEFAULT '0' COMMENT 'Odômetro atual em km',
  `abastecimentoKm` int(11) NOT NULL DEFAULT '0' COMMENT 'Km para alertar próximo abastecimento',
  `ultimoAbastecimentoData` date DEFAULT NULL COMMENT 'Data do último abastecimento',
  `oleoKmVeloc` int(11) NOT NULL DEFAULT '0' COMMENT 'Km para alerta de troca de óleo',
  `ultimaTrocaDeOleoData` date DEFAULT NULL COMMENT 'Data da última troca de óleo',
  `abastecer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=alerta de abastecimento ativo',
  `trocarOleo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=alerta de troca de óleo ativo',
  PRIMARY KEY (`idVeiculos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Controle de frota de veículos';
