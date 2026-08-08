<?php

class Migration_create_forma_pagamento extends CI_Migration
{
    public function up()
    {
        // Cria a tabela caso ela não tenha sido criada pelas correções anteriores
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `forma_pagamento` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nome` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insere os valores padrão
        $formas = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Boleto', 'Pix', 'Cheque', 'Transferência Bancária'];
        foreach ($formas as $f) {
            $this->db->query("INSERT IGNORE INTO `forma_pagamento` (`nome`) VALUES ('$f')");
        }

        // Corrige os valores do grupo_financeiro, caso tenham ficado com Receita/Despesa na VPS
        $this->db->query("TRUNCATE TABLE `grupo_financeiro`");
        $grupos = ['FIXO INDIRETO', 'FIXO DIRETO', 'VARIAVEL DIRETO', 'VARIAVEL INDIRETO', 'RECEITA'];
        foreach ($grupos as $g) {
            $this->db->query("INSERT IGNORE INTO `grupo_financeiro` (`nome`) VALUES ('$g')");
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `forma_pagamento`;");
    }
}
