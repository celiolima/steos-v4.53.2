# Documentação: csrf.js

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\assets\js\csrf.js`
- **Inclusão Global:** `application/views/tema/topo.php` (`csrf.js?v=20261121`)
- **Módulo:** `assets_js`

## 2. Propósito e Correção Crítica de POST AJAX
Injeta automaticamente o token CSRF do CodeIgniter (`STEOS_TOKEN` lido do cookie `STEOS_COOKIE`) em todos os `<form>` HTML e em todas as requisições AJAX modificadoras de estado (`POST`, `PUT`, `PATCH`, `DELETE`).

## 3. Estrutura Interna
- `getCookie(name)`: lê o token CSRF ativo no cookie do navegador.
- `setCsrfTokenInAllForms(csrfTokenName, csrfCookieName)`: atualiza os `<input type="hidden" name="STEOS_TOKEN">` em todos os formulários da página no load e ao fim de cada chamada AJAX.
- `$.ajaxPrefilter(function (options))`:
  - Substitui o antigo `beforeSend` no `$.ajaxSetup`, evitando falha de HTTP `403 Forbidden` quando uma chamada `$.ajax({ type: 'POST' })` é feita **sem `data`** (jQuery 1.12.4 antes omitia `Content-Type` e concatenava `"undefined&STEOS_TOKEN=..."`).
  - Suporta `FormData`, `string`, `object` e `options.data === undefined/null`, garantindo envio válido de `Content-Type: application/x-www-form-urlencoded; charset=UTF-8` e mantendo compatibilidade quando callbacks individuais definem `beforeSend`.
