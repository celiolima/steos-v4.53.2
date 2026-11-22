# API RESTful v1 — STEOS v4.53.2

A API RESTful do STEOS opera sobre `REST_Controller` (CodeIgniter 3) com autenticação stateless via **JWT (`Authorization: Bearer <token>`)**, ativada através da variável de ambiente `API_ENABLED=true` (carregada em `application/config/routes.php` -> `application/config/routes_api.php`).

---

## 1. Autenticação e Segurança
- **Painel Administrativo / Staff:** `POST /api/v1/login` (`UsuariosController::login_post`), validando hash `password_verify()` em `usuarios.senha` e data de expiração. Renovação em `GET /api/v1/reGenToken`.
- **Portal do Cliente (App / Conecte):** `POST /api/v1/client/auth` (`ClientLoginController::login_post`), validando `clientes.senha` via `password_verify()`.
- **Proteções Aplicadas:**
  - `UsuariosController`: senhas gravadas exclusivamente com `password_hash(..., PASSWORD_DEFAULT)` no `POST` e no `PUT`, e campo `senha` removido de todos os JSONs retornados (`unset`).
  - `ClientesController`, `ProdutosController` e `ServicosController`: sanitização de termos de busca com `$this->db->escape_like_str()`, evitando SQL Injection e corrigindo o filtro por `search`.
  - `ClientOsController`, `ClientComprasController`, `ClientCobrancasController` e `ClientContratosController`: validação estrita de propriedade (`clientes_id === $clientLogged->usuario->idClientes`) retornando `HTTP 403` contra vulnerabilidades IDOR.

---

## 2. Mapa de Endpoints (`/api/v1`)

| Grupo | Endpoint | Métodos | Controller | Permissão ACL |
| :--- | :--- | :--- | :--- | :--- |
| **Core** | `/api/v1`, `/api/v1/emitente`, `/api/v1/calendario`, `/api/v1/audit` | `GET` | `ApiController` | Logado / `vOs` / `cAuditoria` |
| **Auth / Usuários** | `/api/v1/login`, `/api/v1/reGenToken`, `/api/v1/conta`, `/api/v1/usuarios(/id)` | `GET`, `POST`, `PUT`, `DELETE` | `UsuariosController` | `cUsuario` |
| **Clientes** | `/api/v1/clientes(/id)` | `GET`, `POST`, `PUT`, `DELETE` | `ClientesController` | `v/a/e/dCliente` |
| **Produtos** | `/api/v1/produtos(/id)` | `GET`, `POST`, `PUT`, `DELETE` | `ProdutosController` | `v/a/e/dProduto` |
| **Serviços** | `/api/v1/servicos(/id)` | `GET`, `POST`, `PUT`, `DELETE` | `ServicosController` | `v/a/e/dServico` |
| **O.S.** | `/api/v1/os(/id)`, `/produtos`, `/servicos`, `/anotacoes`, `/anexos`, `/desconto`, `/checklists` | `GET`, `POST`, `PUT`, `DELETE` | `OsController` | `v/a/e/dOs` (sub-recursos exigem `eOs`) |
| **Contratos** | `/api/v1/contratos(/id)` | `GET` | `ContratosController` | `vContrato` |
| **Cobranças** | `/api/v1/cobrancas(/id)`, `/api/v1/cobrancas/{id}/pix` | `GET` | `CobrancasController` | `vCobranca` |
| **NFS-e (Asaas)** | `/api/v1/nfse(/id)`, `/servicos`, `/emitir/{osId}`, `/consultar/{osId}`, `/cancelar/{osId}` | `GET`, `POST` | `NfseController` | `vNfse` / `aNfse` / `dNfse` |
| **Financeiro** | `/api/v1/financeiro(/id)`, `/api/v1/financeiro/resumo` | `GET` | `FinanceiroController` | `vLancamento` |
| **Frota** | `/api/v1/veiculos(/id)` | `GET` | `VeiculosController` | `vVeiculo` |
| **Vendas** | `/api/v1/vendas(/id)` | `GET` | `VendasController` | `vVenda` |
| **Cliente Conecte** | `/api/v1/client`, `/auth`, `/os(/id)`, `/compras(/id)`, `/cobrancas`, `/contratos(/id)` | `GET`, `POST` | `Client*Controller` | Token JWT do Cliente |

---

## 3. Collection Postman v2.1.0 & Environments

Os artefatos prontos para importação no Postman estão disponíveis em [`postman/`](../../postman/README.md):
- **Collection v2.1.0 (14 pastas, 72 requisições cobrindo todas as 54 rotas):** `postman/STEOS_API_v1.postman_collection.json`
- **Environment Desenvolvimento Local:** `postman/STEOS_Local_Dev.postman_environment.json` (`http://localhost:8080`)
- **Environment Produção:** `postman/STEOS_Production.postman_environment.json` (`https://steos.stesistemas.com`)

> Os endpoints `POST /index.php/api/v1/login`, `GET /index.php/api/v1/reGenToken` e `POST /index.php/api/v1/client/auth` possuem scripts de resposta que gravam automaticamente `{{access_token}}` e `{{client_access_token}}` na Collection e no Environment ativo.
