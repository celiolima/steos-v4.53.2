-- ============================================================
-- Migration: 007_criar_tabela_veiculos.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Controle de frota com odômetro, alertas de
--            abastecimento e troca de óleo.
-- Dependências: Nenhuma (tabela independente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `veiculos` (
  `idVeiculos` int(11) NOT NULL AUTO_INCREMENT,
  `nomeVeiculo` varchar(255) NOT NULL COMMENT 'Identificação do veículo (placa, nome)',
  `observacoes` text DEFAULT NULL,
  `autonomia` int(11) NOT NULL DEFAULT '0',
  `abastecimentoLitro` decimal(10,2) DEFAULT '0.00',
  `valorGasolinAabastecimento` decimal(10,2) DEFAULT '0.00',
  `ultimoAbastecimentoData` datetime NOT NULL,
  `abastecimentoKm` int(11) DEFAULT NULL,
  `ultimaTrocaDeOleoData` datetime NOT NULL,
  `ultimaTrocaOleoVeloc` int(11) DEFAULT NULL,
  `oleoKmVeloc` int(11) NOT NULL DEFAULT '0',
  `saldoAtualVeic` int(11) NOT NULL DEFAULT '0',
  `abastecer` tinyint(1) NOT NULL DEFAULT '0',
  `trocarOleo` tinyint(1) NOT NULL DEFAULT '0',
  `situacao` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idVeiculos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Controle de frota de veículos';
