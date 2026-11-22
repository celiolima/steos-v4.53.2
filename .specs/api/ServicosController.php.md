# Documentação: ServicosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\ServicosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
CRUD RESTful do catálogo de serviços (`servicos`) em `/api/v1/servicos` e `/api/v1/servicos/(:num)` com permissões `vServico`, `aServico`, `eServico` e `dServico`.

## 3. Observações Técnicas
- Termo de busca `search` sanitizado com `escape_like_str()` para `Servicos_model::get()`.
- Retorna `HTTP 404` quando o ID não é encontrado.
