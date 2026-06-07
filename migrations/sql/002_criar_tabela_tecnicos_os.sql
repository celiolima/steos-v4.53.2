-- ============================================================
-- Migration: 002_criar_tabela_tecnicos_os.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Pivot N:N entre OS e técnicos.
--            Permite múltiplos técnicos por OS.
-- Dependências: tecnicos (001), os (tabela existente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `tecnicos_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) NOT NULL COMMENT 'FK: os.idOs',
  `tecnicos_id` int(11) NOT NULL COMMENT 'FK: tecnicos.idTecnicos',
  PRIMARY KEY (`id`),
  KEY `idx_tecnicos_os_os` (`os_id`),
  KEY `idx_tecnicos_os_tecnicos` (`tecnicos_id`),
  CONSTRAINT `fk_tecnicos_os_os` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tecnicos_os_tecnicos` FOREIGN KEY (`tecnicos_id`) REFERENCES `tecnicos` (`idTecnicos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot N:N entre Ordens de Serviço e Técnicos';
