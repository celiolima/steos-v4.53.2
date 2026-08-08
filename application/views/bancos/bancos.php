<div class="new122">
    <div class="widget-title">
        <span class="icon">
            <i class="fas fa-list"></i>
        </span>
        <h5>Bancos</h5>
    </div>
    <div class="span12" style="margin-left: 0;margin-top: 1rem;">
        <a href="<?php echo base_url(); ?>index.php/bancos/adicionar" class="button btn btn-success" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></a>
    </div>

<div class="widget-box">
    <div class="widget-content nopadding tab-content">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>#</th>
<th>Nome do Banco</th>

                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$results) {
                    echo '<tr><td colspan="3">Nenhum registro encontrado.</td></tr>';
                }
                foreach ($results as $r) {
                    echo '<tr>';
                    echo '<td>' . $r->id . '</td>';
echo '<td>' . $r->nome . '</td>';

                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                        echo '<a href="' . base_url() . 'index.php/bancos/editar/' . $r->id . '" style="margin-right: 1%" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" banco="' . $r->id . '" class="btn-nwe4" title="Excluir"><i class="bx bx-trash-alt"></i></a>';
                    }
                    echo '</td>';
                    echo '</tr>';
                } ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>
</div>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/bancos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Bancos</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este registro?</h5>
        </div>
        <div class="modal-footer">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var banco = $(this).attr('banco');
            $('#id').val(banco);
        });
    });
</script>
