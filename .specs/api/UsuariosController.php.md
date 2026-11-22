# Documentação: UsuariosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\UsuariosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Gerencia autenticação JWT de usuários internos (`login_post`, `reGenToken_get`, `conta_get`) e o CRUD de `usuarios` protegido pela permissão `cUsuario`.

## 3. Segurança Aplicada
- Tokens emitidos em `login_post()` e `reGenToken_get()` carregam a claim `'type' => 'staff'` (isolada de tokens de clientes em `REST_Controller::logged_user()` / `logged_client()`).
- `reGenToken_get()` exige usuário ativo (`situacao === 1`), e-mail válido e `dataExpiracao` vigente antes de reemitir token.
- Senhas recebidas em `index_post` e `index_put` são criptografadas com `password_hash(..., PASSWORD_DEFAULT)`.
- Validação de campos no `index_put` usa nomes corretos de parâmetros (`$this->put('campo', true)`).
- Campo `senha` é sempre removido (`unset`) das respostas JSON de `index_get`, `index_post`, `index_put` e `conta_get`.
- Proteção contra desativação ou exclusão do usuário Super Admin (`id == 1`).
