# Documentação: Bancos.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Bancos.php`
- **Tipo:** Controller (`MY_Controller`)
- **Módulo:** `financeiro`

## 2. Propósito
CRUD auxiliar de instituições bancárias (`bancos`) utilizadas no módulo Financeiro.

## 3. Permissões e Métodos
- `__construct()`: exige permissão `vCadFinanceiro` e carrega `Bancos_model` (`model`).
- `index()` / `gerenciar()`: listagem paginada (10 por página) na view `bancos/bancos`.
- `adicionar()`: exige `aCadFinanceiro`, valida campo `nome` obrigatório.
- `editar()`: exige `eCadFinanceiro`, valida `nome` e atualiza pela PK `id`.
- `excluir()`: exige `dCadFinanceiro`, remove registro via POST `id`.
