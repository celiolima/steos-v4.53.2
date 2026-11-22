# Documentação: CentroGastos.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\CentroGastos.php`
- **Tipo:** Controller (`MY_Controller`)
- **Módulo:** `financeiro`
- **Rota Linux Case-Sensitive:** `centrogastos` -> `CentroGastos` em `application/config/routes.php`.

## 2. Métodos e Permissões
- Exige `vLancamento` no construtor; `aLancamento` (`adicionar`), `eLancamento` (`editar`), `dLancamento` (`excluir`).
- Gerencia registros na tabela `centro_gastos` via `CentroGastos_model` e views `centrogastos/*`.
