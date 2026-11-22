# Controller `Contratos.php`

## Objetivo
Responsável por gerenciar todo o ciclo de vida dos contratos de prestação de serviço, BPO e locação, orquestrando as dependências financeiras, sistemas vinculados e regras de desconto.

## Funcionalidades Principais
- **Gerenciamento de Contratos (`adicionar`, `editar`, `excluir`, `gerenciar`)**: Criação de contratos atrelados a um Cliente (`clientes_id`) e Técnico (`tecnico_id`).
- **Motor de Cálculo (`recalcularValorTotalContrato`)**: A base do contrato é a soma rigorosa dos Sistemas/Equipamentos vinculados. O sistema aplica descontos e acréscimos dinâmicos (%) ou (R$) sobre esta base.
- **Sistemas e Ativos (`adicionarSistema`, `excluirSistema`)**: Vincula serviços e equipamentos (`sistemas_contratos`) a um contrato específico, herdando os "Checks" padrões.
- **Checklists Técnicos (`adicionarCheckManual`)**: Permite inclusão manual de itens no checklist preventivo.

## Relacionamentos MVC
- **Models Utilizados**: `Contratos_model`, `Clientes_model`, `Tecnicos_model`.
- **Views Carregadas**: `contratos/contratos`, `contratos/adicionarContrato`, `contratos/editarContrato`.

## Permissões e Segurança (ACL Granular)
- Controle granular com 4 chaves próprias via `requireWebPermission()` e `requireJsonPermission()`:
  - `vContrato`: `gerenciar`, `downloadanexo`, `checklist`
  - `aContrato`: `adicionar`
  - `eContrato`: `editar`, `adicionarSistema`, `excluirSistema`, `adicionarCheckManual`, `atualizarCheck`, `anexar`, `excluirAnexo`, `gerarOsPreventivas`, `ativarAssinaturaAsaas`
  - `dContrato`: `excluir`
  - Endpoints AJAX de suporte (`auto_complete_cliente`, `get_contratos_por_cliente`, `autoCompleteSistema`) aceitam qualquer permissão de leitura/escrita de Contrato, O.S. ou Venda.
- Views `contratos.php` e `editarContrato.php` condicionam botões, formulários de upload/sistemas e modais às chaves `a/e/dContrato`.
