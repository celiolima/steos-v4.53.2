# Postman Collection & Environments — STEOS v4.53.2 (API RESTful v1)

Esta pasta contém a especificação oficial para testes e integração da **API RESTful v1 do STEOS** no **Postman** (formato **Collection v2.1.0**), sincronizada integralmente com as **54 rotas** de `application/config/routes_api.php` (**72 operações HTTP** distribuídas em **14 pastas**).

---

## Arquivos Disponíveis

| Arquivo | Descrição | Uso no Postman |
| :--- | :--- | :--- |
| **[STEOS_API_v1.postman_collection.json](./STEOS_API_v1.postman_collection.json)** | Collection v2.1.0 completa com as 14 pastas, 72 requisições, exemplos de payload JSON/form-data e scripts de captura automática de JWT. | Importar em **Collections** (`Import`). |
| **[STEOS_Local_Dev.postman_environment.json](./STEOS_Local_Dev.postman_environment.json)** | Environment configurado para `http://localhost:8080` (ambiente local Docker). | Importar em **Environments**. |
| **[STEOS_Production.postman_environment.json](./STEOS_Production.postman_environment.json)** | Environment configurado para `https://steos.stesistemas.com` (produção VPS Hostinger). | Importar em **Environments**. |

---

## Como Importar no Postman

### Opção A — Via Arquivo Local
1. Abra o **Postman** e clique em **Import** (canto superior esquerdo).
2. Selecione ou arraste os 3 arquivos `.json` da pasta `postman/`:
   - `STEOS_API_v1.postman_collection.json`
   - `STEOS_Local_Dev.postman_environment.json`
   - `STEOS_Production.postman_environment.json`
3. No seletor de ambiente (canto superior direito), escolha **`STEOS - Desenvolvimento Local (Docker)`** ou **`STEOS - Produção (Hostinger)`**.

### Opção B — Importação Direta por URL
Se o STEOS estiver ativo no navegador, você também pode colar a URL abaixo no modal **Import** do Postman:
- Local Dev: `http://localhost:8080/postman/STEOS_API_v1.postman_collection.json`
- Produção: `https://steos.stesistemas.com/postman/STEOS_API_v1.postman_collection.json`

---

## Fluxo de Autenticação Automática (JWT)

### 1. Painel Administrativo / Staff (Pastas `01` a `13`)
1. Ajuste `admin_email` e `admin_password` no Environment selecionado.
2. Execute **`01. Autenticação & Conta (Admin / Staff) > Login Administrativo (JWT)`** (`POST /index.php/api/v1/login`).
3. O script pós-resposta (`Tests`) extrai automaticamente `result.access_token` e grava na variável `{{access_token}}` da Collection e do Environment ativo.
4. Todas as pastas `01` a `13` herdam `Authorization: Bearer {{access_token}}`.
5. Quando o token expirar (`API_TOKEN_EXPIRE_TIME`, padrão `60` segundos no `.env` atual — ajuste conforme necessidade em produção/homologação), execute **`Renovar Token JWT (reGenToken)`** (`GET /index.php/api/v1/reGenToken`) para renovar sem precisar reenviar senha.

### 2. Portal do Cliente Conecte / App (Pasta `14`)
1. Ajuste `client_email` e `client_password` (padrão STEOS: `steas@123`) no Environment.
2. Execute **`14. Portal do Cliente (Conecte / App) > Login do Cliente (Portal Conecte)`** (`POST /index.php/api/v1/client/auth`).
3. O script pós-resposta grava automaticamente `{{client_access_token}}`.
4. Toda a pasta `14` utiliza autenticação própria `Bearer {{client_access_token}}`, com proteção multi-tenant estrita (`HTTP 403` em tentativas de acesso cruzado).

---

## Observações Importantes de Payload & Configuração
- **Pré-requisito no `.env`:** `API_ENABLED=true`, `API_JWT_KEY` configurada e header `Authorization` repassado pelo `.htaccess` raiz.
- **Formato de Datas em O.S.:** Os endpoints `POST /api/v1/os` e `PUT /api/v1/os/{id}` exigem `dataInicial` e `dataFinal` no padrão brasileiro **`DD/MM/YYYY`**. Filtros `GET` (`calendario`, `nfse`, `cobrancas`, `financeiro`, `vendas`) aceitam **`YYYY-MM-DD`**.
- **NFS-e Portal Nacional & IBS/CBS:** O endpoint `POST /api/v1/nfse/emitir/{idOs}` separa `federal_service_tax_code` (`taxes.federalServiceTaxCode`) de `operation_indicator_code` (`taxes.operationIndicatorCode`). Consulte os 33 códigos oficiais em `GET /api/v1/nfse/servicos`.
