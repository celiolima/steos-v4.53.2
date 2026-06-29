# Documentação: Relatorios_model.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\models\Relatorios_model.php`
- **Tipo:** Model
- **Módulo:** relatorios

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Model do módulo de relatorios.

## 3. Dependências
Nenhuma dependência explícita via `$this->load`.

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `get`
- **Parâmetros:** `$table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array'`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `get`.

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

### Método: `clientesCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null, $tipo = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `clientesCustom`.

### Método: `clientesRapid`
- **Parâmetros:** `$array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `clientesRapid`.

### Método: `produtosRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosRapid`.

### Método: `produtosRapidMin`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosRapidMin`.

### Método: `produtosCustom`
- **Parâmetros:** `$precoInicial = null, $precoFinal = null, $estoqueInicial = null, $estoqueFinal = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosCustom`.

### Método: `produtosEtiquetas`
- **Parâmetros:** `$de, $ate`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosEtiquetas`.

### Método: `skuRapid`
- **Parâmetros:** `$array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `skuRapid`.

### Método: `skuCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null, $cliente = null, $origem = null, $array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `skuCustom`.

### Método: `servicosRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `servicosRapid`.

### Método: `servicosCustom`
- **Parâmetros:** `$precoInicial = null, $precoFinal = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `servicosCustom`.

### Método: `osRapid`
- **Parâmetros:** `$array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `osRapid`.

### Método: `osCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null, $cliente = null, $responsavel = null, $status = null, $array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `osCustom`.

### Método: `financeiroRapid`
- **Parâmetros:** `$array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `financeiroRapid`.

### Método: `financeiroCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null, $tipo = null, $situacao = null, $array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `financeiroCustom`.

### Método: `vendasRapid`
- **Parâmetros:** `$array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `vendasRapid`.

### Método: `vendasCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null, $cliente = null, $responsavel = null, $array = false`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `vendasCustom`.

### Método: `receitasBrutasRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `receitasBrutasRapid`.

### Método: `receitasBrutasCustom`
- **Parâmetros:** `$dataInicial = null, $dataFinal = null`
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `receitasBrutasCustom`.

**Queries SQL:** Contem operacoes de banco de dados via Query Builder.


## 5. Fluxo de Dados
Recebe as instruções do Controller, executa as operações CRUD no banco de dados via `Query Builder` (`$this->db`), e devolve arrays/objetos para o Controller.

## 6. Rotas Relacionadas
N/A

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
