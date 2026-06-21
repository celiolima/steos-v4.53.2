# SteosSteos v4.53.2

Bem-vindo ao **SteosSteos**, uma solução de Sistema de Ordem de Serviço (Steos) atualizada e superpoderosa. Este projeto combina a base oficial e mais recente do Steos v4.53.2 com os módulos exclusivos criados sob medida para o "Steos".

## Visão Geral do Sistema
O **SteosSteos** foi desenhado para oferecer a estabilidade da versão mais moderna do framework CodeIgniter e Bootstrap (trazidos na v4.53.2 oficial), enquanto mantém a inteligência de negócios customizada:

- **Controle de Comissões e Relatórios Avançados:** Comissões por Venda, Serviço e Impressões parametrizadas.
- **Módulo de Assinaturas (Recorrência):** Controle de contratos e serviços recorrentes.
- **Gateway de Pagamento Asaas Integrado:** Geração inteligente de cobranças, links de pagamentos e Pix nativo.
- **Layout Modernizado:** Interfaces enxutas e focadas no usuário.

## Tecnologias e Requisitos
* **Linguagem:** PHP 8+
* **Framework:** CodeIgniter 3
* **Frontend:** Bootstrap 4, jQuery
* **Banco de Dados:** MySQL / MariaDB (Driver mysqli)
* **API:** Asaas REST API

## Como Rodar Localmente (Desenvolvimento)

1. **Baixe o Projeto:** Extraia a pasta `steosSteos v4.53.2` no diretório raiz do seu servidor web (Ex: `htdocs` no XAMPP ou `www` no WAMP).
2. **Configuração do Banco de Dados:**
   - Crie um banco de dados no MySQL (ex: `steos`).
   - Importe o script `banco.sql` na base criada para obter toda a estrutura de tabelas.
3. **Variáveis de Ambiente:**
   - Acesse o arquivo `application/config/database.php` ou crie um arquivo `.env` para apontar as credenciais do seu banco de dados local (`username`, `password`, `database`).
4. **Base URL:**
   - Se estiver usando em localhost, garanta que a URL base no arquivo `application/config/config.php` esteja apontando corretamente (ex: `http://localhost/steosSteos/`).

## Configurando o Gateway Asaas

O sistema está blindado e programado para gerar cobranças integradas via Asaas.
1. Faça login com uma conta nível Administrador.
2. Acesse: **Configurações > Financeiro**.
3. Na aba **Asaas**, insira sua **Chave de API (Access Token)** oficial do Asaas (Sandbox para testes ou Produção para valer).
4. O sistema irá automaticamente ler a chave e permitir a emissão de Pix e Boletos nas Ordens de Serviço.

## Estrutura de Customizações
As customizações exclusivas deste projeto em relação ao Steos oficial encontram-se principalmente nos arquivos:
- `application/controllers/Assinaturas.php` (Fluxos de Recorrência)
- `application/controllers/Vendas.php` (Proteção e lógicas do Asaas injetadas)
- `application/controllers/Relatorios.php` (Busca de comissões nativa `comissaoOs` e `comissaoVendas`)
- `application/models/Os_model.php` (Função extra `getOsComissao` para cruzamento de dados)

## Notas de Versão
- **Versão:** 4.53.2 (Custom Steos Branch)
- **Status da Migração:** Concluída com sucesso, com testes e depurações completas. Todos os métodos de código espaguete/antigos foram adaptados para o novo padrão CodeIgniter.

---
*Documentação gerada após a limpeza e migração do sistema legado para a infraestrutura moderna.*
