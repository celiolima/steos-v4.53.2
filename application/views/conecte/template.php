<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Área do Cliente - <?php echo $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item("csrf_token_name") ?>">
    <meta name="csrf-cookie-name" content="<?= config_item("csrf_cookie_name") ?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-media.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css" />
    <link href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
    <style>
        /* Ajustes de Responsividade e Visão Mobile - Conecte */
        @media (max-width: 767px) {
            .widget-content, .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                width: 100% !important;
            }
            .table {
                width: 100% !important;
                max-width: none !important;
            }
            /* Em tabelas comuns de listagem (não faturas/relatórios), evitar quebra de linha em datas/valores */
            .table:not(.invoice-head table):not(.invoice-content table) th, 
            .table:not(.invoice-head table):not(.invoice-content table) td {
                white-space: nowrap !important;
                vertical-align: middle !important;
            }
            /* Colunas longas (Observações e Responsável) com quebra de linha normal nas listagens */
            .table:not(.invoice-head table):not(.invoice-content table) td:nth-child(6), 
            .table:not(.invoice-head table):not(.invoice-content table) td:nth-child(2) {
                white-space: normal !important;
                min-width: 180px;
            }
            /* =========================================================
               AJUSTES PARA TELAS DE VISUALIZAÇÃO E RELATÓRIOS (OS / Vendas)
               ========================================================= */
            /* Permitir quebra normal de texto e palavras nas faturas e visualizações */
            .invoice-content table, .invoice-content table th, .invoice-content table td,
            .invoice-head table, .invoice-head table th, .invoice-head table td {
                white-space: normal !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }
            /* Cabeçalho da OS/Venda (Logo | Emitente | Nº OS): empilhar verticalmente em cards limpos */
            .invoice-head table, .invoice-head tbody, .invoice-head tr, .invoice-head td {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                box-sizing: border-box !important;
            }
            .invoice-head td {
                padding: 8px 0 !important;
            }
            .invoice-head td img {
                margin: 0 auto !important;
                max-height: 80px !important;
            }
            .invoice-head td:nth-child(2) {
                border-top: 1px dashed #ccc !important;
                border-bottom: 1px dashed #ccc !important;
                margin: 10px 0 !important;
                padding: 12px 5px !important;
            }
            /* Tabelas de Status, Datas, Descrições e Equipamentos: transformar em blocos/cards responsivos */
            .invoice-content .table-condensed, .invoice-content .table-condensed tbody,
            .invoice-content .table-condensed tr, .invoice-content .table-condensed td,
            .invoice-content .table-bordered:not(#tblProdutos):not(#tabela):not(.tbl-produtos):not(.tbl-servicos) td,
            .invoice-content .table-bordered:not(#tblProdutos):not(#tabela):not(.tbl-produtos):not(.tbl-servicos) th {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                text-align: left !important;
            }
            .invoice-content .table-condensed td,
            .invoice-content .table-bordered:not(#tblProdutos):not(#tabela):not(.tbl-produtos):not(.tbl-servicos) td {
                padding: 8px 10px !important;
                border-top: none !important;
                border-bottom: 1px solid #eee !important;
            }
            .invoice-content .table-condensed tr,
            .invoice-content .table-bordered:not(#tblProdutos):not(#tabela):not(.tbl-produtos):not(.tbl-servicos) tr {
                border: 1px solid #ddd !important;
                margin-bottom: 12px !important;
                border-radius: 6px !important;
                background: #fafafa !important;
            }
            /* Tabelas financeiras de Produtos e Serviços: manter estrutura de colunas com scroll horizontal */
            #tblProdutos, .tbl-produtos, .tbl-servicos {
                display: table !important;
                width: 100% !important;
            }
            #tblProdutos th, #tblProdutos td, .tbl-produtos th, .tbl-produtos td, .tbl-servicos th, .tbl-servicos td {
                display: table-cell !important;
                white-space: nowrap !important;
            }
            /* Formulários de busca e filtros no mobile */
            form[method="get"] .span3, form[method="get"] .span4, form[method="get"] .span2,
            .form-pesquisa-os .span3, .form-pesquisa-os .span4, .form-pesquisa-os .span2 {
                margin-left: 0 !important;
                margin-bottom: 8px !important;
                width: 100% !important;
                box-sizing: border-box;
            }
            form[method="get"] input, form[method="get"] select, form[method="get"] button, form[method="get"] a.button {
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 5px !important;
                box-sizing: border-box;
            }
            form[method="get"] .span4 input.datepicker {
                width: 48% !important;
                display: inline-block !important;
            }
            /* Ajuste dos botões de ação na tabela no mobile */
            .table td a.btn, .table td a.btn-nwe, .table td a.btn-nwe3, .table td a.btn-nwe4 {
                display: inline-block;
                margin-bottom: 3px;
            }
        }

        /* ==========================================================================
           Estilo Premium e Moderno para a Pílula do Usuário no Topo (Desktop e Mobile)
           Elimina a caixa cinza escuro do navbar-inverse e cria um visual limpo/translúcido
           ========================================================================== */
        .navebarn {
            background: transparent !important;
            box-shadow: none !important;
        }
        .navebarn #user-nav {
            background: transparent !important;
            border: none !important;
        }
        .navebarn #user-nav > ul {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
        .navebarn #user-nav > ul > li {
            background: transparent !important;
            border: none !important;
        }
        .navebarn #user-nav > ul > li > a {
            display: flex !important;
            align-items: center !important;
            height: 28px !important;
            line-height: 26px !important;
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 20px !important;
            padding: 0 16px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            text-shadow: none !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease !important;
        }
        .navebarn #user-nav > ul > li > a:hover,
        .navebarn #user-nav > ul > li.open > a {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
        }
        .navebarn #user-nav > ul > li > a > i {
            color: #ffffff !important;
            font-size: 1.3em !important;
            margin-right: 6px !important;
            line-height: 1 !important;
        }
        .client-top-name {
            display: inline-block !important;
            max-width: 450px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            color: #ffffff !important;
        }

        /* Ajustes Exclusivos de Alinhamento para Visão Mobile - Conecte */
        @media (max-width: 767px) {
            #header {
                height: 48px !important;
                min-height: 48px !important;
            }
            #header h1 a {
                height: 48px !important;
            }
            .navebarn {
                position: absolute !important;
                top: 11px !important;
                right: 12px !important;
                left: auto !important;
                width: auto !important;
                margin: 0 !important;
                height: auto !important;
                z-index: 9999 !important;
            }
            .navebarn #user-nav {
                width: auto !important;
                margin: 0 !important;
            }
            .navebarn #user-nav > ul {
                margin: 0 !important;
                position: relative !important;
                top: 0 !important;
            }
            .navebarn #user-nav > ul > li {
                left: 0 !important;
            }
            .navebarn #user-nav > ul > li > a {
                height: 26px !important;
                line-height: 24px !important;
                padding: 0 14px !important;
                font-size: 12px !important;
            }
            .navebarn #user-nav > ul > li > a > i {
                font-size: 1.2em !important;
                margin-right: 5px !important;
            }
            .client-top-name {
                max-width: 300px !important;
            }
        }
        @media (max-width: 480px) {
            #header {
                height: 44px !important;
                min-height: 44px !important;
            }
            #header h1 a {
                height: 44px !important;
            }
            .navebarn {
                top: 9px !important;
                right: 8px !important;
            }
            .navebarn #user-nav > ul > li > a {
                height: 25px !important;
                line-height: 23px !important;
                padding: 0 12px !important;
                font-size: 11.5px !important;
            }
            .navebarn #user-nav > ul > li > a > i {
                font-size: 1.15em !important;
                margin-right: 4px !important;
            }
            .client-top-name {
                max-width: 220px !important;
            }
        }
    </style>
