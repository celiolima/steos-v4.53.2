# Documentação: routes.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\config\routes.php`
- **Tipo:** Config
- **Módulo:** `configuracoes`

## 2. Estrutura de Roteamento Configurada
- `$route['default_controller'] = 'steos';`
- **Mapeamento explícito em lowercase para Controllers CamelCase (blindagem Linux case-sensitive):**
  - `classificacaofinanceira` -> `ClassificacaoFinanceira`
  - `grupofinanceiro` -> `GrupoFinanceiro`
  - `centrogastos` -> `CentroGastos`
  - `formapagamento` -> `FormaPagamento`
- **Carregamento condicional da API REST v1:**
  - Quando `$_ENV['API_ENABLED']` for `true`, carrega `APPPATH . 'config/routes_api.php'`.
