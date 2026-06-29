<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<style>
    select {
        width: 70px;
    }
</style>
<?php
/* echo "<pre>";
print_r($tecnicos);
exit; */
?>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-diagnoses"></i>
        </span>
        <h5>Ordens de Serviço</h5>
    </div>
    <?php
    /* echo "<pre>";
    //print_r($this->session->userdata('tecnico'));
    print_r($this->session->userdata('permissao'));
    //print_r($this->data['results']);
    exit; */
    ?>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/os/gerenciar">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) { ?>
                <div class="span2">
                    <a href="<?php echo base_url(); ?>index.php/os/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Ordem de Serviço</span></a>

                </div>
            <?php
            } ?>
            <div class="span2">
                <input type="text" name="os" id="os" placeholder="Pesquisar Os Numero" class="span12" value="<?= $this->input->get('os') ? $this->input->get('os') : ''; ?>">
                <select name="tecnico" id="" class="span12">
                    <option value="">Todos técnicos</option>
                    <?php
                    if ($this->session->userdata('permissao') == '1' || $this->session->userdata('permissao') == '2') {
                        if ($this->input->get('tecnico')) {
                            echo '<option selected>' . $this->input->get('tecnico') . '</option>';
                        } else {
                            foreach ($tecnicos as $tecnico) {
                                echo '<option >' . $tecnico->nome . '</option>';
                            }
                        }
                    } else {
                        echo '<option selected>' . $this->session->userdata('tecnico') . '</option>';
                    }
                    ?>
                </select>
                <select class="span12" name="local" id="local" value="">
                    <option value="">Todos Locais</option>
                    <option value="Externo" <?= $this->input->get('local') == "Externo" ? "selected" : ""; ?>>Externo</option>
                    <option value="Interno" <?= $this->input->get('local') == "Interno" ? "selected" : ""; ?>>Interno</option>
                </select>
            </div>

            <div class="span2">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente a pesquisar" class="span12" value="">
                <input type="text" name="observacao" id="observacao" placeholder=" observacao a pesquisar" class="span12" value="">
                <select class="span12" name="tipo" id="tipo" value="">
                    <option value="">Todos os tipos</option>
                    <option value="Avulso" <?= $this->input->get('tipo') == "Avulso" ? "selected" : ""; ?>>Avulso</option>
                    <option value="Contrato" <?= $this->input->get('tipo') == "Contrato" ? "selected" : ""; ?>>Contrato</option>
                </select>
            </div>

            <div class="span2">
                <select name="status" id="" class="span12">
                    <?php
                    if ((int)$this->session->userdata('permissao') == 1 || (int)$this->session->userdata('permissao') == 2) { ?>
                        <option value="">Selecione status</option>
                        <option value="A Sair | Aguard Conclusão" <?= $this->input->get('status') == "A Sair | Aguard Conclusão" ? "selected" : ""; ?>>A Sair | Aguard Conclusão</option>
                        <option value="Manutenção Preventiva" <?= $this->input->get('status') == "Manutenção Preventiva" ? "selected" : ""; ?>>Manutenção Preventiva</option>
                        <option value="Faturado" <?= $this->input->get('status') == "Faturado" ? "selected" : ""; ?>>Faturado</option>
                        <option value="Em Andamento" <?= $this->input->get('status') == "Em Andamento" ? "selected" : ""; ?>>Em Andamento</option>
                        <option value="Orçamento" <?= $this->input->get('status') == "Orçamento" ? "selected" : ""; ?>>Orçamento</option>
                        <option value="Negociação" <?= $this->input->get('status') == "Negociação" ? "selected" : ""; ?>>Negociação</option>
                        <option value="Finalizado" <?= $this->input->get('status') == "Finalizado" ? "selected" : ""; ?>>Finalizado</option>
                        <option value="Cancelado" <?= $this->input->get('status') == "Cancelado" ? "selected" : ""; ?>>Cancelado</option>
                        <option value="Aguardando Peças" <?= $this->input->get('status') == "Aguardando Peças" ? "selected" : ""; ?>>Aguardando Peças</option>
                        <option value="Aprovado" <?= $this->input->get('status') == "Aprovado" ? "selected" : ""; ?>>Aprovado</option>
                    <?php    } else {  ?>
                        <option value="">Selecione status</option>
                        <option value="A Sair | Aguard Conclusão" <?= $this->input->get('status') == "A Sair | Aguard Conclusão" ? "selected" : ""; ?>>A Sair | Aguard Conclusão</option>
                        <option value="Manutenção Preventiva" <?= $this->input->get('status') == "Manutenção Preventiva" ? "selected" : ""; ?>>Manutenção Preventiva</option>
                        <option value="Em Andamento" <?= $this->input->get('status') == "Em Andamento" ? "selected" : ""; ?>>Em Andamento</option>
                        <option value="Finalizado" <?= $this->input->get('status') == "Finalizado" ? "selected" : ""; ?>>Finalizado</option>
                        <option value="Cancelado" <?= $this->input->get('status') == "Cancelado" ? "selected" : ""; ?>>Cancelado</option>
                    <?php   }
                    ?>
                </select>
                <select name="vendedor" id="" class="span12">
                    <option value="">Todos vendedor</option>
                    <?php
                    if ($this->session->userdata('permissao') == '1' || $this->session->userdata('permissao') == '2') {
                        if ($this->input->get('vendedor')) {
                            echo '<option selected>' . $this->input->get('vendedor') . '</option>';
                        } else {
                            foreach ($usuarios as $usuario) {
                                echo '<option >' . $usuario->nome . '</option>';
                            }
                        }
                    } else {
                        echo '<option selected>' . $this->session->userdata('nome_admin') . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="span2">
                <input type="text" name="data" autocomplete="off" id="data" placeholder="Data Inicial" class="span6 datepicker" value="<?= $this->input->get('data') ? $this->input->get('data') : '' ?>">
                <input type="text" name="data2" autocomplete="off" id="data2" placeholder="Data Final" class="span6 datepicker" value="<?= $this->input->get('data2') ? $this->input->get('data2') : '' ?>">
                <?php if ((int)$this->session->userdata('permissao') == 1) { ?>
                    <input type="checkbox" id="afaturar" name="afaturar" value="1" <?= $this->input->get('afaturar') == "1" ? "checked" : ""; ?>>
                    <label>A faturar</label>
                <?php    }   ?>
                <input type="checkbox" id="manPrevnt" name="manPrevnt" value="1" <?= $this->input->get('manPrevnt') == "1" ? "checked" : ""; ?>>
                <label>Manut Peventiva</label>

            </div>

            <div class="span2">
                <button class="button btn btn-mini btn-warning">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    <span class="button__text2">Pesquisar</span>
                </button>
                <a href="<?php echo base_url(); ?>index.php/os/" class="button btn btn-mini btn-success" style="max-width: 140px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpa</span></a>
            </div>

        </form>
    </div>
    <?php
    /*   echo "<pre>";
    print_r($results);
    exit; */
    ?>

    <div class="widget-box" style="margin-top: 8px">
        <div class="widget-content nopadding">
            <div class="table-responsive">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th width="20%">Cliente</th>
                            <th class="ph1">Técnico</th>
                            <th>Data Inicial</th>
                            <th class="ph2">Data Final</th>
                            <th class="ph3">Venc. Garantia</th>
                            <th>Valor Total</th>
                            <th>Valor com Desconto</th>
                            <th class="ph4">V.T (Faturado)</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$results) {
                            echo '<tr>
                            <td colspan="10">Nenhuma OS Cadastrada</td>
                            </tr>';
                        }

                        $this->load->model('os_model');
                        foreach ($results as $r) {
                            $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                            if ($r->dataFinal != null) {
                                $dataFinal = date(('d/m/Y'), strtotime($r->dataFinal));
                            } else {
                                $dataFinal = "";
                            }

                            switch ($r->status) {
                                case 'A Sair | Aguard Conclusão':
                                    $cor = '#00cd00';
                                    break;
                                case 'Em Andamento':
                                    $cor = '#436eee';
                                    break;
                                case 'Negociação':
                                    $cor = '#ffd700 ';
                                    break;
                                case 'Orçamento':
                                    $cor = '#CDB380';
                                    break;
                                case 'Manutenção Preventiva':
                                    $cor = '#AEB404';
                                    break;
                                case 'Cancelado':
                                    $cor = '#CD0000';
                                    break;
                                case 'Finalizado':
                                    $cor = '#256';
                                    break;
                                case 'Faturado':
                                    $cor = '#B266FF';
                                    break;
                                case 'Aguardando Peças':
                                    $cor = '#FF7F00';
                                    break;
                                case 'Aprovado':
                                    $cor = '#808080';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                                    break;
                            }
                            $vencGarantia = '';

                            if ($r->garantia && is_numeric($r->garantia)) {
                                $vencGarantia = dateInterval($r->dataFinal, $r->garantia);
                            }
                            $corGarantia = '';
                            if (!empty($vencGarantia)) {
                                $dataGarantia = explode('/', $vencGarantia);
                                $dataGarantiaFormatada = $dataGarantia[2] . '-' . $dataGarantia[1] . '-' . $dataGarantia[0];
                                if (strtotime($dataGarantiaFormatada) >= strtotime(date('d-m-Y'))) {
                                    $corGarantia = '#4d9c79';
                                } else {
                                    $corGarantia = '#f24c6f';
                                }
                            } elseif ($r->garantia == "0") {
                                $vencGarantia = 'Sem Garantia';
                                $corGarantia = '';
                            } else {
                                $vencGarantia = '';
                                $corGarantia = '';
                            }

                            echo '<tr>';
                            echo '<td>' . $r->idOs . '</td>';
                            echo '<td class="cli1"><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->idClientes . '" style="margin-right: 1%">' . $r->nomeCliente . '</a></td>';
                            echo '<td class="ph1">' . (!empty($r->tecnico_responsavel) ? $r->tecnico_responsavel : $r->nome) . '</td>';
                            echo '<td>' . $dataInicial . '</td>';
                            echo '<td class="ph2">' . $dataFinal . '</td>';
                            echo '<td class="ph3"><span class="badge" style="background-color: ' . $corGarantia . '; border-color: ' . $corGarantia . '">' . $vencGarantia . '</span> </td>';
                            echo '<td>R$ ' . number_format($r->totalProdutos + $r->totalServicos, 2, ',', '.') . '</td>';
                            echo '<td>R$ ' . number_format(floatval($r->valor_desconto), 2, ',', '.') . '</td>';
                            echo '<td class="ph4">R$ ' . number_format($r->valor_desconto != 0 ? $r->valor_desconto : $r->valorTotal, 2, ',', '.') . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $r->status . '</span> </td>';

                            echo '<td>';
                            $editavel = $this->os_model->isEditable($r->idOs);
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/os/visualizar/' . $r->idOs . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show"></i></a>';
                            }
                            if ($editavel) {
                                if (($r->status != 'Finalizado') || $this->session->userdata('permissao') == '1') {
                                    echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/os/editar/' . $r->idOs . '" class="btn-nwe3" title="Editar OS"><i class="bx bx-edit"></i></a>';
                                }
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dOs') && $editavel) {
                                echo '<a href="#modal-excluir" role="button" data-toggle="modal" os="' . $r->idOs . '" class="btn-nwe4" title="Excluir OS"><i class="bx bx-trash-alt"></i></a>  ';
                            }
                            echo '</td>';
                            echo '</tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    /*    if ($result->status == 'Finalizado') {
                echo 'disabled';
            }  */
    ?>

    <?php echo $this->pagination->create_links(); ?>

    <!-- Modal -->
    <div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/os/excluir" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Excluir OS</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idOs" name="id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir esta OS?</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                    <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var os = $(this).attr('os');
            $('#idOs').val(os);
        });
        $(document).on('click', '#excluir-notificacao', function(event) {
            event.preventDefault();
            $.ajax({
                    url: '<?php echo site_url() ?>/os/excluir_notificacao',
                    type: 'GET',
                    dataType: 'json',
                })
                .done(function(data) {
                    if (data.result == true) {
                        Swal.fire({
                            type: "success",
                            title: "Sucesso",
                            text: "Notificação excluída com sucesso."
                        });
                        location.reload();
                    } else {
                        Swal.fire({
                            type: "success",
                            title: "Sucesso",
                            text: "Ocorreu um problema ao tentar exlcuir notificação."
                        });
                    }
                });
        });
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script>