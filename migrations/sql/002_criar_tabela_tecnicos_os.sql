-- ============================================================
-- Migration: 002_criar_tabela_tecnicos_os.sql
-- Projeto: steosSteos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Pivot N:N entre OS e técnicos.
--            Permite múltiplos técnicos por OS.
-- Dependências: tecnicos (001), os (tabela existente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `tecnicos_os` (
  `idTecnicos_os` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) NOT NULL COMMENT 'FK: os.idOs',
  `tecnico_id` int(11) NOT NULL COMMENT 'FK: tecnicos.idTecnicos',
  `tecnicoName` varchar(50) NOT NULL COMMENT 'Nome do tecnico denormalizado',
  PRIMARY KEY (`idTecnicos_os`),
  KEY `idx_tecnicos_os_os` (`os_id`),
  KEY `idx_tecnicos_os_tecnicos` (`tecnico_id`),
  CONSTRAINT `fk_tecnicos_os_os` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tecnicos_os_tecnicos` FOREIGN KEY (`tecnico_id`) REFERENCES `tecnicos` (`idTecnicos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot N:N entre Ordens de Serviço e Técnicos';
