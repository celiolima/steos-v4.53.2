# Arquitetura Geral do Sistema Steos

Este documento ilustra a arquitetura fundamental do sistema Steos, baseado no **CodeIgniter 3**. O sistema utiliza o padrão arquitetural MVC (Model-View-Controller) fortemente acoplado.

## Diagrama MVC e Fluxo de Execução

```mermaid
graph TD
    %% Usuário e Requisição
    User((Usuário))
    Browser[Navegador / Cliente]
    Router{Roteador CI3<br>routes.php}
    
    User -- Interage --> Browser
    Browser -- "HTTP Request (GET/POST)" --> Router
    
    %% Camada Controller
    subgraph Controllers [Camada Controllers]
        MainCtrl[Steos.php<br>Controller Principal]
        OutrosCtrls[Os.php, Clientes.php, Vendas.php, etc.]
        MainCtrl -.-> OutrosCtrls
    end
    
    Router -- Encaminha para --> Controllers
    
    %% Camada Model
    subgraph Models [Camada Models]
        MainModel[Steos_model.php]
        OutrosModels[Os_model.php, Clientes_model.php, etc.]
    end
    
    Controllers -- "$this->load->model()<br>Delega operações CRUD" --> Models
    Models -- Retorna Arrays/Objetos --> Controllers
    
    %% Banco de Dados
    Database[(Banco de Dados MySQL)]
    Models -- "Query Builder<br>($this->db)" --> Database
    Database -- "Resultados (Result/Row)" --> Models
    
    %% Camada Views
    subgraph Views [Camada Views]
        Tema(Tema / Layout Base)
        Telas[Telas Específicas<br>Views Dinâmicas]
        Tema -.-> Telas
    end
    
    Controllers -- "$this->load->view()<br>Envia Array $data" --> Views
    Views -- "Renderiza HTML + JS" --> Browser
```

## Descrição das Camadas
- **Controllers:** Orquestradores do fluxo. Eles recebem requisições, validam dados (usando `form_validation`), solicitam informações aos Models e processam a lógica de negócio antes de compilar os dados para a exibição nas Views.
- **Models:** Exclusivos para a manipulação do banco de dados (MySQL). Eles padronizam métodos como `add()`, `edit()`, `delete()`, e `getById()`.
- **Views:** Responsáveis pela UI e UX. Utilizam templates e recebem variáveis processadas pelos Controllers. Possuem também scripts JavaScript embarcados ou referenciados para requisições assíncronas (Ajax).
