<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>
<style>
    .texto {
        resize: none;
        overflow: hidden;
        min-height: 50px;
        width: 50%;
    }
</style>

<?php
$cliente = "teste";
$totalProdutos = 0;
?>
<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-book"></i>
                </span>
                <h5>Modelo</h5>
                <div class="buttons">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eModelo')) {
                        echo '<a title="Editar  Modelo" class="button btn btn-mini btn-success" href="' . base_url() . 'index.php/modelos/editar/' . $result->idModelos . '">
    <span class="button__icon"><i class="bx bx-edit"></i> </span> <span class="button__text">Editar</span></a>';
                    } ?>
                    <a target="_blank" title="Imprimir" class="button btn btn-mini btn-inverse" href="<?php echo site_url() ?>/modelos/imprimir/<?php echo $result->idModelos; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span> <span class="button__text">Imprimir</span></a>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head">
                        <table class="table">
                            <tbody>
                                <?php if ($emitente == null) { ?>
                                    <tr>
                                        <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/mapos/emitente">Configurar</a>
                                            <<<< /td>
                                    </tr> <?php
                                        } else { ?> <tr>
                                        <td style="width: 25%"><img src=" <?php echo $emitente->url_logo; ?> "></td>
                                        <td> <span style="font-size: 20px; ">
                                                <?php echo $emitente->nome; ?></span> </br><span>
                                                <?php echo $emitente->cnpj; ?> </br>
                                                <?php echo $emitente->rua . ', nº:' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?> </span> </br> <span> E-mail:
                                                <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?></span></td>
                                        <td style="width: 18%; text-align: center">#Modelo: <span>
                                                <?php echo $result->idModelos ?></span></br> </br> <span>Emissão:
                                                <?php echo date('d/m/Y'); ?></span>
                                        </td>
                                    </tr>
                                <?php
                                        } ?>
                            </tbody>
                        </table>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td style="width: 40%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5>Responsável</h5>
                                                </span>
                                                <span>
                                                    <?php echo $result->nome ?></span> <br />
                                                <span>Telefone:
                                                    <?php echo $result->telefone ?></span><br />
                                                <span>Email:
                                                    <?php echo $result->email ?></span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td style="width: 30%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5>Data</h5>
                                                </span>
                                                <span> <?php echo date('d/m/Y', strtotime($result->dataModelo)); ?></span> <br />

                                            </li>
                                        </ul>
                                    </td>
                                    <td style="width: 30%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5>Ref. Termo</h5>
                                                </span>
                                                <span><?php echo $result->refModelo;  ?> </span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table">
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span class="texto"><?php
                                                                    $nome = "LANDISCAPE";
                                                                    $nome2 = "CONDONDOMINIO LANDISCAPE";
                                                                    $texto_db = htmlspecialchars_decode($result->textoModelo);
                                                                    echo str_replace(
                                                                        array(
                                                                            '{{cliente1}}',
                                                                            '{{cliente2}}',
                                                                        ),
                                                                        array(
                                                                            $nome,
                                                                            $nome2,
                                                                        ),
                                                                        $texto_db
                                                                    );
                                                                    //echo htmlspecialchars_decode($result->textoModelo) 
                                                                    ?></span>

                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function auto_grow(element) {
        element.style.height = "5px";
        element.style.height = (element.scrollHeight) + "px";
    }

    $(document).ready(function() {

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
        $('.editor').trumbowyg({
            lang: 'pt_br'
        });
    });
</script>