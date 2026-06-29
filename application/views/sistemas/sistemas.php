<style>
  /* Barra superior no mobile */
  @media (max-width: 767px) {
    .barra-topo-sistemas {
      flex-direction: column !important;
      align-items: stretch !important;
      gap: 10px !important;
    }
    .barra-topo-sistemas > div a {
      width: 100% !important;
      text-align: center !important;
    }
    .barra-topo-sistemas > form {
      display: flex !important;
      gap: 6px !important;
      width: 100% !important;
    }
    .barra-topo-sistemas > form input {
      flex: 1 !important;
      min-width: 0 !important;
    }
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-cubes"></i>
        </span>
        <h5>Sistemas</h5>
    </div>
    <div class="span12 barra-topo-sistemas" style="margin-left: 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
        <div>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                <a href="<?php echo base_url(); ?>index.php/sistemas/adicionar" class="button btn btn-mini btn-success" style="margin: 0; max-width: 180px;">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar Sistema</span>
                </a>
            <?php } ?>
        </div>
        <form method="get" action="<?php echo base_url(); ?>index.php/sistemas/gerenciar" style="display: flex; align-items: center; gap: 8px; margin: 0; flex: 1; justify-content: flex-end;">
            <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nome..." value="<?php echo $this->input->get('pesquisa'); ?>" style="margin: 0; height: 30px; width: 100%; max-width: 350px;">
            <button type="submit" class="button btn btn-mini btn-warning" style="margin: 0;">
                <span class="button__icon"><i class='bx bx-search-alt'></i></span><span class="button__text2">Pesquisar</span>
            </button>
            <?php if ($this->input->get('pesquisa')) { ?>
                <a href="<?php echo base_url(); ?>index.php/sistemas/gerenciar" class="button btn btn-mini btn-danger" style="margin: 0;" title="Limpar Filtro">
                    <span class="button__icon"><i class='bx bx-x'></i></span><span class="button__text2">Limpar</span>
                </a>
            <?php } ?>
        </form>
    </div>

    <div class="widget-box" style="clear: both; margin-top: 15px;">
        <div class="widget-content nopadding tab-content">
            <div class="table-responsive">
                <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                                <td colspan="5">Nenhum Sistema Cadastrado</td>
                              </tr>';
                    }
                    foreach ($results as $r) {
                        $situacao = $r->situacao == 1 ? 'Ativo' : 'Inativo';
                        echo '<tr>';
                        echo '<td>' . $r->idSistemas . '</td>';
                        echo '<td>' . $r->nome . '</td>';
                        echo '<td>R$ ' . number_format($r->preco, 2, ',', '.') . '</td>';
                        echo '<td>' . $situacao . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="' . base_url() . 'index.php/sistemas/editar/' . $r->idSistemas . '" class="btn-nwe3" title="Editar Sistema"><i class="bx bx-edit"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" sistema="' . $r->idSistemas . '" class="btn-nwe4" title="Excluir Sistema"><i class="bx bx-trash-alt"></i></a>';
                        }
                        echo '</td>';
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
    <form action="<?php echo base_url() ?>index.php/sistemas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Sistema</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idSistema" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este sistema e todos os checklists associados a ele?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
              <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var sistema = $(this).attr('sistema');
            $('#idSistema').val(sistema);
        });
    });
</script>
