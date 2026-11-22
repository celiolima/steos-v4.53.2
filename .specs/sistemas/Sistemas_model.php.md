# Documentação: Sistemas_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Sistemas_model.php`
- **Tipo:** Model (`CI_Model`)
- **Tabelas:** `sistemas`, `sistemas_checks`

## 2. Métodos
- `get($table, $fields, $where, $perpage, $start, $one, $array)`: consulta ordenada por `idSistemas DESC` com filtro `nome LIKE`.
- `getById($id)`: retorna sistema por `idSistemas`.
- `getChecksBySistema($id)`: retorna verificações padrão (`sistemas_checks`) atreladas ao sistema.
- `add`, `edit`, `delete`, `count` e `autoCompleteSistema($q)`.
