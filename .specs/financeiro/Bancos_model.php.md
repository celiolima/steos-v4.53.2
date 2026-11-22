# Documentação: Bancos_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Bancos_model.php`
- **Tipo:** Model (`CI_Model`)
- **Tabela:** `bancos` (`id`, `nome`)

## 2. Métodos
- `get($table, $fields, $where, $perpage, $start, $one, $array)`: aplica `LIMIT` apenas quando `$perpage > 0`, ordenado por `id DESC`.
- `getById($id)`, `add($table, $data)`, `edit($table, $data, $fieldID, $ID)`, `delete($table, $fieldID, $ID)`, `count($table)`, `getAll($table)`.
