-- ============================================================
-- Migration: 008_criar_tabela_gasolina.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Histórico de abastecimentos de cada veículo.
--            Registra velocímetro de entrada/saída e data.
-- Dependências: veiculos (007)
-- ============================================================

CREATE TABLE IF NOT EXISTS `gasolina` (
  `idGasolina` int(11) NOT NULL AUTO_INCREMENT,
  `veiculos_id` int(11) NOT NULL COMMENT 'FK: veiculos.idVeiculos',
  `saldoAnterior` int(11) NOT NULL DEFAULT '0' COMMENT 'Km no início do período',
  `saldoAtual` int(11) NOT NULL DEFAULT '0' COMMENT 'Km no fim do período (odômetro atual)',
  `velocimetroEntrada` int(11) NOT NULL DEFAULT '0' COMMENT 'Km no velocímetro na entrada',
  `velocimetroSaida` int(11) NOT NULL DEFAULT '0' COMMENT 'Km no velocímetro na saída',
  `dataLancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do registro',
  PRIMARY KEY (`idGasolina`),
  KEY `idx_gasolina_veiculo` (`veiculos_id`),
  KEY `idx_gasolina_data` (`dataLancamento`),
  CONSTRAINT `fk_gasolina_veiculo` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`idVeiculos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Histórico de abastecimentos e odômetro por veículo';
