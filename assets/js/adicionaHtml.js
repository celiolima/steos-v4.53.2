function addEquipamentos(i) {
    var str = '';
    //str += '<input id="' + i + 'idEquipamentos_os" class="span12" name="equipamentos[' + i + '][idEquipamentos_os]" type="hidden" value="' + $equipamento -> idEquipamentos_os + '" />';
    str += '<div id="' + i + 'addEquipamentos" name="addequipamentos[' + i + ']" >';
    str += '<div class="span12" style="padding:1%; border: 2px solid #ECF0F1;border-radius: 5px; ">';
    /*  str += '<div id="' + i + 'busca">';
     str += '<input id="equipamentos" class="span12 pull-right" autocomplete="off" style="padding: 1; margin-right: 0" type="text" name="equipamentos" placeholder="Pesquise por Equipamento, Série ou Modelo " value="" />';
     str += ' </div>'; */
    str += '<table class="table table-bordered">';
    str += '<thead>';
    str += '<tr>';
    str += '<td>';
    str += '<strong id="' + i + 'equipamentos_label">';

    str += '<td colspan="7" style="text-align: left; "><a id="' + i + 'equipamentos" name="equipamentos[' + i + '][equipamentos]"></a></td>';
    str += '<input id="' + i + 'equipamentos_value" class="span12" type="hidden" name="equipamentos[' + i + '][equipamentos_value]" value="" />';

    str += '<input id="' + i + 'equipamento_id" class="span12" name="equipamentos[' + i + '][equipamento_id]" type="hidden" value="" />';

    str += ' </strong>';
    str += ' </td>';
    str += '<td>';
    str += '</td>';
    str += ' </tr>';
    str += ' </thead>';
    str += '<tbody>';
    str += '<tr>';

    str += '<td colspan="7" style="text-align: left; ">Numero de Serie:<a id="' + i + 'serie" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'serie_value" class="span12" type="hidden" name="equipamentos[' + i + '][serie_value]" value="" />';

    str += '<td colspan="7" style="text-align: left; ">Modelo:<a id="' + i + 'modelo" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'modelo_value" class="span12" type="hidden" name="equipamentos[' + i + '][modelo_value]" value="" />';
    str += ' </tr>';
    str += '<tr>';
    str += '<td colspan="7" style="text-align: left; ">Cor:<a id="' + i + 'cor" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'cor_value" class="span12" type="hidden" name="equipamentos[' + i + '][cor_value]" value="" />';

    str += '<td colspan="7" style="text-align: left; ">Descricao:<a id="' + i + 'descricao" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'descricao_value" class="span12" type="hidden" name="equipamentos[' + i + '][descricao_value]" value="" />';
    str += ' </tr>';
    str += '<tr>';
    str += '<td colspan="7" style="text-align: left; ">Potência:<a id="' + i + 'potencia" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'potencia_value" class="span12" type="hidden" name="equipamentos[' + i + '][potencia_value]" value="" />';

    str += '<td colspan="7" style="text-align: left; ">Voltagem:<a id="' + i + 'voltagem" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'voltagem_value" class="span12" type="hidden" name="equipamentos[' + i + '][voltagem_value]" value="" />';
    str += ' </tr>';
    str += '<tr>';
    str += '<td colspan="7" style="text-align: left; ">Marcas:<a id="' + i + 'marcas" style="color: green">&nbsp</a></td>';
    str += '<input id="' + i + 'marcas_value" class="span12" type="hidden" name="equipamentos[' + i + '][marcas_value]" value="" />';
    str += '<td colspan="7" style="text-align: left; ">Local:&nbsp<a id="' + i + 'local" style="color: green"><input id="' + i + 'local_value"  name="equipamentos[' + i + '][local_value]" value="" style="width:90%; border: 1px solid #D2D4DE; border-radius: 5px; "/></a></td>';
    //str += '<input id="' + i + 'local_value" class="span12" name="equipamentos[' + i + '][local_value]" value="" />';
    str += ' </tr>';
    str += '<tr>';
    str += '<td colspan="7" style="text-align: left; ">';
    str += '<label>Defeito Reclamado</label>';
    str += '<textarea rows="5" cols="20" name="equipamentos[' + i + '][defeito_relatado]" id="' + i + 'defeito_relatado" placeholder="insira o defeito reclamado" style="width:100%"></textarea>';
    str += ' </td>';
    str += '<td colspan="7" style="text-align: left; ">';
    str += '<label> Defeito Encontrado</label>';
    str += '<textarea name="equipamentos[' + i + '][defeito_encontrado]" id="' + i + 'defeito_encontardo" placeholder="insira o defeito encontrado" cols="20" rows="5" style="width:100%"></textarea>';
    str += ' </td>';
    str += ' </tr>';
    str += ' </tbody>';
    str += ' </table>';
    str += '<button type="button" name="remove" id=' + i + ' class="btn btn-danger btn_remove">X</button>';
    str += ' </div>';
    str += '</div>';
    i++;
    $('#dynamic_field').append(str);
}
function addEquipamentosAutocomplete(n, i) {
    $("#" + n + "equipamentos_label").append(i.equipamento);
    $("#" + n + "equipamentos_value").val(i.equipamento);
    $("#" + n + "equipamento_id").val(i.id);

    $("#" + n + "serie").append(i.num_serie);
    $("#" + n + "serie_value").val(i.num_serie);

    $("#" + n + "modelo").append(i.modelo);
    $("#" + n + "modelo_value").val(i.modelo);
    $("#" + n + "cor").append(i.cor);
    $("#" + n + "cor_value").val(i.cor);
    $("#" + n + "descricao").append(i.descricao);
    $("#" + n + "descricao_value").val(i.descricao);
    $("#" + n + "potencia").append(i.potencia);
    $("#" + n + "potencia_value").val(i.potencia);
    $("#" + n + "voltagem").append(i.voltagem);
    $("#" + n + "voltagem_value").val(i.voltagem);
    $("#" + n + "marcas").append(i.marcas);
    $("#" + n + "marcas_value").val(i.marcas);

    //$("#busca").html("");

    //console.log(i);

}
