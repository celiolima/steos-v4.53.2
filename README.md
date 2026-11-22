# STEOS v4.53.2 — Sistema de Gestão ERP Comercial e de Serviços

O **STEOS** é um ERP completo voltado para empresas de assistência técnica, prestação de serviços, locação de equipamentos e contratos de manutenção recorrente. Construído em **PHP 8.4** sobre **CodeIgniter 3 (MVC)** e **MySQL 8.0**, com integração nativa à **API v3 do Asaas** (Cobranças, Assinaturas, Webhooks e NFS-e no Portal Nacional + Reforma Tributária IBS/CBS) e **API RESTful v1 (JWT)**.

---

## Stack Tecnológica
- **Backend:** PHP 8.4 (`php:8.4-apache`), CodeIgniter 3, `CodePhix\Asaas` + `asaas_helper` cURL v3, `REST_Controller` JWT.
- **Banco de Dados:** MySQL 8.0 (`utf8mb4` / `utf8mb4_unicode_ci`) com migrations versionadas (`application/database/migrations/`) e snapshot estrutural (`migrations/schema_snapshot.json`).
- **Frontend:** Bootstrap 4, jQuery, DataTables, FullCalendar, Chart.js, Select2, SweetAlert2, Trumbowyg, MaskMoney e assinaturas em Canvas HTML5.
- **Infraestrutura:** Docker Compose (Desenvolvimento com live-reload vs Produção com Traefik Let's Encrypt e CI/CD GitHub Actions Zero-Data-Loss).

---

## Fluxo Automatizado de Ambientes (Dev vs Produção)

### 1. Desenvolvimento Local (`branch develop` + Asaas Sandbox)
```powershell
.\steos-env.ps1 start      # Garante rede Docker + override com live-reload e sobe http://localhost:8080
.\steos-env.ps1 pull-db    # Clona banco real da VPS para Dev preservando modo Sandbox
.\steos-env.ps1 status     # Status dos containers locais/VPS e runs do Actions
.\steos-env.ps1 stop       # Para containers locais
```

### 2. Banco de Dados & Migrations (`steos-db-sync.ps1`)
```powershell
.\steos-db-sync.ps1 diff
.\steos-db-sync.ps1 sync "descricao_da_alteracao"
.\steos-db-sync.ps1 migrate
```

### 3. Versionamento & Deploy em Produção (`branch main` -> VPS `ste01`)
```powershell
.\steos-env.ps1 save "mensagem"     # Salva commit apenas na develop (sem deploy)
.\steos-env.ps1 release "mensagem"  # Merge develop -> main e push (dispara GitHub Actions -> /usr/local/bin/steos-deploy)
```
O deploy em produção realiza backup `mysqldump` pré-deploy, protege `.env.production` e uploads do volume `steos_data`, executa `php index.php tools migrate` e conta com rollback automático.

---

## Portas e Acessos Locais
- **Aplicação Web STEOS:** [http://localhost:8080](http://localhost:8080)
- **Portal do Cliente (Conecte):** [http://localhost:8080/index.php/mine](http://localhost:8080/index.php/mine)
- **phpMyAdmin:** [http://localhost:9080](http://localhost:9080)
- **MySQL 8.0:** `localhost:3306`

---

## Segurança, ACL Granular & Servidor MCP
- Controle de acesso baseado em **92 chaves granulares** (migration `20261122153000_add_granular_permissions.php` / `026`) + perfil dedicado **`API MCP Admin`** e conta de serviço **`mcp.service@stesistemas.com`** (migration `20261122170000_add_mcp_service_profile.php` / `027`), aplicadas de forma consistente nos controllers Web (`MY_Controller`), views/menus (`menu.php`, `topo.php`) e endpoints JWT da API RESTful v1 (com separação `type=staff` vs `type=client`).
- **Servidor MCP Privado (`mcp-server/`):** Endpoints isolados `/mcp/admin` e `/mcp/client` (Streamable HTTP, SSE legado e STDIO) para **n8n**, bots **WhatsApp**, **Postman** e **IDEs**. Veja o guia em **[.specs/mcp/README.md](./.specs/mcp/README.md)**.

## Documentação Complementar
- **[DOCUMENTACAO_STEOS.md](./DOCUMENTACAO_STEOS.md)** — Documentação técnica consolidada do sistema.
- **[.specs/](./.specs/)** — Especificações detalhadas por módulo, controller, model, view, API v1, Servidor MCP e configurações.
- **[postman/](./postman/)** — Collection Postman v2.1.0 ([`STEOS_API_v1.postman_collection.json`](./postman/STEOS_API_v1.postman_collection.json)) e Environments Dev/Produção com todas as 55 rotas da API v1.
- **[.agents/memoria.md](./.agents/memoria.md)** — Memória persistente global e governança dos agentes especialistas.
