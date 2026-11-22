# Documentação: ClientLoginController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\client\ClientLoginController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api` (Portal do Cliente)

## 2. Métodos & Segurança
- **`login_post()` (`POST /api/v1/client/auth`):**
  - Autentica clientes finais usando `password_verify` sobre `clientes.senha` e emite token JWT contendo `uid`, `nome`, `email` e `'type' => 'client'`.
  - Registra entrada em `logs` (`Audit_model`).
- **`auth_bot_post()` (`POST /api/v1/client/auth-bot`):**
  - Projetada para bots WhatsApp/n8n via endpoint `/mcp/client` do servidor MCP.
  - Exige header `X-Bot-Secret` validado contra `API_BOT_SECRET` via `hash_equals()` (`HTTP 403` se ausente ou incorreto).
  - Normaliza `documento` e `telefone`, exige CPF (11) ou CNPJ (14) válido **rejeitando sequências de dígitos repetidos** (`00000000000`, `11111111111`, etc. → `HTTP 400`).
  - Rate-limit em `sys_get_temp_dir()` (máximo 5 falhas em 15 minutos por IP + documento → `HTTP 429`).
  - Valida telefone contra `clientes.celular` e `clientes.telefone` tratando DDI `55`, DDD e 9º dígito (`phonesMatch()`).
  - Grava auditoria em `logs` (mascarando o documento em falhas) e emite JWT de curta duração (`type => 'client'`, `exp_ttl => 1800`).
