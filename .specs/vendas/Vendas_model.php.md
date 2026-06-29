# Documentação: Vendas_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Vendas_model.php`
- **Tipo:** Model
- **Módulo:** vendas

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de vendas.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `get`
- **Parâmetros:** `$table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `get`.

### Método: `getVendas`
- **Parâmetros:** `$table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getVendas`.

### Método: `getById`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getById`.

### Método: `isEditable`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `isEditable`.

### Método: `getByIdCobrancas`
- **Parâmetros:** `$id`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getByIdCobrancas`.

### Método: `getProdutos`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getProdutos`.

### Método: `getCobrancas`
- **Parâmetros:** `$id = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getCobrancas`.

### Método: `add`
- **Parâmetros:** `$table, $data, $returnId = false`
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

### Método: `autoCompleteProduto`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteProduto`.

### Método: `autoCompleteCliente`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteCliente`.

### Método: `autoCompleteUsuario`
- **Parâmetros:** `$q`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `autoCompleteUsuario`.

### Método: `getQrCode`
- **Parâmetros:** `$id, $pixKey, $emitente`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getQrCode`.

### Método: `getTotalVendas`
- **Parâmetros:** `$idVendas`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `getTotalVendas`.

### Método: `faturarVenda`
- **Parâmetros:** `$vendas_id, $dataLancamento, $dadosVenda`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `faturarVenda`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
