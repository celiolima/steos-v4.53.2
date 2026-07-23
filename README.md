# 🚀 STEOS - Sistema de Gestão ERP

**Versão:** 4.53.2 (Custom Steos Branch)

O **STEOS** é um ERP e sistema de gestão robusto, desenvolvido especialmente para empresas de serviços, locação, contratos de manutenção e assistências técnicas. Construído sobre o framework CodeIgniter (PHP) com banco de dados MySQL, o sistema oferece gestão financeira completa integrada com gateways de pagamento.

## 🛠️ Tecnologias e Setup
- **Backend:** CodeIgniter 3 (PHP)
- **Banco de Dados:** MySQL 8.0
- **Frontend:** Bootstrap 4, jQuery, DataTables, SweetAlert2
- **Infraestrutura:** Docker e Docker Compose

### 🐳 Subindo o Ambiente (Docker)
Para iniciar o projeto localmente com as configurações padrão e orquestração automatizada:
```bash
docker-compose up -d --build
```
O Docker orquestrará os seguintes contêineres:
- `steosMySql`: Servidor de Banco de Dados MySQL
- `steos`: Servidor Web Apache (rodando a aplicação na porta 8080)
- `steosMyadmin`: Interface PHPMyAdmin (disponível na porta 9080)

> **Dica:** As migrações do CodeIgniter (`php index.php tools migrate`) e a verificação de dependências do Composer são executadas automaticamente no boot do contêiner da aplicação web, garantindo que o banco de dados esteja sempre na estrutura mais recente.

## 📖 Documentação Completa e Arquitetura

Para obter detalhes profundos sobre as regras de negócio, estrutura MVC, Integrações (Asaas API V3), Portal do Cliente e o Módulo de Contratos, preparamos um documento estendido:

👉 **[Acessar a Documentação Técnica Detalhada (DOCUMENTACAO_STEOS.md)](./DOCUMENTACAO_STEOS.md)**
