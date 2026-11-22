# Roteiro de Construção e Índice de Módulos (STEOS v4.53.2)

Guia consolidado de arquitetura, automação de ambientes, banco de dados e módulos do sistema **STEOS**.

---

## 1. Stack Tecnológica & Ambientes
- **Backend:** PHP 8.4 (`php:8.4-apache`) sobre **CodeIgniter 3 (MVC)**.
- **Banco de Dados:** MySQL 8.0 (`utf8mb4` / `utf8mb4_unicode_ci`) gerenciado por Query Builder e migrations timestampadas (`application/database/migrations/` + espelhos SQL em `migrations/sql/`).
- **Frontend:** Bootstrap 4.1.x, jQuery, Select2, DataTables, FullCalendar, Chart.js, Trumbowyg, SweetAlert2, Canvas Base64.
- **Containers & Ambientes:**
  - **Desenvolvimento (`develop`):** `docker-compose.yml` + `docker-compose.override.yml` (template `docker-compose.dev.yml`) com live-reload (`.:/var/www/html:cached`), `application/.env_dev` (Asaas Sandbox) e script `steos-env.ps1` (`start`, `stop`, `status`, `pull-db`, `save`, `release`).
  - **Produção (`main`):** VPS Hostinger `ste01` com Traefik SSL, CI/CD GitHub Actions (`.github/workflows/deploy.yml` -> `/usr/local/bin/steos-deploy`), backup `mysqldump` pré-deploy e rollback automático.

---

## 2. Utilitário de Schema e Migrations (`steos-db-sync.ps1` / `Tools.php`)
- `.\steos-db-sync.ps1 diff`: compara o MySQL local com `migrations/schema_snapshot.json`.
- `.\steos-db-sync.ps1 sync "nome"`: gera migration CI3 idempotente, atualiza `application/config/migration.php` e renova `schema_snapshot.json`.
- `.\steos-db-sync.ps1 migrate` / `snapshot`: aplica migrations ou gera snapshot manualmente.

---

## 3. Índice de Especificações em `.specs/`

- **`arquitetura_geral.md` & `mapa_de_rotas.md`**: Topologia MVC/REST/Asaas e mapa completo de URLs.
- **`api/`**: Especificação da API RESTful v1 (`README.md`, `ApiController`, `OsController`, `ContratosController`, `CobrancasController`, `NfseController`, `FinanceiroController`, `VeiculosController`, `VendasController`, `Client*Controller`).
- **`steos/`, `usuarios/`, `permissoes/`, `auditoria/`**: Dashboard FullCalendar, Login, ACL e logs.
- **`clientes/`, `produtos/`, `servicos/`, `tecnicos/`, `garantias/`, `equipamentos/`, `marcas/`, `modelos/`, `sistemas/`**: Cadastros mestres e catálogo de sistemas/equipamentos.
- **`os/` & `poc_checklists_whatsapp.md`**: Ordens de Serviço, Checklists Digitais v2, Assinaturas e emissão de NFS-e integrada.
- **`contratos/`**: Contratos recorrentes, cálculo automático por sistemas, O.S. Preventivas (`manutPreventiva = 1`) e assinaturas Asaas.
- **`nfse/`**: Portal Nacional (`useNationalPortal`), Reforma Tributária (IBS/CBS: `codigo_tributacao_nacional`, `ind_operacao`, `cst_ibs_cbs`, `class_trib_ibs_cbs`, `codigo_nbs`), `Asaas.php`, `asaas_helper.php`, `Nfse.php`, `Servicos_nfse.php` e `Servicos_nfse_model.php`.
- **`cobrancas/`**: Gateways Asaas/Efí/MercadoPago, `Asaas_webhook.php` e `Simulador_asaas.php`.
- **`financeiro/` & `contas/`**: Lançamentos, Chart.js, exportações e cadastros auxiliares (`Bancos`, `CentroGastos`, `ClassificacaoFinanceira`, `FormaPagamento`, `GrupoFinanceiro`).
- **`veiculos/` & `gasolina/`**: Gestão de frota com trava diária de odômetro e `veiculos_logs`.
- **`vendas/`, `relatorios/`, `arquivos/`, `email/`**: PDV, relatórios PDF/CSV/XLSX, anexos e fila de e-mails.
- **`mine/`, `conecte/` & `portal_do_cliente_conecte.md`**: Portal do Cliente isolado por `clientes_id`.
- **`configuracoes/`, `tools/`, `tema/`, `assets_js/`**: Configurações CI3, CLI, layout base e `csrf.js`.
