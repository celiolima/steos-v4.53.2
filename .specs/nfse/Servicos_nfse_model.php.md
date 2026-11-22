# Documentação: Servicos_nfse_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Servicos_nfse_model.php`
- **Tipo:** Model (`CI_Model`)
- **Tabela:** `servicos_nfse` (`idServicosNfse`, `nome_servico`, `codigo_servico_municipal`, `asaas_service_id`, `codigo_nbs`, `codigo_tributacao_nacional`, `aliquota`, `cst_ibs_cbs`, `class_trib_ibs_cbs`, `ind_operacao`)

## 2. Métodos
- `get($table, $fields, $where, $perpage, $start, $one, $array)`: aplica busca com `group_start()` em `nome_servico`, `codigo_servico_municipal` e `codigo_nbs`; ordena por `idServicosNfse DESC`.
- `getById($id)`, `add($table, $data)`, `edit($table, $data, $fieldID, $ID)`, `delete($table, $fieldID, $ID)`, `count($table)`.
