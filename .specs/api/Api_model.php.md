# Documentação: Api_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Api_model.php`
- **Tipo:** Model
- **Módulo:** api

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de api.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `lastRow`
- **Parâmetros:** `$table, $idColumn`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `lastRow`.

### Método: `getRowById`
- **Parâmetros:** `$table, $idColumn, $id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getRowById`.

### Método: `getUserByEmail`
- **Parâmetros:** `$email`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getUserByEmail`.

### Método: `searchUsuario`
- **Parâmetros:** `$search`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `searchUsuario`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
