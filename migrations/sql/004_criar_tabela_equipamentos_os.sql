-- ============================================================
-- Migration: 004_criar_tabela_equipamentos_os.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Pivot N:N entre OS e equipamentos.
--            Vincula equipamentos específicos de clientes a cada OS.
-- Dependências: equipamentos (003), os (existente), clientes (existente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `equipamentos_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) NOT NULL COMMENT 'FK: os.idOs',
  `clientes_id` int(11) DEFAULT NULL COMMENT 'FK: clientes.idClientes (redundância para facilitar consultas)',
  `equipamentos_id` int(11) NOT NULL COMMENT 'FK: equipamentos.idEquipamentos',
  PRIMARY KEY (`id`),
  KEY `idx_equipamentos_os_os` (`os_id`),
  KEY `idx_equipamentos_os_equip` (`equipamentos_id`),
  CONSTRAINT `fk_equipamentos_os_os` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_equipamentos_os_equip` FOREIGN KEY (`equipamentos_id`) REFERENCES `equipamentos` (`idEquipamentos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot N:N entre Ordens de Serviço e Equipamentos';
