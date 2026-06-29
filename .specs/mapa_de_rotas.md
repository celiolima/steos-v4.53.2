# Mapa de Rotas do Sistema Steos

O Steos utiliza primariamente as capacidades de **Auto-Routing** (roteamento automático) nativas do CodeIgniter 3. Diferente de frameworks modernos que exigem a declaração explícita de cada rota, o sistema resolve as URLs diretamente espelhando a estrutura de pastas e funções dos Controllers.

## Regras de Roteamento Definidas em `routes.php`

```php
$route['default_controller'] = 'steos';
$route['404_override'] = '';
```

## Padrão de URL

A estrutura padrão seguida em todo o sistema é:
`http://seusistema.com/[Controller]/[Metodo]/[Parametro]`

### Mapa Analítico dos Módulos Funcionais (Controllers Base)

Abaixo o mapeamento das rotas de entrada de cada módulo do sistema extraído através do mapeamento em `controllers/`:

| Rota / Controller URL Base | Funcionalidade Mapeada | Model Associado Padrão |
| :--- | :--- | :--- |
| `/steos` | Dashboard e Controller Principal | `steos_model` |
| `/os` | Gestão de Ordens de Serviço | `os_model` |
| `/clientes` | Gestão de Clientes | `clientes_model` |
| `/vendas` | Gestão de Vendas (PDV) | `vendas_model` |
| `/produtos` | Cadastro e Gestão de Produtos | `produtos_model` |
| `/servicos` | Cadastro e Gestão de Serviços | `servicos_model` |
| `/financeiro` | Lançamentos, Receitas e Despesas | `financeiro_model` |
| `/cobrancas` | Gestão de Cobranças (Ex: Asaas/Gateways) | `cobrancas_model` |
| `/garantias` | Gestão de Garantias e Termos | `garantias_model` |
| `/relatorios` | Geração de Relatórios PDF/HTML | `relatorios_model` |
| `/arquivos` | Upload e Gerenciamento de Anexos | `arquivos_model` |
| `/usuarios` | Gestão de Usuários (Staff) | `usuarios_model` |
| `/permissoes` | Controle de Acesso e Perfis (ACL) | `permissoes_model` |
| `/login` | Autenticação e Recuperação de Senha | `steos_model` |
| `/mine` | Área do Cliente (Frontend Conecte) | `steos_model` |
| `/api/*` | Rotas da API RESTful (se habilitada) | Vários |

> **Nota sobre o Módulo Mine:** O controller `Mine.php` gerencia as views da "Área do Cliente", carregando recursos específicos para que clientes finais possam aprovar O.S., visualizar compras e pagar cobranças independentes do painel administrativo.
