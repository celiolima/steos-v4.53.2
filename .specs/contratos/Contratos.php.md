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

## Permissões e Segurança
- Usa a checagem padrão de sessão para autenticação do lado administrador.
