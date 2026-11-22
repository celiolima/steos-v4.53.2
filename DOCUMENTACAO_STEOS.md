# Documentação Técnica Oficial — STEOS v4.53.2

Documento de referência da arquitetura, módulos de negócio, integração Asaas v3 (Portal Nacional + IBS/CBS), API RESTful v1, segurança e automação de ambientes do **STEOS**.

---

## 1. Arquitetura Geral (CodeIgniter 3 MVC + REST API v1)

- **Framework & Runtime:** PHP 8.4 sobre **CodeIgniter 3**.
- **Controllers Web (`application/controllers/`):** Herdam `MY_Controller`, validam autenticação, permissões ACL (`Permission`) e CSRF (`STEOS_TOKEN`), delegam SQL aos Models (`application/models/`) e renderizam Views via `$this->layout()`.
- **Controllers REST (`application/controllers/api/v1/`):** Herdam `REST_Controller` com autenticação stateless **JWT Bearer** (`Authorization_Token`), habilitados via `API_ENABLED=true` em `application/config/routes.php` -> `routes_api.php`.
- **Blindagem Case-Sensitive Linux:** Controllers CamelCase possuem rotas explícitas em minúsculas em `routes.php` (`classificacaofinanceira`, `grupofinanceiro`, `centrogastos`, `formapagamento`).

---

## 2. Infraestrutura Docker, Separação de Ambientes & CI/CD

### Containers (`docker-compose.yml` / `docker-compose.override.yml`)
- `steosMySql` (`mysql:8.0`, porta `3306`): charset `utf8mb4`, collation `utf8mb4_unicode_ci`.
- `steos` (`php:8.4-apache`, porta `8080`): executa wait-loop `--skip-ssl`, sincroniza assets estáticos de `/usr/src/steos-assets` preservando uploads/anexos, executa `php index.php tools migrate` e aplica trava `ALLOW_INITIAL_DB_IMPORT` contra truncamento acidental.
- `steosMyadmin` (`phpmyadmin`, porta `9080`): interface administrativa protegida por Traefik SSL em produção.

### Ambientes Isolados (`steos-env.ps1` + `steos-db-sync.ps1`)
- **Desenvolvimento (`develop`):** `docker-compose.override.yml` monta `.` em `/var/www/html` com live-reload e `application/.env_dev` (Asaas Sandbox). `base_url` dinâmico por `HTTP_HOST` em `config.php`.
- **Produção (`main`):** `.github/workflows/deploy.yml` conecta via SSH restrito na VPS `ste01` e executa `/usr/local/bin/steos-deploy` (backup pré-deploy, preservação de `.env.production`, rebuild `--no-deps steos`, migrations e rollback automático).
- **Schema Sync:** `Tools::schema_snapshot`, `Tools::diff_schema` e `Tools::sync_migration` mantêm `migrations/schema_snapshot.json` e `application/database/migrations/` sincronizados.

---

## 3. Integração Financeira & Fiscal Asaas API v3

- **Core Gateway (`application/libraries/Gateways/Asaas.php` + `application/helpers/asaas_helper.php`):**
  - Endpoints oficiais v3: `https://api.asaas.com` (Produção) e `https://api-sandbox.asaas.com` (Sandbox).
  - Clientes são sincronizados via `clientes.asaas_id` validado no ambiente ativo.
  - Para requisições `POST` e `PUT` sem body (ex: cancelamento de NFS-e), `asaas_api_request()` envia `'{}'` com `Content-Length` explícito.
