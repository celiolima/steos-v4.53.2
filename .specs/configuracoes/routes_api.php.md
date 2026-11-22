# Documentação: routes_api.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\config\routes_api.php`
- **Tipo:** Config
- **Módulo:** `configuracoes` / `api`

## 2. Rotas Mapeadas (`/api/v1`)
- **Core:** `api/v1`, `api/v1/audit`, `api/v1/emitente`, `api/v1/calendario` -> `ApiController`
- **Autenticação & Usuários:** `api/v1/login`, `api/v1/reGenToken`, `api/v1/conta`, `api/v1/usuarios(/(:num))` -> `UsuariosController`
- **Cadastros Base:** `api/v1/clientes(/(:num))`, `api/v1/produtos(/(:num))`, `api/v1/servicos(/(:num))`
- **Ordens de Serviço:** `api/v1/os(/(:num))`, `/produtos`, `/servicos`, `/anotacoes`, `/anexos`, `/desconto`, `/checklists` -> `OsController`
- **Contratos:** `api/v1/contratos`, `api/v1/contratos/(:num)` -> `ContratosController`
- **Cobranças:** `api/v1/cobrancas`, `api/v1/cobrancas/(:num)`, `api/v1/cobrancas/(:num)/pix` -> `CobrancasController`
- **NFS-e Asaas:** `api/v1/nfse`, `api/v1/nfse/servicos`, `api/v1/nfse/emitir/(:num)`, `api/v1/nfse/consultar/(:num)`, `api/v1/nfse/cancelar/(:num)`, `api/v1/nfse/(:num)` -> `NfseController`
- **Financeiro, Frota & Vendas:** `api/v1/financeiro(/resumo|/(:num))`, `api/v1/veiculos(/(:num))`, `api/v1/vendas(/(:num))`
- **Portal do Cliente:** `api/v1/client`, `api/v1/client/auth`, `api/v1/client/os(/(:num))`, `api/v1/client/compras(/(:num))`, `api/v1/client/cobrancas`, `api/v1/client/contratos(/(:num))`
