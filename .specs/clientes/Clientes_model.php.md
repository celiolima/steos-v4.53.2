# Documentação: Clientes_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Clientes_model.php`
- **Tipo:** Model
- **Módulo:** clientes

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de clientes.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `get`
- **Parâmetros:** `$table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `get`.

### Método: `getById`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getById`.

### Método: `add`
- **Parâmetros:** `$table, $data`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `add`.

### Método: `edit`
- **Parâmetros:** `$table, $data, $fieldID, $ID`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `edit`.

### Método: `delete`
- **Parâmetros:** `$table, $fieldID, $ID`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `delete`.

### Método: `count`
- **Parâmetros:** `$table`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `count`.

### Método: `getOsByCliente`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getOsByCliente`.

### Método: `getAllOsByClient`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getAllOsByClient`.

### Método: `removeClientOs`
- **Parâmetros:** `$os`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `removeClientOs`.

### Método: `getAllVendasByClient`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getAllVendasByClient`.

### Método: `removeClientVendas`
- **Parâmetros:** `$vendas`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `removeClientVendas`.

### Método: `emailExists`
- **Parâmetros:** `$email, $id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `emailExists`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
