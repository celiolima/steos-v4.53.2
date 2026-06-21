-- ============================================================
-- Migration: 008_criar_tabela_gasolina.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Histórico de abastecimentos de cada veículo.
--            Registra velocímetro de entrada/saída e data.
-- Dependências: veiculos (007)
-- ============================================================

CREATE TABLE IF NOT EXISTS `gasolina` (
  `idGasolina` int(11) NOT NULL AUTO_INCREMENT,
  `dataLancamento` datetime NOT NULL,
  `saldoAtual` int(11) DEFAULT '0',
  `kmRodadoDia` int(11) DEFAULT '0',
  `velocimetroEntrada` int(11) DEFAULT '0',
  `velocimetroSaida` int(11) DEFAULT '0',
  `veiculos_id` int(11) NOT NULL,
  PRIMARY KEY (`idGasolina`),
  KEY `idx_gasolina_veiculo` (`veiculos_id`),
  KEY `idx_gasolina_data` (`dataLancamento`),
  CONSTRAINT `fk_gasolina_veiculo` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`idVeiculos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Histórico de abastecimentos e odômetro por veículo';
