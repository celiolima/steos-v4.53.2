# Documentação: ClientLoginController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\client\ClientLoginController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api` (Portal do Cliente)

## 2. Propósito
Autentica clientes finais em `POST /api/v1/client/auth` usando `password_verify` sobre `clientes.senha` e emite token JWT contendo `uid`, `nome` e `email`.
