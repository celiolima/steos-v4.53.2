CREATE TABLE IF NOT EXISTS `veiculos_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `veiculo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `conteudo` text NOT NULL,
  `data_hora` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `veiculo_id` (`veiculo_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_veiculo_log` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`idVeiculos`) ON DELETE CASCADE,
  CONSTRAINT `fk_usuario_log` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
