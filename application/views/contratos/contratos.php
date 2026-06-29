<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-signature"></i>
        </span>
        <h5>Contratos</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/contratos/gerenciar">
            <div class="span2">
                <a href="<?php echo base_url(); ?>index.php/contratos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Novo Contrato</span></a>
            </div>
            
            <div class="span3">
                <input type="text" name="contrato" id="contrato" placeholder="Pesquisar Número do Contrato" class="span12" value="<?= $this->input->get('contrato') ? $this->input->get('contrato') : '' ?>">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente a pesquisar" class="span12" value="<?= $this->input->get('pesquisa') ? $this->input->get('pesquisa') : '' ?>">
            </div>
            
            <div class="span3">
                <input type="text" name="nome" id="nome" placeholder="Nome do Contrato" class="span12" value="<?= $this->input->get('nome') ? $this->input->get('nome') : '' ?>">
                <select name="status" id="" class="span12">
                    <option value="">Selecione status</option>
                    <option value="Negociação" <?= $this->input->get('status') == "Negociação" ? "selected" : ""; ?>>Negociação</option>
                    <option value="Ativo" <?= $this->input->get('status') == "Ativo" || $this->input->get('status') == "1" ? "selected" : ""; ?>>Ativo</option>
                    <option value="Inativo" <?= $this->input->get('status') == "Inativo" || $this->input->get('status') == "0" ? "selected" : ""; ?>>Inativo</option>
                </select>
            </div>
            
            <div class="span2">
                <button class="button btn btn-mini btn-warning">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    <span class="button__text2">Pesquisar</span>
                </button>
                <a href="<?php echo base_url(); ?>index.php/contratos/" class="button btn btn-mini btn-success" style="max-width: 140px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpa</span></a>
            </div>
        </form>
    </div>

    <div class="widget-box" style="margin-top: 8px">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <div class="table-responsive">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Cliente</th>
                            <th>Nome do Contrato</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (! $results) {
                            echo '<tr>
                                    <td colspan="8">Nenhum Contrato Cadastrado</td>
                                  </tr>';
                        }
                        foreach ($results as $r) {
                            $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                            $dataFinal = $r->dataFinal ? date(('d/m/Y'), strtotime($r->dataFinal)) : '';

                            switch ($r->status) {
                                case 'Ativo':
                                case '1':
                                    $cor = '#54c795';
                                    $statusStr = 'Ativo';
                                    break;
                                case 'Inativo':
                                case '0':
                                    $cor = '#225566';
                                    $statusStr = 'Inativo';
                                    break;
                                case 'Negociação':
                                    $cor = '#ffd700';
                                    $statusStr = 'Negociação';
                                    break;
                                default:
                                    $cor = '#E0E4CC';
                                    $statusStr = $r->status;
                                    break;
                            }

                            echo '<tr>';
                            echo '<td>' . $r->idContratos . '</td>';
                            echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->clientes_id . '" style="margin-right: 1%">' . $r->nomeCliente . '</a></td>';
                            echo '<td>' . $r->nomeContratos . '</td>';
                            echo '<td>' . $dataInicial . '</td>';
                            echo '<td>' . $dataFinal . '</td>';
                            echo '<td>R$ ' . number_format($r->valorTotal, 2, ',', '.') . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $cor . '; border-color: ' . $cor . '">' . $statusStr . '</span> </td>';
                            echo '<td>
                                    <a style="margin-right: 1%" href="' . base_url() . 'index.php/contratos/editar/' . $r->idContratos . '" class="btn-nwe3" title="Editar Contrato"><i class="bx bx-edit bx-xs"></i></a>
                                    <a href="#modal-excluir" role="button" data-toggle="modal" contrato="' . $r->idContratos . '" class="btn-nwe4" title="Excluir Contrato"><i class="bx bx-trash-alt bx-xs"></i></a>
                                  </td>';
                            echo '</tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/contratos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Contrato</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idContratos" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este contrato?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var contrato = $(this).attr('contrato');
            $('#idContratos').val(contrato);
        });
    });
</script>
