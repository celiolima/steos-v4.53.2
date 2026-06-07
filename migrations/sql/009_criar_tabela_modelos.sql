-- ============================================================
-- Migration: 009_criar_tabela_modelos.sql
-- Projeto: steosMapos v4.53.2
-- Fase 2 — Banco de Dados
-- Descrição: Templates reutilizáveis de documentos com texto rico
--            e referência ao usuário criador.
-- Dependências: usuarios (tabela existente)
-- ============================================================

CREATE TABLE IF NOT EXISTS `modelos` (
  `idModelos` int(11) NOT NULL AUTO_INCREMENT,
  `dataModelo` date NOT NULL DEFAULT (CURRENT_DATE) COMMENT 'Data de criação do modelo',
  `refModelo` varchar(255) NOT NULL COMMENT 'Referência/título do modelo',
  `textoModelo` text DEFAULT NULL COMMENT 'Conteúdo HTML do template (gerado pelo wysihtml5)',
  `usuarios_id` int(11) DEFAULT NULL COMMENT 'FK: usuarios.id_admin — usuário criador',
  PRIMARY KEY (`idModelos`),
  KEY `idx_modelos_usuario` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Templates reutilizáveis de documentos com texto rico';
