<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/table-custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/maskmoney.js"></script>
<style>
  select {
    width: 70px;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-cash-register"></i>
            </span>
            <h5>Cobranças</h5>
    </div>
    <div class="span12" style="margin-left: 0; margin-top: 10px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/cobrancas/cobrancas">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do cliente ou CPF/CNPJ" class="span12" value="<?= $this->input->get('pesquisa') ? htmlspecialchars($this->input->get('pesquisa')) : ''; ?>">
                <input type="text" name="os_id" id="os_id" placeholder="Nº da Ordem de Serviço (OS)" class="span12" style="margin-top: 5px;" value="<?= $this->input->get('os_id') ? htmlspecialchars($this->input->get('os_id')) : ''; ?>">
                <input type="text" name="vendas_id" id="vendas_id" placeholder="Nº da Venda / Fatura" class="span12" style="margin-top: 5px;" value="<?= $this->input->get('vendas_id') ? htmlspecialchars($this->input->get('vendas_id')) : ''; ?>">
            </div>

            <div class="span2">
                <select name="tipo" id="tipo" class="span12">
                    <option value="">Todos os tipos</option>
                    <option value="boleto" <?= $this->input->get('tipo') == "boleto" ? "selected" : ""; ?>>Boleto</option>
                    <option value="link" <?= $this->input->get('tipo') == "link" ? "selected" : ""; ?>>Link de Pagamento</option>
                    <option value="pix" <?= $this->input->get('tipo') == "pix" ? "selected" : ""; ?>>Pix</option>
                    <option value="cartao" <?= $this->input->get('tipo') == "cartao" ? "selected" : ""; ?>>Cartão</option>
                </select>
                <select name="status" id="status" class="span12" style="margin-top: 5px;">
                    <option value="">Todos os status</option>
                    <option value="PENDING" <?= $this->input->get('status') == "PENDING" ? "selected" : ""; ?>>Aguardando pagamento</option>
                    <option value="RECEIVED" <?= $this->input->get('status') == "RECEIVED" ? "selected" : ""; ?>>Recebida (paga)</option>
                    <option value="CONFIRMED" <?= $this->input->get('status') == "CONFIRMED" ? "selected" : ""; ?>>Confirmada</option>
                    <option value="OVERDUE" <?= $this->input->get('status') == "OVERDUE" ? "selected" : ""; ?>>Vencida</option>
                    <option value="DELETED" <?= $this->input->get('status') == "DELETED" ? "selected" : ""; ?>>Cancelada / Excluída</option>
                    <option value="REFUNDED" <?= $this->input->get('status') == "REFUNDED" ? "selected" : ""; ?>>Estornada</option>
                </select>
            </div>

            <div class="span3">
                <div class="span12" style="margin-left: 0;">
                    <input type="text" name="data_de" autocomplete="off" id="data_de" placeholder="Vencimento de" title="Vencimento Inicial" class="span6 datepicker" value="<?= $this->input->get('data_de') ? htmlspecialchars($this->input->get('data_de')) : '' ?>">
                    <input type="text" name="data_ate" autocomplete="off" id="data_ate" placeholder="Vencimento até" title="Vencimento Final" class="span6 datepicker" value="<?= $this->input->get('data_ate') ? htmlspecialchars($this->input->get('data_ate')) : '' ?>">
                </div>
                <div class="span12" style="margin-left: 0; margin-top: 5px;">
                    <input type="text" name="valor_de" autocomplete="off" id="valor_de" placeholder="Valor Mín. (R$)" class="span6 money" value="<?= $this->input->get('valor_de') ? htmlspecialchars($this->input->get('valor_de')) : '' ?>">
                    <input type="text" name="valor_ate" autocomplete="off" id="valor_ate" placeholder="Valor Máx. (R$)" class="span6 money" value="<?= $this->input->get('valor_ate') ? htmlspecialchars($this->input->get('valor_ate')) : '' ?>">
                </div>
            </div>

            <div class="span2" style="margin-top: 2px;">
                <button class="button btn btn-mini btn-warning" style="min-width: 110px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    <span class="button__text2">Pesquisar</span>
                </button>
                <a href="<?php echo base_url(); ?>index.php/cobrancas/cobrancas" class="button btn btn-mini btn-success" style="min-width: 110px; margin-top: 5px;">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Limpar</span>
                </a>
            </div>
        </form>
    </div>
    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Cliente</th>
                        <th>Gateway</th>
                        <th>Tipo</th>
                        <th>Data de Vencimento</th>
                        <th>Referência</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                                <td colspan="9">Nenhuma cobrança Cadastrada</td>
                            </tr>';
                    }
                    foreach ($results as $r) {
                        $dataVenda = date(('d/m/Y'), strtotime($r->expire_at));
                        $cobrancaStatus = getCobrancaStatusBadge(
                            $r->status,
                            $this->config->item('payment_gateways'),
                            $r->payment_gateway
                        );

                        echo '<tr>';
                        // Coluna OS (substituindo ID da cobrança)
                        if (!empty($r->os_id)) {
                            echo '<td><a href="' . base_url() . 'index.php/os/visualizar/' . $r->os_id . '">#' . $r->os_id . '</a></td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // Coluna Cliente
                        if (!empty($r->clientes_id)) {
                            echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->clientes_id . '">' . htmlspecialchars($r->cliente_nome ?? '-', ENT_QUOTES, 'UTF-8') . '</a></td>';
                        } else {
                            echo '<td>' . htmlspecialchars($r->cliente_nome ?? '-', ENT_QUOTES, 'UTF-8') . '</td>';
                        }
                        echo '<td>' . $r->payment_gateway . '</td>';
                        echo '<td>' . $r->payment_method . '</td>';
                        echo '<td>' . $dataVenda . '</td>';

                        // Coluna Referência sempre com 1 único <td>
                        echo '<td>';
                        if ($r->os_id != '') {
                            echo '<a href="' . base_url() . 'index.php/os/visualizar/' . $r->os_id . '"> Ordem de Serviço: #' . $r->os_id . '</a> ';
                        }
                        if ($r->vendas_id != '') {
                            echo '<a href="' . base_url() . 'index.php/vendas/visualizar/' . $r->vendas_id . '"> Venda: #' . $r->vendas_id . '</a> ';
                        }
                        if (empty($r->os_id) && empty($r->vendas_id)) {
                            echo 'Cobrança Avulsa';
                        }
                        echo '</td>';

                        echo '<td>' .  $cobrancaStatus . '</td>';
                        echo '<td>R$ ' . number_format($r->total / 100, 2, ',', '.') . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCobranca')) {
                            echo '<a style="margin-right: 1%" href="#modal-cancelar" role="button" data-toggle="modal" cancela_id="' . $r->idCobranca . '" class="btn-nwe4" title="Cancelar Cobrança"><i class="bx bx-x" ></i></a>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/cobrancas/atualizar/' . $r->idCobranca . '" class="btn-nwe" title="Atualizar Cobrança"><i class="bx bx-refresh"></i></a>';
                            echo '<a style="margin-right: 1%" href="#modal-confirmar" role="button" data-toggle="modal" confirma_id="' . $r->idCobranca . '" class="btn-nwe3" title="Confirmar pagamento"><i class="bx bx-check"></i></a>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/cobrancas/visualizar/' . $r->idCobranca . '" class="btn-nwe2" title="Ver mais detalhes"><i class="bx bx-show" ></i></a>';
                            echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/cobrancas/enviarEmail/' . $r->idCobranca . '" class="btn-nwe5" title="Enviar por E-mail"><i class="bx bx-envelope" ></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCobranca') && $r->barcode != '') {
                            echo '<a style="margin-right: 1%" href="' . $r->link . '" target="_blank" class="btn-nwe" title="Visualizar boleto"><i class="bx bx-barcode" ></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCobranca')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" excluir_id="' . $r->idCobranca . '" class="btn-nwe4" title="Excluir Cobrança"><i class="bx bx-trash-alt"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links(); ?>

    <!-- Modal -->
    <div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/excluir" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Excluir cobrança</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="excluir_id" name="excluir_id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir esta cobrança? A cobrança será cancelada.</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
            </div>
        </form>
    </div>

    <div id="modal-confirmar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/confirmarpagamento" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Confirmar pagamento</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="confirma_id" name="confirma_id" value="" />
                <h5 style="text-align: center">Deseja realmente confirmar pagamento desta cobrança?</h5>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button class="btn btn-success">Confirmar</button>
            </div>
        </form>
    </div>

    <div id="modal-cancelar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/cobrancas/cancelar" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Cancelar cobrança</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancela_id" name="cancela_id" value="" />
                <h5 style="text-align: center">Deseja realmente Cancelar esta cobrança?</h5>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                <button class="btn btn-danger">Confirmar</button>
            </div>
        </form>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('excluir_id');
                $('#excluir_id').val(cobranca);
            });

            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('confirma_id');
                $('#confirma_id').val(cobranca);
            });

            $(document).on('click', 'a', function(event) {
                var cobranca = $(this).attr('cancela_id');
                $('#cancela_id').val(cobranca);
            });

            $(".datepicker").datepicker({
                dateFormat: 'dd/mm/yy'
            });
            $(".money").maskMoney({
                prefix: '',
                allowNegative: false,
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        });
    </script>