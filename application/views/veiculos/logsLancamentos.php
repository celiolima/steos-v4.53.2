<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-list-ul"></i>
                </span>
                <h5>Logs de Lançamentos de Veículos</h5>
                <div class="buttons">
                    <a href="<?php echo base_url(); ?>index.php/veiculos" class="button btn btn-mini btn-warning">
                        <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                    </a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Veículo</th>
                            <th>Tipo</th>
                            <th>Conteúdo</th>
                            <th>Usuário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$logs) {
                            echo '<tr><td colspan="6">Nenhum log encontrado.</td></tr>';
                        }
                        foreach ($logs as $r) {
                            echo '<tr>';
                            echo '<td>' . $r->id . '</td>';
                            echo '<td>' . date('d/m/Y H:i', strtotime($r->data_hora)) . '</td>';
                            echo '<td>' . $r->nomeVeiculo . '</td>';
                            echo '<td>' . $r->tipo . '</td>';
                            echo '<td>' . $r->conteudo . '</td>';
                            echo '<td>' . $r->nomeUsuario . '</td>';
                            echo '</tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php echo $this->pagination->create_links(); ?>
    </div>
</div>
