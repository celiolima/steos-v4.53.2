<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <h5>Serviços NFS-e (Cadastros)</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aServico')) : ?>
            <div class="span3 flexxn" style="display: flex;">
                <a href="<?= base_url() ?>index.php/servicos_nfse/adicionar" class="button btn btn-mini btn-success" style="max-width: 180px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2"> Adicionar Serviço</span>
                </a>
            </div>
        <?php endif; ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/servicos_nfse" style="display: flex; justify-content: flex-end;">
            <div class="span4">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nome ou Cód. Municipal..." class="span12" value="<?=$this->input->get('pesquisa')?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span></button>
            </div>
        </form>
    </div>
    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Nome do Serviço</th>
                        <th>Cód. Serviço Municipal (LC 116)</th>
                        <th>Código NBS</th>
                        <th>Alíquota ISS (%)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!$results) {
                            echo '<tr>
                                    <td colspan="6">Nenhum Serviço NFS-e Cadastrado</td>
                                </tr>';
                        }
        foreach ($results as $r) {
            echo '<tr>';
            echo '<td>' . $r->idServicosNfse . '</td>';
            echo '<td>' . $r->nome_servico . '</td>';
            echo '<td>' . $r->codigo_servico_municipal . '</td>';
            echo '<td>' . ($r->codigo_nbs ?: '-') . '</td>';
            echo '<td>' . $r->aliquota . '%</td>';
            echo '<td>';
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) {
                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/servicos_nfse/editar/' . $r->idServicosNfse . '" class="btn-nwe3" title="Editar Serviço"><i class="bx bx-edit bx-xs"></i></a>';
            }
            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dServico')) {
                echo '<a href="#modal-excluir" role="button" data-toggle="modal" servico="' . $r->idServicosNfse . '" class="btn-nwe4" title="Excluir Serviço"><i class="bx bx-trash-alt bx-xs"></i></a>  ';
            }
            echo '</td>';
            echo '</tr>';
        } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/servicos_nfse/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Serviço NFS-e</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idServicosNfse" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este serviço NFS-e?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
          <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
          <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var servico = $(this).attr('servico');
            if (servico) {
                $('#idServicosNfse').val(servico);
            }
        });
    });
</script>
