# Documentação: Relatorios.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\Relatorios.php`
- **Tipo:** Controller
- **Módulo:** relatorios

## 2. Propósito
Responsável pelo processamento e lógica pertinente à camada de Controller do módulo de relatorios.

## 3. Dependências
- **Views Carregadas:**
  - `relatorios/imprimir/imprimirVendas`
  - `relatorios/imprimir/imprimirServicos`
  - `relatorios/imprimir/imprimirEtiquetas`
  - `relatorios/imprimir/imprimirFinanceiro`
  - `relatorios/imprimir/imprimirComissaoServicos`
  - `relatorios/imprimir/imprimirTopo`
  - `relatorios/imprimir/imprimirClientes`
  - `relatorios/imprimir/imprimirOs`
  - `relatorios/imprimir/imprimirProdutos`
  - `relatorios/imprimir/imprimirComissaoVendas`
  - `relatorios/imprimir/imprimirSKU`
- **Models Carregados:**
  - `tecnicos_os_model`
  - `Relatorios_model`
  - `steos_model`
  - `contas_model`
  - `os_model`
  - `Usuarios_model`
  - `tecnicos_model`
  - `Steos_model`

## 4. Estrutura Interna
### Método: `__construct`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `__construct`.

### Método: `index`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `index`.

### Método: `clientes`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `clientes`.

### Método: `produtos`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtos`.

### Método: `clientesCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `clientesCustom`.

### Método: `clientesRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `clientesRapid`.

### Método: `produtosRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosRapid`.

### Método: `produtosRapidMin`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosRapidMin`.

### Método: `produtosCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosCustom`.

### Método: `produtosEtiquetas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `produtosEtiquetas`.

### Método: `sku`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `sku`.

### Método: `skuRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `skuRapid`.

### Método: `skuCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `skuCustom`.

### Método: `servicos`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `servicos`.

### Método: `servicosCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `servicosCustom`.

### Método: `servicosRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `servicosRapid`.

### Método: `os`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `os`.

### Método: `osRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `osRapid`.

### Método: `osCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `osCustom`.

### Método: `financeiro`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `financeiro`.

### Método: `financeiroRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `financeiroRapid`.

### Método: `financeiroCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `financeiroCustom`.

### Método: `vendas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `vendas`.

### Método: `vendasRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `vendasRapid`.

### Método: `vendasCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `vendasCustom`.

### Método: `receitasBrutasMei`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `receitasBrutasMei`.

### Método: `receitasBrutasRapid`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `receitasBrutasRapid`.

### Método: `receitasBrutasCustom`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `receitasBrutasCustom`.

### Método: `comissaoOs`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `comissaoOs`.

### Método: `comissaoServicosImprimir`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `comissaoServicosImprimir`.

### Método: `comissaoVendas`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `comissaoVendas`.

### Método: `comissaoVendasImprimir`
- **Parâmetros:** ``
- **Lógica Principal:** Gerencia o fluxo para a funcionalidade `comissaoVendasImprimir`.


## 5. Fluxo de Dados
Recebe a requisição HTTP (GET/POST), valida via `form_validation`, delega a leitura/escrita para o respectivo Model, e encaminha os resultados em array `$data` para a View correspondente.

## 6. Rotas Relacionadas
Mapeamento automático via CodeIgniter: `site_url('relatorios/metodo')`.

## 7. Observações e Regras de Negócio
- Documentação autogerada para reconstrução.
- Certifique-se de manter os padrões do CodeIgniter 3.
