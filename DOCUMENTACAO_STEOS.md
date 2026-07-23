# 📘 Documentação Oficial Completa: STEOS v4.53.2

Este documento serve como a **Bíblia Técnica** do sistema STEOS, descrevendo exaustivamente a arquitetura, integrações, rotas, banco de dados e os submódulos que compõem o ecossistema comercial.

---

## 1. Arquitetura Geral (CodeIgniter MVC)

O STEOS opera em **CodeIgniter 3 (CI3)**, utilizando o padrão **Model-View-Controller (MVC)** de forma rigorosa, isolando regras de negócio e persistência da camada de apresentação (HTML/CSS).

### Fluxo de Requisição Padrão
1. O usuário acessa a URL (ex: `/contratos/adicionar`).
2. O **Router** (Auto-Routing do CI3) despacha a chamada para o Controller correspondente.
3. O **Controller** valida a permissão do usuário via session, carrega os dados usando os Models associados.
4. O **Controller** empacota tudo em um array `$data` e invoca `$this->layout()` ou `$this->load->view()`.
5. A **View** junta o Layout Padrão com a página interativa de formulário, enriquecida por scripts JS globais (`assets/js`).

---

## 2. Docker e Automação de Migrations

O ambiente de desenvolvimento e produção é **containerizado**. O arquivo `docker-compose.yml` orquestra três pilares:
- `steosMySql`: Contêiner do Banco de Dados (MySQL 8.0).
- `steosMyadmin`: Interface visual para o banco (PHPMyAdmin na porta 9080).
- `steos`: Contêiner da Aplicação Web (PHP + Apache na porta 8080).

### 🔄 Rotina Automática de Inicialização (Boot)
Ao invocar `docker-compose up -d`, o contêiner web executa um script de boot que:
1. Aguarda ativamente o MySQL estar saudável.
2. Atualiza dependências via `composer update` (ex: library `faker`).
3. Executa as **Migrations do CI3** via CLI (`php index.php tools migrate`), mantendo as tabelas do sistema sincronizadas estruturalmente.

---

## 3. Integração Poderosa: API Asaas (Gateway V3)

O faturamento automatizado reside no controller `Cobrancas.php` e no controller receptor `Asaas_webhook.php`. O STEOS atua como sub-adquirente.

- **Boletos / Link de Pagamento / Pix**: Emissão nativa interligada aos dados de CPF/CNPJ.
- **Webhook Receptivo**: O script recebe *POSTs* assíncronos da Asaas (pagamento recebido, atraso, estorno) e **atualiza as tabelas do STEOS automaticamente**.

---

## 4. O Módulo de Contratos (Core de Recorrência)

O controller `Contratos.php` e seu model `Contratos_model.php` formam o módulo mais complexo do sistema, focado em **BPO, Locação e Assinatura de Serviços**. Ele amarra diversas frentes operacionais em um único guarda-chuva comercial.

### Lógica de Cálculo e Faturamento
O coração do contrato é o método genérico `recalcularValorTotalContrato($id)`. 
A base de valor de um contrato não é digitada livremente, ela é a **soma rigorosa dos Sistemas/Equipamentos vinculados** (`sistemas_contratos`). 
O usuário informa apenas acréscimos e descontos (em R$ ou %), e o controller recalcula o total final.

### As 7 Dimensões do Contrato (Visualizadas em Nav-Tabs)
1. **Dados Gerais**: Vigência, contratante, valor total calculado (após descontos/acréscimos) e status.
2. **Ordens de Serviço**: Lista todas as intervenções vinculadas a este ID de contrato.
3. **Faturamentos (Asaas)**: Histórico de cobranças recorrentes, faturas e controle de inadimplência.
4. **Vendas**: Compras realizadas sob o amparo do contrato.
5. **Ativos/Sistemas (`sistemas_contratos`)**: Quais tecnologias ou equipamentos o contrato cobre. Inclui a gestão de "Checks" (tarefas/verificações específicas para cada sistema).
6. **Anexos**: Armazenamento seguro de PDFs digitalizados (contrato físico). Exclusão segura em disco e banco.
7. **Checklists Técnicos**: Relatórios periódicos avaliativos vinculados aos "Checks" dos sistemas.

---

## 5. Portal do Cliente (Conecte / Módulo Mine)

O Controller `Mine.php` compõe a "área VIP" restrita ao usuário final. É o módulo **Conecte** (Portal do Cliente), proporcionando transparência total e autoatendimento 24/7.

### Segurança e Autenticação
- **Login Isolado**: O cliente loga via `Mine::login()` utilizando e-mail e senha. A senha é verificada através de `password_verify` (criptografia bcrypt nativa do PHP). 
- **Sessão Diferenciada**: A sessão do cliente é marcada com `isCliente => true`, isolando completamente os acessos do painel administrativo.
- **Recuperação de Senha Segura**: Implementada via geração de **Tokens Alfanuméricos** criptografados. O token possui validade temporal (`data_expiracao`) e é invalidado após o uso (`token_utilizado = true`), garantindo máxima segurança no processo de reset via e-mail.

### Recursos do Cliente Logado
- **Painel Interativo (`Mine::painel`)**: Dashboard inicial apresentando as últimas ordens de serviço e compras.
- **Gestão de Conta (`Mine::editarDados`)**: Permite que o próprio cliente mantenha seus dados cadastrais (telefone, endereço, CNPJ/CPF) e altere sua senha de forma autônoma.
- **Acompanhamento Comercial (`Mine::compras` e `cobrancas`)**: 
  - Listagem paginada (`library pagination`) de histórico de Vendas.
  - Painel financeiro onde o cliente consulta faturas, visualiza status (Pago, Pendente) e realiza pagamentos pendentes diretamente pelo portal.
- **Abertura e Acompanhamento de O.S**: O cliente abre tickets remotamente. Acompanha laudos técnicos e peças trocadas.

---
> Elaborado e documentado através da arquitetura de inteligência _STEOS_.
