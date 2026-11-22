# Documentação: OsController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\OsController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Expõe operações completas para Ordens de Serviço (`/api/v1/os`), itens (produtos/serviços), anotações, anexos, descontos e checklists digitais.

## 3. Endpoints e Funcionalidades
- `GET /api/v1/os` (`index_get`): filtros `search`, `status`, `from`, `to`, `data_fech_de`, `data_fech_ate`, `tecnico`, `local`, `tipo`, `manPrevnt`, com paginação via `perPage` e `page` (`$start = $page * $perPage`).
- `GET /api/v1/os/{id}`: retorna `HTTP 404` se inexistente; carrega `produtos`, `servicos`, `anexos`, `anotacoes`, `tecnicos` (`Tecnicos_os_model`), `equipamentos` (`Equipamentos_os_model`), `cobrancas`, `checklists` (`os_checklists`), `calcTotal` e `textoWhatsApp`.
- `POST /api/v1/os` e `PUT /api/v1/os/{id}`: compatíveis com campos do STEOS (`dataAbertura`, `contratos_id`, `defeito_encontrado`, `local`, `tipo`, `manutPreventiva`, `prioridade`, `classificacao`), preservando `faturado` no `PUT`.
- `DELETE /api/v1/os/{id}`: exige permissão `dOs`, estorna estoque e exclui lançamentos associados.
- `GET /api/v1/os/{id}/checklists` (`checklists_get`): lista checklists e seus itens (`os_checklists_itens`). Exige `vOs`.
- Sub-recursos mutáveis (`POST /desconto`, `POST/PUT/DELETE /produtos`, `POST/PUT/DELETE /servicos`, `POST/DELETE /anotacoes`, `POST/DELETE /anexos`): protegidos centralizadamente por `requireEditOsPermission()` exigindo permissão `eOs`.
