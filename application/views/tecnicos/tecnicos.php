<style>
  select {
    width: 70px;
  }
</style>

<div class="new122">
  <div class="widget-title" style="margin:-15px -10px 0">
    <h5>Técnicos</h5>
  </div>
  <a href="<?php echo base_url() ?>index.php/tecnicos/adicionar" class="button btn btn-success" style="max-width: 160px">
    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar Técnicos</span></a>

  <div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
      <span class="icon">
        <i class="fas fa-cash-register"></i>
      </span>
      <h5 style="padding: 3px 0"></h5>
    </div>
    <div class="widget-content nopadding tab-content">
      <table id="tabela" class="table table-bordered ">
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Comissão de Seviço</th>
            <th>Comissão de Produto</th>
            <th>Ativo</th>

            <th>Validade</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (!$results) {
            echo '<tr>
                    <td colspan="5">Nenhum Técnico Cadastrado</td>
                </tr>';
          }
          foreach ($results as $r) {
            echo '<tr>';
            echo '<td>' . $r->idTecnicos . '</td>';
            echo '<td>' . $r->nome . '</td>';
            echo '<td>' . $r->comissao_servico . '</td>';
            echo '<td>' . $r->comissao_produto . '</td>';
            if ($r->ativo == '1') {
              echo '<td>SIM</td>';
            } else {
              echo '<td>NÃO</td>';
            }
            echo '<td>' . $r->dataExpiracao . '</td>';
            echo '<td>
                  <a href="' . base_url() . 'index.php/tecnicos/editar/' . $r->idTecnicos . '" class="btn-nwe3" title="Editar OS"><i class="bx bx-edit"></i></a>
                  </td>';
            echo '</tr>';
          } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php //echo $this->pagination->create_links(); 
?>