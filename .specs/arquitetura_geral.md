# Arquitetura Geral do Sistema STEOS v4.53.2

Este documento descreve a arquitetura atualizada do sistema **STEOS**, construído em **PHP 8.4** sobre o framework **CodeIgniter 3 (MVC estrito)**, banco **MySQL 8.0**, orquestração **Docker / Traefik**, integração nativa com **Asaas API v3 (Cobranças, Assinaturas, Webhooks e NFS-e Portal Nacional + IBS/CBS)** e **API RESTful v1 (JWT)**.

## Diagrama de Arquitetura e Fluxo de Execução

```mermaid
graph TD
    User((Usuário Web)) -->|HTTPS / Browser| Traefik[Traefik SSL Proxy]
    App((App / Integrador)) -->|REST JSON + JWT| Traefik
    AsaasWebhook((Asaas API v3 Webhook)) -->|POST /asaas_webhook/receber| Traefik

    Traefik -->|Porta 8080/80| Apache[Apache + PHP 8.4 - Container steos]

    subgraph CodeIgniter 3 [Camada Aplicação CI3]
        Router[Roteador CI3: routes.php + routes_api.php]
        CSRF[Filtro CSRF + Security XSS]
        WebCtrls[Web Controllers: Steos, Os, Contratos, Financeiro, Nfse, Veiculos, Mine]
        RestCtrls[REST API v1: ApiController, OsController, ContratosController, NfseController, Client*]
        Webhook[Asaas_webhook.php]
        Libs[Libraries & Helpers: Gateways/Asaas, asaas_helper, Authorization_Token, Permission]
        Models[Models: Query Builder $this->db]
    end

    Apache --> Router
    Router --> CSRF
    CSRF --> WebCtrls
    Router --> RestCtrls
    Router --> Webhook

    WebCtrls --> Libs
    RestCtrls --> Libs
    WebCtrls --> Models
    RestCtrls --> Models
    Webhook --> Models

    Libs -->|cURL API v3| AsaasAPI[(Asaas Produção / Sandbox)]
    Models -->|MySQLi / UTF8MB4| DB[(MySQL 8.0 - Container steos_mysql)]
```

## Descrição das Camadas
- **Segurança, Roteamento & ACL Granular (92 Chaves):** `routes.php` mantém auto-routing para controllers tradicionais, mapeia rotas explícitas em lowercase para controllers CamelCase (blindagem Linux case-sensitive) e carrega `routes_api.php` quando `API_ENABLED=true`. O controle de acesso funciona em **3 camadas sincronizadas** via `Permission::checkPermission()` (**92 chaves granulares** em JSON na tabela `permissoes`, migration `20261122153000`): (1) Controllers Web (`MY_Controller`), (2) Views/Menus (`menu.php`, `topo.php` e botões/abas), e (3) API RESTful v1 (`HTTP 401`). Requisições web POST usam proteção CSRF gerenciada via `assets/js/csrf.js` (`$.ajaxPrefilter`), Webhooks Asaas usam `hash_equals()` sobre `asaas-access-token` e a API v1 usa JWT Bearer (`Authorization_Token`).
- **Regra Arquitetural Obrigatória de ACL:** Todo novo módulo, controller, ação AJAX ou endpoint da API adicionado ao STEOS deve implementar granulação de permissões ACL (`v/a/e/d/r/c`), migration idempotente sem lockout para `Administrador`, UI em `Permissoes`, guards nas 3 camadas e atualização em `.specs/`.
- **Controllers & Regras de Negócio:** Orquestram ACL (`Permission`), `form_validation`, emissão fiscal/financeira e transações DB (`trans_begin`/`trans_commit`/`trans_rollback`).
- **Libraries & Helpers (`Asaas.php` + `asaas_helper.php`):** Encapsulam chamadas cURL à API v3 do Asaas, detecção do Portal Nacional (`useNationalPortal`), sanitização de códigos IBS/CBS (`federalServiceTaxCode` vs `operationIndicatorCode`) e conciliação automática de cobranças/NFS-e.
- **Models & Persistência (`MySQL 8.0`):** Isolamento estrito de queries via Query Builder; migrações versionadas em `application/database/migrations/` e sincronia de schema em `migrations/schema_snapshot.json` via `Tools.php`.
- **Views (`application/views/`):** Interface responsiva em Bootstrap 4, jQuery, DataTables, FullCalendar, Chart.js e SweetAlert2, incluindo área administrativa e Portal do Cliente Conecte (`Mine`).
