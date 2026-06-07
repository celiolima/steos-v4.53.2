-- SCRIPT DE MIGRACAO: STEOS PARA MAPOS V4.53.2
-- GERADO AUTOMATICAMENTE

DROP TABLE IF EXISTS `tecnicos`;
CREATE TABLE `tecnicos` (
  `idTecnicos` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `comissao_servico` decimal(10,2) DEFAULT 0.00,
  `comissao_produto` decimal(10,2) DEFAULT 0.00,
  `dataCadastro` date NOT NULL,
  `dataExpiracao` date DEFAULT NULL,
  `ativo` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tecnicos_os`;
CREATE TABLE `tecnicos_os` (
  `idTecnicos_os` int(11) NOT NULL,
  `os_id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `tecnicoName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `lancamentos_contas`;
CREATE TABLE `lancamentos_contas` (
  `idlancamentos_contas` int(11) NOT NULL,
  `dataLancamento` datetime NOT NULL,
  `tipo_lacamento` varchar(50) DEFAULT NULL,
  `lancamento` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `contas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `gasolina`;
CREATE TABLE `gasolina` (
  `idGasolina` int(11) NOT NULL AUTO_INCREMENT,
  `dataLancamento` date NOT NULL,
  `saldoAnterior` int(11) NOT NULL,
  `saldoAtual` int(11) NOT NULL,
  `velocimetroEntrada` int(11) DEFAULT 0,
  `velocimetroSaida` int(11) DEFAULT 0,
  `veiculos_id` int(11) NOT NULL,
  PRIMARY KEY (`idGasolina`) USING BTREE,
  KEY `fk_veiculos1` (`veiculos_id`) USING BTREE,
  CONSTRAINT `fk_veiculos1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`idVeiculos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `veiculos`;
CREATE TABLE `veiculos` (
  `idVeiculos` int(11) NOT NULL AUTO_INCREMENT,
  `nomeVeiculo` varchar(255) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `autonomia` int(11) NOT NULL,
  `abastecimentoLitro` decimal(10,2) DEFAULT 0.00,
  `valorGasolinAabastecimento` decimal(10,2) DEFAULT 0.00,
  `ultimoAbastecimentoData` date NOT NULL,
  `abastecimentoKm` int(11) DEFAULT NULL,
  `ultimaTrocaDeOleoData` date NOT NULL,
  `ultimaTrocaOleoVeloc` int(11) DEFAULT NULL,
  `oleoKmVeloc` int(11) NOT NULL,
  `saldoAtualVeic` int(11) NOT NULL,
  `abastecer` tinyint(1) NOT NULL DEFAULT 0,
  `trocarOleo` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idVeiculos`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `modelos`;
CREATE TABLE `modelos` (
  `idModelos` int(11) NOT NULL,
  `dataModelo` date DEFAULT NULL,
  `refModelo` varchar(15) DEFAULT NULL,
  `textoModelo` mediumtext DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `classificacao_financeira`;
CREATE TABLE `classificacao_financeira` (
  `idClassFin` int(11) NOT NULL,
  `nomeClassFin` varchar(50) NOT NULL,
  `grupoFinaceiro` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- ADICAO DE COLUNAS CUSTOMIZADAS NA TABELA OS
ALTER TABLE `os`
  ADD COLUMN `vendedor` VARCHAR(100) NULL AFTER `defeito`,
  ADD COLUMN `afaturar` TINYINT(1) NOT NULL DEFAULT 0 AFTER `vendedor`,
  ADD COLUMN `manPrevnt` TINYINT(1) NOT NULL DEFAULT 0 AFTER `faturado`,
  ADD COLUMN `signature` MEDIUMTEXT NULL AFTER `manPrevnt`;

-- ADICAO DE COLUNAS CUSTOMIZADAS NA TABELA LANCAMENTOS
ALTER TABLE `lancamentos`
  ADD COLUMN `centro_de_gastos` VARCHAR(100) NULL AFTER `tipo`,
  ADD COLUMN `classificacao_fin` VARCHAR(100) NULL AFTER `centro_de_gastos`,
  ADD COLUMN `grupo_financeiro` VARCHAR(100) NULL AFTER `classificacao_fin`;

-- INJECAO DE CONFIGURACOES EXCLUSIVAS
INSERT IGNORE INTO `configuracoes` (`config`, `valor`) VALUES
  ('modulo_veiculos', '1'),
  ('modulo_tecnicos', '1'),
  ('modulo_contas', '1'),
  ('modulo_modelos', '1');
