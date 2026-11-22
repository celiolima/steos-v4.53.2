# Documentação: ClientesController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\ClientesController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
CRUD RESTful de `clientes` (`/api/v1/clientes` e `/api/v1/clientes/(:num)`), validando permissões `vCliente`, `aCliente`, `eCliente` e `dCliente`.

## 3. Segurança e Regras de Negócio
- Termo de pesquisa `search` sanitizado com `$this->db->escape_like_str()` antes de ser repassado a `Clientes_model::get()`.
- Campo `senha` removido de todos os retornos JSON.
- Senha padrão ao criar sem informar senha: `steas@123` (hash bcrypt via `password_hash`).
- CPF/CNPJ validado via `verific_cpf_cnpj()` do `validation_helper`.
