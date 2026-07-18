ALTER TABLE `os` 
ADD COLUMN `manutPreventiva` TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `os_checklists` (
  `idChecklist` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `contratos_id` INT(11) NOT NULL,
  `data_criacao` DATETIME NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Aberto',
  `observacoes` TEXT,
  PRIMARY KEY (`idChecklist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `os_checklists_itens` (
  `idItem` INT(11) NOT NULL AUTO_INCREMENT,
  `checklist_id` INT(11) NOT NULL,
  `descricao` TEXT NOT NULL,
  `concluido` TINYINT(1) DEFAULT 0,
  `observacoes` TEXT,
  PRIMARY KEY (`idItem`),
  KEY `fk_os_checklists_itens` (`checklist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