- **NFS-e Portal Nacional (`useNationalPortal = 1`) & Reforma Tributária (IBS/CBS):**
  - `Os::emitir_nfse()` e `NfseController::emitir_post()` consultam `Asaas::obterFiscalInfo()`: no Portal Nacional enviam `municipalServiceId: null`, `municipalServiceCode: null` e `municipalServiceName` exato sem duplicação.
  - **Tabela `servicos_nfse` (Migrations `20261121151000` e `20261121173000`):** separa corretamente **`codigo_tributacao_nacional`** (`cTribNac` -> `taxes.federalServiceTaxCode`, ex: `140101` e `010101`) de **`ind_operacao`** (`cIndOp` -> `taxes.operationIndicatorCode`, vazio `""` para serviços padrão ou um dos **33 códigos oficiais** do Asaas, como `010106` para Locação `99.03`), além de `cst_ibs_cbs` (`000`), `class_trib_ibs_cbs` (`000001`) e `codigo_nbs`.
  - **Cancelamento Assíncrono (`Os::cancelar_nfse`):** trata retornos `CANCELED`, `PROCESSING_CANCELLATION` e `CANCELLATION_DENIED` e concilia via `consultarNfse`.
- **Webhooks (`Asaas_webhook.php`):**
  - Autentica `asaas-access-token` com `hash_equals()`.
  - Processa `PAYMENT_CREATED` (vinculação automática de assinaturas à próxima O.S. preventiva livre), `PAYMENT_RECEIVED`/`PAYMENT_CONFIRMED` (baixa transacional em `lancamentos` + `cobrancas`), `PAYMENT_OVERDUE` e eventos `INVOICE_*` (atualizando status, número, PDF e XML da NFS-e na O.S.).

---

## 4. Módulos Core de Negócio

1. **Contratos & O.S. Preventivas (`Contratos.php` / `Contratos_model.php`):**
   - Tela em 7 abas (Geral com `tecnico_id`, O.S., Faturas/Cobranças, Vendas, Sistemas & Checks, Anexos, Checklists).
   - Recálculo automático por `sistemas_contratos` (`recalcularValorTotalContrato`) e geração de O.S. Preventivas (`manutPreventiva = 1`, status `A Sair | Aguard Conclusão`).
2. **Ordens de Serviço, Checklists Digitais & Calendário (`Os.php` / `Steos.php`):**
   - Checklists em `os_checklists` / `os_checklists_itens` com assinaturas Canvas Base64 (`Os::assinar`).
   - FullCalendar em `pt-br` com destaque verde (`#00a65a`) / dourado preventiva (`#AEB404`) e suporte Mobile Touch.
3. **Financeiro & Cadastros Auxiliares (`Financeiro.php`):**
   - Gráficos dinâmicos Chart.js (`graficos_dinamicos_json`), relatórios sem paginação via `get1()` (prevenção de `LIMIT 0`), impressão isolada e tabelas auxiliares (`Bancos`, `CentroGastos`, `ClassificacaoFinanceira`, `FormaPagamento`, `GrupoFinanceiro`).
4. **Frota / Veículos (`Veiculos.php` / `Gasolina.php`):**
   - Controle de autonomia e trava de 1 lançamento de odômetro por dia, com auditoria registrada em `veiculos_logs`.
5. **Portal do Cliente Conecte (`Mine.php` / `Conecte_model.php`):**
   - Autenticação isolada (`isCliente`), recuperação por token criptográfico e isolamento rigoroso `WHERE clientes_id = <ID>` em O.S., Vendas, Cobranças, Contratos e NFS-e.
6. **API RESTful v1 (`application/controllers/api/v1/`):**
   - Endpoints para Dashboard/Calendário, Usuários (com hash bcrypt obrigatório no `PUT`), Clientes/Produtos/Serviços (com `escape_like_str`), O.S. (com checklists/técnicos/equipamentos), Contratos, Cobranças/Pix, NFS-e (IBS/CBS), Financeiro, Veículos, Vendas e endpoints Client protegidos contra IDOR.

---

## 5. Segurança & Governança Multi-Agente
- **CSRF Web:** `assets/js/csrf.js?v=20261121` utiliza `$.ajaxPrefilter` garantindo envio válido de `STEOS_TOKEN` e `Content-Type` inclusive em requisições POST sem corpo.
- **Agentes Especialistas (`.agents/`):** Memória global `.agents/memoria.md`, regra obrigatória `.agents/rules/memoria-e-agentes.md` e 6 agentes isolados (`ste_dev`, `especialista-steos`, `especialista-asaas`, `especialista-deploy`, `gerenciando-codeigniter`, `gerenciando-frontend-jjb`).
