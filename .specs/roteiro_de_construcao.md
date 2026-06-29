# Roteiro de Construção e Índice de Módulos (Steos)

Este documento centraliza as instruções e referências necessárias para reconstruir o **Sistema Steos** do zero, guiado pela arquitetura consolidada e especificações geradas em `.specs/`. 

---

## 1. Visão Geral do Sistema
O **Steos** é um sistema web robusto de ERP voltado para gestão comercial. Ele controla fluxo de caixa (Financeiro), Ordens de Serviço (O.S.), vendas diretas no balcão (PDV), estoque de produtos, emissão de cobranças automáticas via gateway, gestão de permissões (ACL) e oferece uma Área de Cliente externa (Conecte/Mine).

**Stack Tecnológica:**
- Backend: PHP 7.4/8.x estruturado sob o framework CodeIgniter 3.
- Database: MySQL / MariaDB via Query Builder.
- Frontend: HTML5, CSS3 (Bootstrap), jQuery e plugins (Select2, DataTables, SweetAlert2).
- Infraestrutura: Empacotado via Docker / Docker-compose.

---

## 2. Arquitetura do Projeto
A estrutura segue o MVC rigoroso do CodeIgniter 3:
- `/application/config/`: Contém `database.php`, `routes.php` e configurações de rotas.
- `/application/controllers/`: Recebem as requisições HTTP e validam (form_validation).
- `/application/models/`: Fazem as interações exclusivas com o banco.
- `/application/views/`: Os temas e as páginas interativas HTML/JS.
- `/assets/`: Javascripts customizados, CSS, Imagens, e pacotes vendor.

> **Diagramas de Apoio:**
> - Veja o [Diagrama de Arquitetura Geral](arquitetura_geral.md).
> - Veja o [Mapa de Rotas e Fluxos](mapa_de_rotas.md).

---

## 3. Configuração do Ambiente (Reconstrução)

Caso precise subir esse projeto do zero, execute:

1. **Repositório:** Clone os arquivos base.
2. **Docker:** Execute `docker-compose up -d --build` para subir o Nginx, PHP-FPM e MariaDB.
3. **Variáveis de Ambiente:** Configure os `.env` da raiz e de `/application/` garantindo as credenciais do banco de dados (ex: DB `steos`, user `steos`).
4. **Banco de Dados:** Importe `dados_steos.sql` para hidratar a base. As *Migrations* do CodeIgniter cuidarão de construir e atualizar as tabelas dinamicamente a partir desse ponto.

---

## 4. Índice de Módulos Funcionais
O sistema foi analisado e divido fisicamente nas seguintes pastas de especificação (dentro de `.specs/`):

- `steos/`: Core da aplicação e Dashboard.
- `usuarios/` & `permissoes/`: Autenticação e Controle de Acesso (ACL).
- `clientes/` & `fornecedores/`: Gestão de CRM.
- `produtos/` & `servicos/`: Catálogo e estoque.
- `os/`: Core Business - Ordens de Serviço.
- `vendas/`: Ponto de Venda.
- `financeiro/`: Lançamentos (Receitas/Despesas).
- `cobrancas/`: Integração de pagamentos Asaas/Outros.
- `arquivos/` & `relatorios/`: Suporte e Exportação.
- `mine/`: Área Externa do Cliente final.
- `configuracoes/`: Configurações de API, Email e Sistema.
- `assets_js/`: Scripts globais de front-end isolados.

*(A documentação técnica detalhada de cada controller/model de cada módulo encontra-se nos arquivos `.md` criados dentro de suas pastas correspondentes).*

---

## 5. Passo a Passo de Reconstrução
Para reescrever este sistema de forma modular, a ordem recomendada de construção é:

1. **Fundação Core:** Implemente `Steos.php` (Dashboard/Login) e `Usuarios.php` + `Permissoes.php`.
2. **Gestão Cadastral:** Crie `Clientes.php`, `Produtos.php` e `Servicos.php`. (Estes fornecem dados para vendas e O.S.).
3. **Core Business Operacional:** Implemente `Os.php` e `Vendas.php`.
4. **Gestão Monetária:** Crie `Financeiro.php` e `Cobrancas.php`.
5. **Suporte Operacional:** `Garantias.php`, `Relatorios.php` e `Arquivos.php`.
6. **Frontend do Cliente:** Crie o módulo `Mine.php` (Conecte).

> **Atenção (Armadilha CI3):** Garanta que a library de Sessões e o Helper de URL estejam carregados no `autoload.php`, caso contrário o sistema falhará silenciosamente nos redirects de permissões.

---

## 6. Novo Módulo: Contratos (A Ser Implementado)

Com o ecossistema estabilizado, nossa próxima demanda é o **Módulo de Contratos**.
Ele utilizará a estrutura já preparada e seguirá os mesmos padrões CI3 mapeados.

### Estrutura Projetada (`.specs/contratos/`):
- **Tabela:** O arquivo já existe em `migrations/sql/017_criar_tabela_contratos.sql`.
- **Model:** `Contratos_model.php` (CRUD: getById, add, edit, delete).
- **Controller:** `Contratos.php` (Endpoints para index, adicionar, editar, imprimir, excluir).
- **Views:**
  - `views/contratos/contratos.php` (DataTables listando).
  - `views/contratos/adicionarContrato.php`
  - `views/contratos/editarContrato.php`
  - `views/contratos/imprimirContrato.php` (Layout de assinatura física/digital).
- **Dependências:** Estará ligado à `Clientes_model` (para puxar os dados de quem contrata).

---

## 7. Checklist de Validação Pós-Reconstrução
- [ ] Login admin funciona?
- [ ] Permissões de um usuário de "nível 2" bloqueiam acesso às rotas restritas?
- [ ] O.S. são gravadas e os lançamentos atrelados caem corretamente no `Financeiro`?
- [ ] O envio de e-mails (`Email.php`) dispara usando a API configurada nas opções?
- [ ] Os scripts JS globais (`assets/js/main.js`) carregam e fecham modais de forma fluida?