</head>

<body>
    <!--Header-part-->
    <div id="header">
        <h1><a href="dashboard.html"><?php echo $this->config->item('app_name'); ?></a></h1>
    </div>
    <!--close-Header-part-->

    <!--top-Header-menu-->
    <div class="navebarn" style="margin-top: -60px;height: 25px;margin-bottom: 15px">
        <div id="user-nav" class="navbar navbar-inverse">
            <ul class="nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='bx bx-user-circle iconN1'></i> <span class="client-top-name"><?= $this->session->userdata('nome') ?></span> </a>
                    <ul class="dropdown-menu">
                        <li class=""><a title="Meu Perfil" href="<?php echo base_url() ?>index.php/mine/conta"><i class="fas fa-user"></i> <span class="text">Meu Perfil</span></a></li>
                        <li class="divider"></li>
                        <li class=""><a title="Sair" href="<?php echo base_url() ?>index.php/mine/sair"><i class="fas fa-sign-out-alt"></i> <span class="text">Sair</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <nav id="sidebar">
        <div id="newlog">
            <div class="icon2">
                <img src="<?php echo base_url() ?>assets/img/logo-two.png">
            </div>
            <div class="title1">
                <img src="<?= base_url() ?>assets/img/logo-steos-branco.png">
            </div>
        </div>
        <a href="#" class="visible-phone">
            <div class="mode">
                <div class="moon-menu">
                    <i class='bx bx-chevron-right iconX open-2'></i>
                    <i class='bx bx-chevron-left iconX close-2'></i>
                </div>
            </div>
        </a>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links" style="position: relative;">
                    <li class="<?php if (isset($menuPainel)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/painel"><i class='bx bx-home-alt iconX'></i> <span class="title">Painel</span></a></li>
                    <li class="<?php if (isset($menuConta)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/conta"><i class="bx bx-user-circle iconX"></i> <span class="title">Minha Contas</span></a></li>
                    <li class="<?php if (isset($menuOs)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/os"><i class='bx bx-spreadsheet iconX'></i> <span class="title">Ordens de Serviço</span></a></li>
                    <?php
                    $ci = &get_instance();
                    $ci->load->model('contratos_model');
                    $totalContratosCliente = $ci->contratos_model->count('contratos', ['clientes_id' => $ci->session->userdata('cliente_id')]);
                    if ($totalContratosCliente > 0) :
                    ?>
                    <li class="<?php if (isset($menuContratos)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/contratos"><i class='bx bx-file iconX'></i> <span class="title">Contratos</span></a></li>
                    <?php endif; ?>
                    <li class="<?php if (isset($menuVendas)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/compras"><i class='bx bx-cart-alt iconX'></i> <span class="title">Compras</span></a></li>
                    <li class="<?php if (isset($menuCobrancas)) {
                        echo 'active';
                    }; ?>"><a class="tip-bottom" title="" href="<?php echo base_url() ?>index.php/mine/cobrancas"><i class='bx bx-credit-card-front iconX'></i> <span class="title">Cobranças</span></a></li>
                </ul>
            </div>

            <div class="botton-content">
                <li class="">
                    <a class="tip-bottom" title="" href="<?= site_url('login/sair'); ?>">
                        <i class='bx bx-log-out-circle iconX'></i>
                        <span class="title">Sair</span></a>
                </li>
            </div>

        </div>
    </nav>

    <div style="background: #f3f4f6" id="content">
        <div class="content-header" id="content-header">
            <div id="breadcrumb"><a href="<?php echo base_url(); ?>index.php/mine/painel" title="Painel" class="tip-bottom"><i class="fas fa-home"></i> Painel</a></div>
        </div>

        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span12">
                    <?php if ($var = $this->session->flashdata('success')) : ?>
                        <script>
                            var rawMsg = <?php echo json_encode($var); ?> || "";
                            var cleanText = rawMsg.replace(/<br\s*[\/]?>/gi, "\n").replace(/\r\n/g, "\n").replace(/\n+/g, "\n").trim();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ title: "Sucesso!", html: cleanText.replace(/\n/g, "<br>"), icon: "success" });
                            } else {
                                swal("Sucesso!", cleanText, "success");
                            }
                        </script>
                    <?php endif; ?>
                    <?php if ($var = $this->session->flashdata('error')) : ?>
                        <script>
                            var rawMsg = <?php echo json_encode($var); ?> || "";
                            var cleanText = rawMsg.replace(/<br\s*[\/]?>/gi, "\n").replace(/\r\n/g, "\n").replace(/\n+/g, "\n").trim();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ title: "Falha!", html: cleanText.replace(/\n/g, "<br>"), icon: "error" });
                            } else {
                                swal("Falha!", cleanText, "error");
                            }
                        </script>
                    <?php endif; ?>
                    <?php if (isset($output)) {
                        $this->load->view($output);
                    } ?>

                </div>
            </div>

        </div>
    </div>
    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12">
            <?= date('Y') ?> &copy;
            <?php echo $this->config->item('app_name'); ?> - Versão:
            <?php echo $this->config->item('app_version'); ?>
        </div>
    </div>

    <!-- javascript
================================================== -->

    <script src="<?= base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/matrix.js"></script>
</body>

</html>
