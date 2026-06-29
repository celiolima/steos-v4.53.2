import re

files = [
    'application/views/os/editarOs.php',
    'application/views/vendas/editarVenda.php'
]

patch = r'''
        // Preencher dropdown de contratos no load da pagina
        var curr_cliente_id = $("#clientes_id").val();
        var curr_contrato_id = "<?php echo isset($result->contratos_id) ? $result->contratos_id : ''; ?>";
        if (curr_cliente_id) {
            $.getJSON("<?php echo base_url(); ?>index.php/contratos/get_contratos_por_cliente", {clientes_id: curr_cliente_id}, function(data) {
                var options = '<option value="">Nenhum</option>';
                for (var i = 0; i < data.length; i++) {
                    var selected = (data[i].id == curr_contrato_id) ? ' selected' : '';
                    options += '<option value="' + data[i].id + '"' + selected + '>' + data[i].nome + '</option>';
                }
                $("#contratos_id").html(options);
            });
        }
'''

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
        
    if 'Preencher dropdown de contratos' not in content:
        # Insert right after $(document).ready(function() {
        content = re.sub(r'\$\(document\)\.ready\(function\s*\(\)\s*\{', '$(document).ready(function() {' + patch, content, count=1)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print('Patched ' + file)
    else:
        print('Already patched ' + file)
