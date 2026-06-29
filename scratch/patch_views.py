import re

def patch_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # --- HTML REPLACEMENTS ---
    
    # 1. Os (Adicionar)
    html_old_os_add = """                                         <div class="span6" style="margin-right: 0">
                                             <label for="cliente">Cliente<span class="required">*</span></label>
                                             <input id="cliente" class="span12" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="cliente" value="" />
                                             <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                             <!--  <a class="btn btn-primary span2" style="padding: 0;margin-left: 0" href="#" role="button">Link</a> -->
                                         </div>"""
    
    html_new_os_add = """                                         <div class="span3" style="margin-right: 0">
                                             <label for="cliente">Cliente<span class="required">*</span></label>
                                             <input id="cliente" class="span12" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="cliente" value="" />
                                             <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                         </div>
                                         <div class="span3">
                                             <label for="contratos_id">Contrato Vinculado</label>
                                             <select id="contratos_id" name="contratos_id" class="span12">
                                                 <option value="">Nenhum</option>
                                             </select>
                                         </div>"""

    # 2. Vendas (Adicionar)
    html_old_vendas_add = """                                        <div class="span6" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                        </div>"""

    html_new_vendas_add = """                                        <div class="span3" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                        </div>
                                        <div class="span3">
                                             <label for="contratos_id">Contrato</label>
                                             <select id="contratos_id" name="contratos_id" class="span12">
                                                 <option value="">Nenhum</option>
                                             </select>
                                         </div>"""

    # 3. Os (Editar) 
    # Linha 117
    html_old_os_edit = """                                        <div class="span6" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                            <input id="valor_desconto" class="span12" type="hidden" name="valor_desconto" value="" />
                                            <input id="valor_troco" class="span12" type="hidden" name="valor_troco" value="" />
                                        </div>"""
                                        
    html_new_os_edit = """                                        <div class="span3" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                            <input id="valor_desconto" class="span12" type="hidden" name="valor_desconto" value="" />
                                            <input id="valor_troco" class="span12" type="hidden" name="valor_troco" value="" />
                                        </div>
                                        <div class="span3">
                                             <label for="contratos_id">Contrato Vinculado</label>
                                             <select id="contratos_id" name="contratos_id" class="span12">
                                                 <option value="">Nenhum</option>
                                                 <?php if(isset($result->contratos_id) && $result->contratos_id != null) { ?>
                                                    <option value="<?php echo $result->contratos_id; ?>" selected>Manter Atual</option>
                                                 <?php } ?>
                                             </select>
                                         </div>"""

    # 4. Vendas (Editar)
    html_old_vendas_edit = """                                        <div class="span6" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                            <input id="valor_desconto" class="span12" type="hidden" name="valor_desconto" value="" />
                                            <input id="valor_troco" class="span12" type="hidden" name="valor_troco" value="" />
                                        </div>"""

    html_new_vendas_edit = """                                        <div class="span3" style="margin-left: 0">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="<?php echo $result->nomeCliente ?>" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="<?php echo $result->clientes_id ?>" />
                                            <input id="valor_desconto" class="span12" type="hidden" name="valor_desconto" value="" />
                                            <input id="valor_troco" class="span12" type="hidden" name="valor_troco" value="" />
                                        </div>
                                        <div class="span3">
                                             <label for="contratos_id">Contrato</label>
                                             <select id="contratos_id" name="contratos_id" class="span12">
                                                 <option value="">Nenhum</option>
                                                 <?php if(isset($result->contratos_id) && $result->contratos_id != null) { ?>
                                                    <option value="<?php echo $result->contratos_id; ?>" selected>Manter Atual</option>
                                                 <?php } ?>
                                             </select>
                                         </div>"""

    if html_old_os_add in content: content = content.replace(html_old_os_add, html_new_os_add)
    if html_old_vendas_add in content: content = content.replace(html_old_vendas_add, html_new_vendas_add)
    if html_old_os_edit in content: content = content.replace(html_old_os_edit, html_new_os_edit)
    if html_old_vendas_edit in content: content = content.replace(html_old_vendas_edit, html_new_vendas_edit)
        
    # --- JS REPLACEMENTS ---
    js_addition = r"""
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/os/autoCompleteCliente",
            minLength: 1,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
                // Buscar contratos atrelados a este cliente
                $.getJSON("<?php echo base_url(); ?>index.php/contratos/get_contratos_por_cliente", {clientes_id: ui.item.id}, function(data) {
                    var options = '<option value="">Nenhum Contrato</option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].id + '">' + data[i].nome + '</option>';
                    }
                    $("#contratos_id").html(options);
                });
            }
        });
    """
    
    js_addition_vendas = r"""
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/vendas/autoCompleteCliente",
            minLength: 1,
            select: function(event, ui) {
                $("#clientes_id").val(ui.item.id);
                // Buscar contratos atrelados a este cliente
                $.getJSON("<?php echo base_url(); ?>index.php/contratos/get_contratos_por_cliente", {clientes_id: ui.item.id}, function(data) {
                    var options = '<option value="">Nenhum Contrato</option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].id + '">' + data[i].nome + '</option>';
                    }
                    $("#contratos_id").html(options);
                });
            }
        });
    """
    
    # We will do a generic replacement for the autocomplete to add the inner logic
    # Find the current autocomplete for #cliente
    pattern = r'\$\("\#cliente"\)\.autocomplete\(\{.*?(?:source:.*?(?:os|vendas)\/autoCompleteCliente.*?\}).*?\}\);'
    
    if "os/autoCompleteCliente" in content:
        content = re.sub(pattern, js_addition.strip(), content, flags=re.DOTALL)
    elif "vendas/autoCompleteCliente" in content:
        content = re.sub(pattern, js_addition_vendas.strip(), content, flags=re.DOTALL)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

patch_file('application/views/os/adicionarOs.php')
patch_file('application/views/vendas/adicionarVenda.php')
patch_file('application/views/os/editarOs.php')
patch_file('application/views/vendas/editarVenda.php')
