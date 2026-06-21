<?php

class Migration_create_base extends CI_Migration
{
    public function up()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $this->db->query('DROP TABLE IF EXISTS `anexos`');
        $this->db->query('CREATE TABLE `anexos` (
  `idAnexos` int(11) NOT NULL AUTO_INCREMENT,
  `anexo` varchar(45) DEFAULT NULL,
  `thumb` varchar(45) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `path` varchar(300) DEFAULT NULL,
  `os_id` int(11) DEFAULT NULL,
  `os_Lancamentos` int(11) DEFAULT NULL,
  PRIMARY KEY (`idAnexos`) USING BTREE,
  KEY `fk_anexos_os1` (`os_id`) USING BTREE,
  KEY `fk_anexos_lancamentos1` (`os_Lancamentos`) USING BTREE,
  CONSTRAINT `fk_anexos_lancamentos1` FOREIGN KEY (`os_Lancamentos`) REFERENCES `lancamentos` (`idLancamentos`),
  CONSTRAINT `fk_anexos_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `anotacoes_os`');
        $this->db->query('CREATE TABLE `anotacoes_os` (
  `idAnotacoes` int(11) NOT NULL AUTO_INCREMENT,
  `anotacao` varchar(255) NOT NULL,
  `data_hora` datetime NOT NULL,
  `os_id` int(11) NOT NULL,
  PRIMARY KEY (`idAnotacoes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `assinatura`');
        $this->db->query('CREATE TABLE `assinatura` (
  `idAssinatura` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nameAssinatura` varchar(85) NOT NULL,
  `assinatura` text NOT NULL,
  `os_id` int(11) NOT NULL,
  `doc` varchar(20) DEFAULT NULL,
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idAssinatura`) USING BTREE,
  KEY `fk_os1` (`os_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1469 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;');

        $this->db->query('DROP TABLE IF EXISTS `bancos`');
        $this->db->query('CREATE TABLE `bancos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `categorias`');
        $this->db->query('CREATE TABLE `categorias` (
  `idCategorias` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(80) DEFAULT NULL,
  `cadastro` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `tipo` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`idCategorias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `ci_sessions`');
        $this->db->query('CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `classificacao_financeira`');
        $this->db->query('CREATE TABLE `classificacao_financeira` (
  `idClassFin` int(11) NOT NULL AUTO_INCREMENT,
  `nomeClassFin` varchar(50) NOT NULL,
  `grupoFinaceiro` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idClassFin`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `clientes`');
        $this->db->query('CREATE TABLE `clientes` (
  `idClientes` int(11) NOT NULL AUTO_INCREMENT,
  `asaas_id` varchar(255) DEFAULT NULL,
  `nomeCliente` varchar(255) NOT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `pessoa_fisica` tinyint(1) NOT NULL DEFAULT 1,
  `documento` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `dataCadastro` date DEFAULT NULL,
  `rua` varchar(70) DEFAULT NULL,
  `numero` varchar(30) DEFAULT NULL,
  `bairro` varchar(70) DEFAULT NULL,
  `cidade` varchar(70) DEFAULT NULL,
  `estado` varchar(70) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `complemento` varchar(150) DEFAULT NULL,
  `fornecedor` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idClientes`)
) ENGINE=InnoDB AUTO_INCREMENT=2402 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `cobrancas`');
        $this->db->query('CREATE TABLE `cobrancas` (
  `idCobranca` int(11) NOT NULL AUTO_INCREMENT,
  `charge_id` varchar(255) DEFAULT NULL,
  `conditional_discount_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `custom_id` int(11) DEFAULT NULL,
  `expire_at` date NOT NULL,
  `message` varchar(255) NOT NULL,
  `payment_method` varchar(11) DEFAULT NULL,
  `payment_url` varchar(255) DEFAULT NULL,
  `request_delivery_address` varchar(64) DEFAULT NULL,
  `status` varchar(36) NOT NULL,
  `total` varchar(15) DEFAULT NULL,
  `barcode` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `payment` varchar(64) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `vendas_id` int(11) DEFAULT NULL,
  `os_id` int(11) DEFAULT NULL,
  `clientes_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCobranca`),
  KEY `fk_cobrancas_os1` (`os_id`),
  KEY `fk_cobrancas_vendas1` (`vendas_id`),
  KEY `fk_cobrancas_clientes1` (`clientes_id`),
  CONSTRAINT `fk_cobrancas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`),
  CONSTRAINT `fk_cobrancas_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`),
  CONSTRAINT `fk_cobrancas_vendas1` FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `configuracoes`');
        $this->db->query('CREATE TABLE `configuracoes` (
  `idConfig` int(11) NOT NULL AUTO_INCREMENT,
  `config` varchar(20) NOT NULL,
  `valor` text DEFAULT NULL,
  PRIMARY KEY (`idConfig`),
  UNIQUE KEY `config` (`config`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `contas`');
        $this->db->query('CREATE TABLE `contas` (
  `idContas` int(11) NOT NULL AUTO_INCREMENT,
  `conta` varchar(45) DEFAULT NULL,
  `banco` varchar(45) DEFAULT NULL,
  `tipo` varchar(80) DEFAULT NULL,
  `agencia` varchar(50) DEFAULT NULL,
  `numero` varchar(45) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `cadastro` date DEFAULT NULL,
  `vencimento_cartao` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idContas`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `contratos`');
        $this->db->query('CREATE TABLE `contratos` (
  `idContratos` int(11) NOT NULL AUTO_INCREMENT,
  `dataInicial` date DEFAULT NULL,
  `dataFinal` date DEFAULT NULL,
  `nomeContratos` varchar(255) NOT NULL,
  `descricaoContratos` text DEFAULT NULL,
  `valorContrato` decimal(10,2) DEFAULT 0.00,
  `valorDesconto` decimal(10,2) DEFAULT 0.00,
  `valorTotal` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`idContratos`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `documentos`');
        $this->db->query('CREATE TABLE `documentos` (
  `idDocumentos` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(70) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `path` varchar(300) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `cadastro` date DEFAULT NULL,
  `categoria` varchar(80) DEFAULT NULL,
  `tipo` varchar(15) DEFAULT NULL,
  `tamanho` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idDocumentos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `email_queue`');
        $this->db->query('CREATE TABLE `email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) NOT NULL,
  `cc` varchar(255) DEFAULT NULL,
  `bcc` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum(\'pending\',\'sending\',\'sent\',\'failed\') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `headers` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5382 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `emitente`');
        $this->db->query('CREATE TABLE `emitente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `cnpj` varchar(45) DEFAULT NULL,
  `ie` varchar(50) DEFAULT NULL,
  `rua` varchar(70) DEFAULT NULL,
  `numero` varchar(15) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `uf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url_logo` varchar(225) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `equipamentos`');
        $this->db->query('CREATE TABLE `equipamentos` (
  `idEquipamentos` int(11) NOT NULL AUTO_INCREMENT,
  `equipamento` varchar(150) NOT NULL,
  `num_serie` varchar(80) DEFAULT NULL,
  `modelo` varchar(80) DEFAULT NULL,
  `cor` varchar(45) DEFAULT NULL,
  `descricao` varchar(150) DEFAULT NULL,
  `potencia` varchar(45) DEFAULT NULL,
  `voltagem` varchar(45) DEFAULT NULL,
  `marcas` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idEquipamentos`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `equipamentos_os`');
        $this->db->query('CREATE TABLE `equipamentos_os` (
  `idEquipamentos_os` int(11) NOT NULL AUTO_INCREMENT,
  `serie` varchar(50) DEFAULT NULL,
  `modelo` varchar(80) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `descricao` varchar(150) DEFAULT NULL,
  `potecia` varchar(20) DEFAULT NULL,
  `voltagem` varchar(20) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `local` varchar(150) DEFAULT NULL,
  `defeito_declarado` varchar(200) DEFAULT NULL,
  `defeito_encontrado` varchar(200) DEFAULT NULL,
  `solucao` varchar(45) DEFAULT NULL,
  `os_id` int(11) DEFAULT NULL,
  `clientes_id` int(11) DEFAULT NULL,
  `equipamento` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idEquipamentos_os`) USING BTREE,
  KEY `fk_equipamentos_os_os1_idx` (`os_id`) USING BTREE,
  KEY `fk_equipanentos_clientes1_idx` (`clientes_id`) USING BTREE,
  CONSTRAINT `fk_equipamentos_os_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`),
  CONSTRAINT `fk_equipanentos_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `garantias`');
        $this->db->query('CREATE TABLE `garantias` (
  `idGarantias` int(11) NOT NULL AUTO_INCREMENT,
  `dataGarantia` date DEFAULT NULL,
  `refGarantia` varchar(15) DEFAULT NULL,
  `textoGarantia` text DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idGarantias`),
  KEY `fk_garantias_usuarios1` (`usuarios_id`),
  CONSTRAINT `fk_garantias_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `gasolina`');
        $this->db->query('CREATE TABLE `gasolina` (
  `idGasolina` int(11) NOT NULL AUTO_INCREMENT,
  `dataLancamento` datetime NOT NULL,
  `saldoAtual` int(11) DEFAULT 0,
  `kmRodadoDia` int(11) DEFAULT 0,
  `velocimetroEntrada` int(11) DEFAULT 0,
  `velocimetroSaida` int(11) DEFAULT 0,
  `veiculos_id` int(11) NOT NULL,
  PRIMARY KEY (`idGasolina`) USING BTREE,
  KEY `fk_veiculos1` (`veiculos_id`) USING BTREE,
  CONSTRAINT `fk_veiculos1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`idVeiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=969 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `itens_de_vendas`');
        $this->db->query('CREATE TABLE `itens_de_vendas` (
  `idItens` int(11) NOT NULL AUTO_INCREMENT,
  `subTotal` decimal(10,2) DEFAULT 0.00,
  `quantidade` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT 0.00,
  `vendas_id` int(11) NOT NULL,
  `produtos_id` int(11) NOT NULL,
  PRIMARY KEY (`idItens`),
  KEY `fk_itens_de_vendas_vendas1` (`vendas_id`),
  KEY `fk_itens_de_vendas_produtos1` (`produtos_id`),
  CONSTRAINT `fk_itens_de_vendas_produtos1` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`idProdutos`),
  CONSTRAINT `fk_itens_de_vendas_vendas1` FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `lancamentos`');
        $this->db->query('CREATE TABLE `lancamentos` (
  `idLancamentos` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT 0.00,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `tipo_desconto` varchar(8) DEFAULT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `baixado` tinyint(1) DEFAULT 0,
  `cliente_fornecedor` varchar(255) DEFAULT NULL,
  `centro_de_gastos` varchar(255) DEFAULT NULL,
  `classificacao_fin` varchar(255) DEFAULT NULL,
  `grupo_finaceiro` varchar(255) DEFAULT NULL,
  `forma_pgto` varchar(100) DEFAULT NULL,
  `tipo` varchar(45) DEFAULT NULL,
  `anexo` varchar(250) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `nu_receita` int(11) DEFAULT NULL,
  `clientes_id` int(11) DEFAULT NULL,
  `categorias_id` int(11) DEFAULT NULL,
  `contas_id` int(11) DEFAULT NULL,
  `vendas_id` int(11) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  PRIMARY KEY (`idLancamentos`) USING BTREE,
  KEY `fk_lancamentos_clientes1` (`clientes_id`) USING BTREE,
  KEY `fk_lancamentos_categorias1_idx` (`categorias_id`) USING BTREE,
  KEY `fk_lancamentos_contas1_idx` (`contas_id`) USING BTREE,
  KEY `fk_lancamentos_usuarios1` (`usuarios_id`) USING BTREE,
  CONSTRAINT `fk_lancamentos_categorias1` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`idCategorias`),
  CONSTRAINT `fk_lancamentos_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`),
  CONSTRAINT `fk_lancamentos_contas1` FOREIGN KEY (`contas_id`) REFERENCES `contas` (`idContas`),
  CONSTRAINT `fk_lancamentos_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=3944 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `lancamentos_contas`');
        $this->db->query('CREATE TABLE `lancamentos_contas` (
  `idlancamentos_contas` int(11) NOT NULL AUTO_INCREMENT,
  `dataLancamento` datetime NOT NULL,
  `tipo_lacamento` varchar(50) DEFAULT NULL,
  `lancamento` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `contas_id` int(11) NOT NULL,
  PRIMARY KEY (`idlancamentos_contas`) USING BTREE,
  KEY `fk_contas1` (`contas_id`) USING BTREE,
  CONSTRAINT `fk_contas1` FOREIGN KEY (`contas_id`) REFERENCES `contas` (`idContas`)
) ENGINE=InnoDB AUTO_INCREMENT=3556 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `logs`');
        $this->db->query('CREATE TABLE `logs` (
  `idLogs` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(80) DEFAULT NULL,
  `tarefa` varchar(255) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idLogs`)
) ENGINE=InnoDB AUTO_INCREMENT=31222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `marcas`');
        $this->db->query('CREATE TABLE `marcas` (
  `idMarcas` int(11) NOT NULL AUTO_INCREMENT,
  `marca` varchar(100) DEFAULT NULL,
  `cadastro` date DEFAULT NULL,
  `situacao` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idMarcas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `modelos`');
        $this->db->query('CREATE TABLE `modelos` (
  `idModelos` int(11) NOT NULL AUTO_INCREMENT,
  `dataModelo` date DEFAULT NULL,
  `refModelo` varchar(15) DEFAULT NULL,
  `textoModelo` mediumtext DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idModelos`) USING BTREE,
  KEY `fk_modelos_usuarios1` (`usuarios_id`) USING BTREE,
  CONSTRAINT `fk_modelos_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `os`');
        $this->db->query('CREATE TABLE `os` (
  `idOs` int(11) NOT NULL AUTO_INCREMENT,
  `dataAbertura` datetime DEFAULT NULL,
  `dataInicial` datetime DEFAULT NULL,
  `dataFinal` datetime DEFAULT NULL,
  `garantia` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `laudoTecnico` text DEFAULT NULL,
  `vendedor` varchar(50) DEFAULT NULL,
  `valorTotal` decimal(10,2) DEFAULT 0.00,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `tipo_desconto` varchar(8) DEFAULT NULL,
  `clientes_id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `lancamento` int(11) DEFAULT NULL,
  `afaturar` tinyint(1) DEFAULT NULL,
  `faturado` tinyint(1) NOT NULL,
  `garantias_id` int(11) DEFAULT NULL,
  `signature` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `equipamentos_id` int(11) DEFAULT NULL,
  `descricaoProduto` text DEFAULT NULL,
  `defeito` text DEFAULT NULL,
  `tipo` varchar(45) DEFAULT NULL,
  `local` varchar(45) DEFAULT NULL,
  `defeito_encontrado` text DEFAULT NULL,
  PRIMARY KEY (`idOs`),
  KEY `fk_os_clientes1` (`clientes_id`),
  KEY `fk_os_usuarios1` (`usuarios_id`),
  KEY `fk_os_lancamentos1` (`lancamento`),
  KEY `fk_os_garantias1` (`garantias_id`),
  KEY `fk_os_equipamentos_1` (`equipamentos_id`),
  CONSTRAINT `fk_os_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`),
  CONSTRAINT `fk_os_equipamentos_1` FOREIGN KEY (`equipamentos_id`) REFERENCES `equipamentos` (`idEquipamentos`),
  CONSTRAINT `fk_os_lancamentos1` FOREIGN KEY (`lancamento`) REFERENCES `lancamentos` (`idLancamentos`),
  CONSTRAINT `fk_os_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=2255 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `permissoes`');
        $this->db->query('CREATE TABLE `permissoes` (
  `idPermissao` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) NOT NULL,
  `permissoes` text DEFAULT NULL,
  `situacao` tinyint(1) DEFAULT NULL,
  `data` date DEFAULT NULL,
  PRIMARY KEY (`idPermissao`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `produtos`');
        $this->db->query('CREATE TABLE `produtos` (
  `idProdutos` int(11) NOT NULL AUTO_INCREMENT,
  `codDeBarra` varchar(70) NOT NULL,
  `descricao` varchar(80) NOT NULL,
  `unidade` varchar(10) DEFAULT NULL,
  `precoCompra` decimal(10,2) DEFAULT NULL,
  `precoVenda` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL,
  `estoqueMinimo` int(11) DEFAULT NULL,
  `saida` tinyint(1) DEFAULT NULL,
  `entrada` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idProdutos`)
) ENGINE=InnoDB AUTO_INCREMENT=514 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `produtos_os`');
        $this->db->query('CREATE TABLE `produtos_os` (
  `idProdutos_os` int(11) NOT NULL AUTO_INCREMENT,
  `quantidade` int(11) NOT NULL,
  `descricao` varchar(80) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT 0.00,
  `os_id` int(11) NOT NULL,
  `produtos_id` int(11) NOT NULL,
  `subTotal` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`idProdutos_os`),
  KEY `fk_produtos_os_os1` (`os_id`),
  KEY `fk_produtos_os_produtos1` (`produtos_id`),
  CONSTRAINT `fk_produtos_os_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`),
  CONSTRAINT `fk_produtos_os_produtos1` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`idProdutos`)
) ENGINE=InnoDB AUTO_INCREMENT=2370 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `resets_de_senha`');
        $this->db->query('CREATE TABLE `resets_de_senha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `token` varchar(255) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `token_utilizado` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `servicos`');
        $this->db->query('CREATE TABLE `servicos` (
  `idServicos` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `descricao` varchar(45) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idServicos`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `servicos_os`');
        $this->db->query('CREATE TABLE `servicos_os` (
  `idServicos_os` int(11) NOT NULL AUTO_INCREMENT,
  `servico` varchar(80) DEFAULT NULL,
  `quantidade` double DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT 0.00,
  `os_id` int(11) NOT NULL,
  `servicos_id` int(11) NOT NULL,
  `subTotal` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`idServicos_os`),
  KEY `fk_servicos_os_os1` (`os_id`),
  KEY `fk_servicos_os_servicos1` (`servicos_id`),
  CONSTRAINT `fk_servicos_os_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`),
  CONSTRAINT `fk_servicos_os_servicos1` FOREIGN KEY (`servicos_id`) REFERENCES `servicos` (`idServicos`)
) ENGINE=InnoDB AUTO_INCREMENT=2267 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `tecnicos`');
        $this->db->query('CREATE TABLE `tecnicos` (
  `idTecnicos` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) NOT NULL,
  `comissao_servico` decimal(10,2) DEFAULT 0.00,
  `comissao_produto` decimal(10,2) DEFAULT 0.00,
  `dataCadastro` date NOT NULL,
  `dataExpiracao` date DEFAULT NULL,
  `ativo` char(1) NOT NULL DEFAULT \'1\',
  PRIMARY KEY (`idTecnicos`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `tecnicos_os`');
        $this->db->query('CREATE TABLE `tecnicos_os` (
  `idTecnicos_os` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `tecnicoName` varchar(50) NOT NULL,
  PRIMARY KEY (`idTecnicos_os`) USING BTREE,
  KEY `fk_os_id1_idx` (`os_id`) USING BTREE,
  KEY `fk_tecnico_id1_os_idx` (`tecnico_id`) USING BTREE,
  CONSTRAINT `fk_os_id1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`),
  CONSTRAINT `fk_tecnico_os_id1` FOREIGN KEY (`tecnico_id`) REFERENCES `tecnicos` (`idTecnicos`)
) ENGINE=InnoDB AUTO_INCREMENT=3196 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `usuarios`');
        $this->db->query('CREATE TABLE `usuarios` (
  `idUsuarios` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `cpf` varchar(20) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(70) DEFAULT NULL,
  `numero` varchar(15) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `situacao` tinyint(1) NOT NULL,
  `dataCadastro` date NOT NULL,
  `permissoes_id` int(11) NOT NULL,
  `dataExpiracao` date DEFAULT NULL,
  `url_image_user` varchar(255) DEFAULT NULL,
  `tecnico` varchar(50) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`idUsuarios`),
  KEY `fk_usuarios_permissoes1_idx` (`permissoes_id`),
  CONSTRAINT `fk_usuarios_permissoes1` FOREIGN KEY (`permissoes_id`) REFERENCES `permissoes` (`idPermissao`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `veiculos`');
        $this->db->query('CREATE TABLE `veiculos` (
  `idVeiculos` int(11) NOT NULL AUTO_INCREMENT,
  `nomeVeiculo` varchar(255) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `autonomia` int(11) NOT NULL,
  `abastecimentoLitro` decimal(10,2) DEFAULT 0.00,
  `valorGasolinAabastecimento` decimal(10,2) DEFAULT 0.00,
  `ultimoAbastecimentoData` datetime NOT NULL,
  `abastecimentoKm` int(11) DEFAULT NULL,
  `ultimaTrocaDeOleoData` datetime NOT NULL,
  `ultimaTrocaOleoVeloc` int(11) DEFAULT NULL,
  `oleoKmVeloc` int(11) NOT NULL,
  `saldoAtualVeic` int(11) NOT NULL,
  `abastecer` tinyint(1) NOT NULL DEFAULT 0,
  `trocarOleo` tinyint(1) NOT NULL DEFAULT 0,
  `situacao` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idVeiculos`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('DROP TABLE IF EXISTS `vendas`');
        $this->db->query('CREATE TABLE `vendas` (
  `idVendas` int(11) NOT NULL AUTO_INCREMENT,
  `dataVenda` date DEFAULT NULL,
  `valorTotal` decimal(10,2) DEFAULT 0.00,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `tipo_desconto` varchar(8) DEFAULT NULL,
  `faturado` tinyint(1) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `observacoes_cliente` text DEFAULT NULL,
  `clientes_id` int(11) NOT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  `lancamentos_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVendas`),
  KEY `fk_vendas_clientes1` (`clientes_id`),
  KEY `fk_vendas_usuarios1` (`usuarios_id`),
  KEY `fk_vendas_lancamentos1` (`lancamentos_id`),
  CONSTRAINT `fk_vendas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`),
  CONSTRAINT `fk_vendas_lancamentos1` FOREIGN KEY (`lancamentos_id`) REFERENCES `lancamentos` (`idLancamentos`),
  CONSTRAINT `fk_vendas_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $this->db->query('DROP TABLE IF EXISTS `vendas`');
        $this->db->query('DROP TABLE IF EXISTS `veiculos`');
        $this->db->query('DROP TABLE IF EXISTS `usuarios`');
        $this->db->query('DROP TABLE IF EXISTS `tecnicos_os`');
        $this->db->query('DROP TABLE IF EXISTS `tecnicos`');
        $this->db->query('DROP TABLE IF EXISTS `servicos_os`');
        $this->db->query('DROP TABLE IF EXISTS `servicos`');
        $this->db->query('DROP TABLE IF EXISTS `resets_de_senha`');
        $this->db->query('DROP TABLE IF EXISTS `produtos_os`');
        $this->db->query('DROP TABLE IF EXISTS `produtos`');
        $this->db->query('DROP TABLE IF EXISTS `permissoes`');
        $this->db->query('DROP TABLE IF EXISTS `os`');
        $this->db->query('DROP TABLE IF EXISTS `modelos`');
        $this->db->query('DROP TABLE IF EXISTS `marcas`');
        $this->db->query('DROP TABLE IF EXISTS `logs`');
        $this->db->query('DROP TABLE IF EXISTS `lancamentos_contas`');
        $this->db->query('DROP TABLE IF EXISTS `lancamentos`');
        $this->db->query('DROP TABLE IF EXISTS `itens_de_vendas`');
        $this->db->query('DROP TABLE IF EXISTS `gasolina`');
        $this->db->query('DROP TABLE IF EXISTS `garantias`');
        $this->db->query('DROP TABLE IF EXISTS `equipamentos_os`');
        $this->db->query('DROP TABLE IF EXISTS `equipamentos`');
        $this->db->query('DROP TABLE IF EXISTS `emitente`');
        $this->db->query('DROP TABLE IF EXISTS `email_queue`');
        $this->db->query('DROP TABLE IF EXISTS `documentos`');
        $this->db->query('DROP TABLE IF EXISTS `contratos`');
        $this->db->query('DROP TABLE IF EXISTS `contas`');
        $this->db->query('DROP TABLE IF EXISTS `configuracoes`');
        $this->db->query('DROP TABLE IF EXISTS `cobrancas`');
        $this->db->query('DROP TABLE IF EXISTS `clientes`');
        $this->db->query('DROP TABLE IF EXISTS `classificacao_financeira`');
        $this->db->query('DROP TABLE IF EXISTS `ci_sessions`');
        $this->db->query('DROP TABLE IF EXISTS `categorias`');
        $this->db->query('DROP TABLE IF EXISTS `bancos`');
        $this->db->query('DROP TABLE IF EXISTS `assinatura`');
        $this->db->query('DROP TABLE IF EXISTS `anotacoes_os`');
        $this->db->query('DROP TABLE IF EXISTS `anexos`');

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
