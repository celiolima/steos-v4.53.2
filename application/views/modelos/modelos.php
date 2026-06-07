<style>
    select {
        width: 70px;
    }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-book"></i>
        </span>
        <h5>Modelos</h5>
    </div>
    <a href="<?php echo base_url(); ?>index.php/modelos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Modelo</span></a>

    <?php /* if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aModelo')) { ?>
        <a href="<?php echo base_url(); ?>index.php/modelos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Termo Modelo</span></a>
    <?php }*/ ?>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Ref. Modelo</th>
                        <th>Termo de Modelo</th>
                        <th>Usuario</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <?php
                /*  echo "<pre>";
                print_r($results);
                die(); */
                ?>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                                <td colspan="6">Nenhum Termo de Modelo Cadastrada</td>
                                </tr>';
                    }
                    foreach ($results as $r) {
                        $dataModelo = date(('d/m/Y'), strtotime($r->dataModelo));
                        $textoModeloShort = mb_strimwidth(strip_tags($r->textoModelo), 0, 50, "...");

                        echo '<tr>';
                        echo '<td>' . $r->idModelos . '</td>';
                        echo '<td>' . $dataModelo . '</td>';
                        echo '<td>' . $r->refModelo . '</td>';
                        echo '<td>' . $textoModeloShort . '</td>';
                        echo '<td><a href="' . base_url() . 'index.php/usuarios/editar/' . $r->idUsuarios . '">' . $r->nome . '</a></td>';
                        echo '<td>';

                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/visualizar/' . $r->idModelos . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/imprimir/' . $r->idModelos . '" target="_blank" class="btn-nwe6" title="Imprimir"><i class="bx bx-printer bx-xs"></i></a>';


                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/editar/' . $r->idModelos . '" class="btn-nwe3" title="Editar"><i class="bx bx-edit bx-xs"></i></a>';


                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" modelo="' . $r->idModelos . '" class="btn-nwe4" title="Excluir"><i class="bx bx-trash-alt bx-xs"></a>';

                        /*    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vModelo')) {
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/visualizar/' . $r->idModelos . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/imprimir/' . $r->idModelos . '" target="_blank" class="btn-nwe6" title="Imprimir"><i class="bx bx-printer bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eModelo')) {
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/modelos/editar/' . $r->idModelos . '" class="btn-nwe3" title="Editar"><i class="bx bx-edit bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dModelo')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" modelo="' . $r->idModelos . '" class="btn-nwe4" title="Excluir"><i class="bx bx-trash-alt bx-xs"></a>';
                        } */
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
    <form action="<?php echo base_url() ?>index.php/modelos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Termo de Modelo</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idModelos" name="idModelos" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este termo de modelo?</h5>
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
            var modelo = $(this).attr('modelo');
            $('#idModelos').val(modelo);
        });
    });
</script>