# Mapa de Rotas do Sistema STEOS v4.53.2

O STEOS combina **Auto-Routing** do CodeIgniter 3 com **mapeamentos explícitos** em `application/config/routes.php` (para controllers CamelCase em Linux e rotas da API REST v1 via `routes_api.php`).

## Rotas Explícitas Definidas em `application/config/routes.php`

```php
$route['default_controller'] = 'steos';
$route['404_override'] = '';

// Blindagem Case-Sensitive Linux para Controllers CamelCase
$route['classificacaofinanceira'] = 'ClassificacaoFinanceira';
$route['classificacaofinanceira/(.*)'] = 'ClassificacaoFinanceira/$1';
$route['grupofinanceiro'] = 'GrupoFinanceiro';
$route['grupofinanceiro/(.*)'] = 'GrupoFinanceiro/$1';
$route['centrogastos'] = 'CentroGastos';
$route['centrogastos/(.*)'] = 'CentroGastos/$1';
$route['formapagamento'] = 'FormaPagamento';
$route['formapagamento/(.*)'] = 'FormaPagamento/$1';

if (filter_var($_ENV['API_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
    require APPPATH . 'config/routes_api.php';
}
```

## Mapa Analítico dos Módulos Web

| Rota Base | Funcionalidade Principal | Model Associado |
| :--- | :--- | :--- |
| `/steos` | Dashboard, calendário FullCalendar, configurações e emitente | `Steos_model` |
| `/login` | Autenticação da equipe administrativa | `Steos_model` |
| `/os` | Ordens de Serviço, Checklists Digitais, Assinaturas e NFS-e (`/emitir_nfse`, `/consultar_nfse`, `/cancelar_nfse`) | `Os_model`, `Tecnicos_os_model`, `Equipamentos_os_model` |
| `/contratos` | Contratos recorrentes, O.S. Preventivas, Sistemas/Checks e Assinaturas Asaas | `Contratos_model` |
| `/sistemas` & `/equipamentos` | Catálogo de Sistemas, Checks e Equipamentos/Ativos | `Sistemas_model`, `Equipamentos_model`, `Modelos_model` |
| `/clientes`, `/produtos`, `/servicos` | Cadastros comerciais e controle de estoque | `Clientes_model`, `Produtos_model`, `Servicos_model` |
| `/vendas` | Vendas e PDV com integração financeira e cobranças | `Vendas_model` |
| `/financeiro` & `/contas` | Lançamentos (receitas/despesas), gráficos Chart.js, exportações e contas | `Financeiro_model`, `Contas_model`, `Lancamentos_contas_model` |
| `/bancos`, `/centrogastos`, `/classificacaofinanceira`, `/formapagamento`, `/grupofinanceiro` | Cadastros auxiliares do módulo Financeiro | Respectivos `*_model` |
| `/cobrancas` & `/asaas_webhook` | Gestão de cobranças Gateway Asaas/Efí/MP e recepção de Webhooks v3 | `Cobrancas_model` |
| `/nfse` & `/servicos_nfse` | Listagem de NFS-e emitidas e catálogo fiscal (LC 116, NBS, `cTribNac`, IBS/CBS) | `Servicos_nfse_model`, `Os_model` |
| `/veiculos` & `/gasolina` | Frota, trava diária de odômetro, abastecimento, óleo e `veiculos_logs` | `Veiculos_model`, `Gasolina_model` |
| `/tecnicos`, `/garantias`, `/arquivos`, `/relatorios`, `/email`, `/auditoria`, `/usuarios`, `/permissoes` | Suporte operacional, relatórios, logs e ACL | Respectivos `*_model` |
| `/mine` | Portal do Cliente **Conecte** (isolamento estrito por `clientes_id`) | `Conecte_model`, `ResetSenhas_model` |
| `/tools` | CLI para migrations, seeds, `schema_snapshot`, `diff_schema` e `sync_migration` | `dbforge` |

## Mapa de Endpoints da API RESTful (`/api/v1` — `routes_api.php`)

- **Core:** `GET /api/v1`, `GET /api/v1/emitente`, `GET /api/v1/calendario`, `GET /api/v1/audit`
- **Auth & Usuários:** `POST /api/v1/login`, `GET /api/v1/reGenToken`, `GET /api/v1/conta`, CRUD `/api/v1/usuarios(/(:num))`
- **Cadastros & O.S.:** CRUD `/api/v1/clientes(/(:num))`, `/api/v1/produtos(/(:num))`, `/api/v1/servicos(/(:num))`, `/api/v1/os(/(:num))`, sub-recursos `/produtos`, `/servicos`, `/anotacoes`, `/anexos`, `/desconto`, `/checklists`
- **Módulos STEOS v4.53.2:** `/api/v1/contratos(/(:num))`, `/api/v1/cobrancas(/(:num))(/pix)`, `/api/v1/nfse(/servicos|/emitir/(:num)|/consultar/(:num)|/cancelar/(:num)|/(:num))`, `/api/v1/financeiro(/resumo|/(:num))`, `/api/v1/veiculos(/(:num))`, `/api/v1/vendas(/(:num))`
- **Portal do Cliente (JWT Client):** `POST /api/v1/client/auth`, `GET /api/v1/client`, `/api/v1/client/os(/(:num))`, `POST /api/v1/client/os`, `GET /api/v1/client/compras(/(:num))`, `GET /api/v1/client/cobrancas`, `GET /api/v1/client/contratos(/(:num))`
