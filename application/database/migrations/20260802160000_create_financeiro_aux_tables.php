<?php

class Migration_create_financeiro_aux_tables extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `centro_gastos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `grupo_financeiro` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `forma_pagamento` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");


        // Inserir dados padrão (Retrocompatibilidade)
        $centros = ['SERVICOS', 'VENDAS', 'OPERACIONAIS', 'RH', 'ADMINISTRATIVO', 'MARKETING', 'GASTOS FINANCEIROS', 'INVESTIMENTOS'];
        foreach ($centros as $c) {
            $this->db->query("INSERT IGNORE INTO `centro_gastos` (`nome`) VALUES ('$c')");
        }

        $grupos = ['FIXO INDIRETO', 'FIXO DIRETO', 'VARIAVEL DIRETO', 'VARIAVEL INDIRETO', 'RECEITA'];
        foreach ($grupos as $g) {
            $this->db->query("INSERT IGNORE INTO `grupo_financeiro` (`nome`) VALUES ('$g')");
        }

        $formas = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Boleto', 'Pix', 'Cheque', 'Transferência Bancária'];
        foreach ($formas as $f) {
            $this->db->query("INSERT IGNORE INTO `forma_pagamento` (`nome`) VALUES ('$f')");
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `centro_gastos`;");
        $this->db->query("DROP TABLE IF EXISTS `grupo_financeiro`;");
        $this->db->query("DROP TABLE IF EXISTS `forma_pagamento`;");
    }
}
