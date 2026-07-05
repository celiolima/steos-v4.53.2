# 🌐 Documentação e Análise Completa: Módulo Conecte (Portal do Cliente)

> **Projeto:** STEOS v4.53.2 (Sistema de Gestão de Ordens de Serviço)  
> **Módulo:** Portal do Cliente (Conecte / Mine)  
> **Arquivo Exclusivo de Especificação:** [portal_do_cliente_conecte.md](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/.specs/portal_do_cliente_conecte.md)

---

## 1. Visão Geral do Módulo

O **Portal do Cliente** (popularmente denominado na arquitetura como **Módulo Conecte** ou **Mine**) é a interface de atendimento *self-service* do STEOS. Ele atua como a ponte direta de comunicação, transparência e gestão financeira entre a prestadora de serviços (emitente) e os seus clientes finais.

Através de uma interface limpa, responsiva e otimizada (utilizando Bootstrap, Boxicons, FontAwesome e componentes modernos), o cliente possui autonomia para:
- Acompanhar em tempo real o status e o histórico completo de suas **Ordens de Serviço (O.S.)**;
- **Abrir novas Ordens de Serviço** diretamente pelo portal, descrevendo defeitos e necessidades;
- Visualizar laudos técnicos, peças utilizadas, serviços executados e **baixar ou enviar anexos** (fotos, documentos, PDFs);
- Consultar o histórico de **Compras e Vendas** de produtos;
- Gerenciar suas **Cobranças e Faturas**, com integração nativa para pagamento via **Boleto Bancário / Link Gerencianet** ou pagamento instantâneo via **QR Code Pix Estático** gerado em tela;
- Gerenciar seus dados cadastrais e senha de acesso com total segurança (tokens temporários por e-mail, proteção CSRF e validação em tempo real).

---

## 2. Arquitetura e Fluxo de Dados

A arquitetura do Portal do Cliente segue rigorosamente o padrão **MVC (Model-View-Controller)** do CodeIgniter 3, com uma separação clara entre a lógica de controle, acesso aos dados e camada de apresentação:

```mermaid
graph TD
    Client[👤 Cliente / Navegador] -->|HTTP / HTTPS| Routes[🔀 Rotas / index.php/mine]
    
    subgroup Controller
        Routes -->|Requisições| MineCtrl[🎮 Controller: Mine.php]
    end
    
    subgroup Model & Database
        MineCtrl -->|Consultas e Operações| ConecteModel[🗄️ Model: Conecte_model.php]
        ConecteModel <-->|SQL Query Builder| DB[(🐬 Banco de Dados MySQL)]
        ConecteModel -->|StaticPayload| PixQR[📱 Gerador de QR Code Pix]
    end
    
    subgroup Views & UI
        MineCtrl -->|Renderização| Views[🖥️ Views: application/views/conecte/]
        Views -->|Template Master| Tpl[template.php]
        Views -->|Dashboard| Painel[painel.php]
        Views -->|OS & Abertura| OsViews[os.php / visualizar_os.php / adicionarOs.php]
        Views -->|Financeiro| CobViews[cobrancas.php / compras.php]
        Views -->|Perfil & Auth| AuthViews[login.php / conta.php / resetar_senha.php]
    end
    
    subgroup Frontend Assets & Gateways
        Views <-->|Validação e UX| JSAssets[📦 JS: Trumbowyg / Validate / SweetAlert / Mask]
        MineCtrl <-->|API de Boleto / Link| Gerencianet[💳 Gateway Gerencianet / API]
    end
```

---

## 3. Análise Detalhada do Controller: `Mine.php`

O arquivo [application/controllers/Mine.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/controllers/Mine.php) é o maestro de todo o Portal do Cliente. Com mais de **1.000 linhas de código**, ele gerencia segurança, sessões, regras de negócio, notificações e chamadas de renderização.

### 🛡️ Mecanismos de Segurança e Controle de Acesso
- **Verificação de Sessão (`cliente_id`)**: Em todos os endpoints protegidos, o controller valida se a sessão `cliente_id` está ativa no `$this->session->userdata()`. Se não estiver, o cliente é imediatamente redirecionado para `mine/login`.
- **Proteção CSRF**: Todas as submissões de formulários e requisições AJAX são protegidas por tokens CSRF ativados no CodeIgniter (`csrf_token_name` / `csrf_cookie_name`).
- **Validações Nativas**: Contém métodos dedicados e rigorosos para validação de documentos: `validarCPF($cpf)` e `validarCNPJ($cnpj)`, impedindo fraudes ou dados inconsistentes.

### 🔌 Mapeamento Completo de Funcionalidades e Métodos

| Método / Endpoint | Tipo | Descrição e Lógica Implementada |
| :--- | :---: | :--- |
| `index()` / `login()` | **Auth** | Exibe a tela de login do portal ([login.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/login.php)) e processa a autenticação via e-mail e senha, invocando `check_credentials()`. |
| `sair()` | **Auth** | Destrói a sessão atual do cliente e redireciona para a página de login. |
| `resetarSenha()` / `senhaSalvar()` | **Auth** | Gerencia o fluxo de esquecimento de senha. O método `gerarTokenResetarSenha()` cria um token seguro com data de expiração no banco e envia por e-mail através de `enviarRecuperarSenha()`. O cliente valida via `verifyTokenSenha()` ou digitação em `tokenManual()`. |
| `cadastrar()` | **Perfil** | Permite o auto-cadastro de novos clientes no portal ([cadastrar.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/cadastrar.php)). Valida CPF/CNPJ, e-mail duplicado e dispara e-mail de boas-vindas via `enviarEmailBoasVindas()`. |
| `painel()` | **UI** | Carrega o dashboard principal ([painel.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/painel.php)), buscando no model as últimas 10 O.S., últimas compras e contagens gerais da conta. |
| `conta()` / `editarDados()` | **Perfil** | Carrega e processa a atualização dos dados cadastrais, telefone, endereço e senha do próprio cliente ([conta.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/conta.php)). |
| `os()` / `visualizarOs($id)` | **O.S.** | Lista todas as ordens de serviço do cliente e exibe a visualização detalhada ([visualizar_os.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/visualizar_os.php)), calculando valores, garantias e gerando o QR Code Pix. |
| `detalhesOs($id)` | **O.S.** | Exibe a interface em abas detalhada da O.S. ([detalhes_os.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/detalhes_os.php)), mostrando produtos, serviços, laudo técnico e gerenciando a aba de anexos. |
| `adicionarOs()` | **O.S.** | **Abertura Self-Service:** Permite que o cliente abra uma nova O.S. direto pelo portal ([adicionarOs.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/adicionarOs.php)). Ao salvar, dispara notificação automática por e-mail para a equipe técnica via `enviarEmailTecnicoNotificaClienteNovo()`. |
| `imprimirOs($id)` | **O.S.** | Renderiza a versão limpa e formatada para impressão/PDF de uma O.S. ([imprimirOs.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/imprimirOs.php)). |
| `minha_ordem_de_servico($y, $when)`| **Acesso Externo** | **Recurso Arquitetural Brilhante:** Permite acesso à O.S. via link externo sem necessidade de login! Utiliza uma função linear de obuscamento (`y = 7653 * ID + 44023` para envio e `x = (y - 44023) / 7653` para recepção) e validação de timestamp. |
| `downloadanexo($id)` | **O.S.** | Controla o download seguro de arquivos e anexos vinculados às ordens de serviço do cliente. |
| `compras()` / `visualizarCompra($id)`| **Vendas** | Lista o histórico de vendas/produtos adquiridos e exibe detalhes ([visualizar_compra.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/visualizar_compra.php)) e impressão ([imprimirVenda.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/imprimirVenda.php)). |
| `cobrancas()` | **Financeiro**| Lista todas as faturas e cobranças vinculadas às O.S. ou Vendas do cliente ([cobrancas.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte/cobrancas.php)). |
| `gerarPagamentoGerencianet...()` | **Gateways** | Métodos de integração bancária (`gerarPagamentoGerencianetBoleto` / `gerarPagamentoGerencianetLink`) que comunicam com a API da Gerencianet para emitir boletos e links de pagamento online em tempo real. |

---

## 4. Análise Detalhada do Model: `Conecte_model.php`

O arquivo [application/models/Conecte_model.php](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/models/Conecte_model.php) é responsável pela persistência, paginação e inteligência de dados do portal.

### 🔍 Principais Consultas e Lógica de Banco
- **Isolamento por Cliente (`$cliente`)**: Todas as queries de listagem (`getLastOs`, `getLastCompras`, `getCompras`, `getCobrancas`, `getOs`, `count`) injetam obrigatoriamente a cláusula `$this->db->where('clientes_id', $cliente)`, garantindo que um cliente jamais tenha acesso aos dados de outro.
- **Joins Estratégicos**: O método `getById($id)` realiza um `JOIN` múltiplo unindo a tabela `os`, `clientes`, `usuarios` (técnico responsável) e `garantias` (para obter o termo e tempo de garantia).

### 📱 Geração Nativa de QR Code Pix Estático (`getQrCode`)
Um dos recursos mais modernos do model é a geração em tempo real de QR Code para pagamento instantâneo via Pix:
```php
public function getQrCode($id, $pixKey, $emitente) {
    // 1. Calcula o valor total da O.S. (considerando descontos ou soma de produtos + serviços)
    $result = $this->valorTotalOS($id);
    $amount = $result['valor_desconto'] != 0 ? round(floatval($result['valor_desconto']), 2) : round(floatval($result['totalServico'] + $result['totalProdutos']), 2);

    // 2. Utiliza a biblioteca StaticPayload para montar o payload EMVCo (Padrão BACEN)
    $pix = (new StaticPayload())
        ->setAmount($amount)
        ->setTid($id)
        ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
        ->setPixKey(getPixKeyType($pixKey), $pixKey)
        ->setMerchantName($emitente->nome)
        ->setMerchantCity($emitente->cidade);

    return $pix->getQRCode(); // Retorna imagem base64 / string do QR Code
}
```

---

## 5. Análise Exaustiva das Views: `application/views/conecte/`

A pasta [application/views/conecte/](file:///e:/DEV/EMISSOR%20DE%20NOTAS/STEOS/application/views/conecte) abriga toda a camada visual do Portal do Cliente, composta por 18 arquivos PHP e 1 subpasta de e-mails.

| Arquivo da View | Função e Lógica Visual Implementada |
| :--- | :--- |
| **`template.php`** | **Layout Mestre:** Define o cabeçalho HTML, importação de assets (Bootstrap, Boxicons, FontAwesome, SweetAlert), barra de navegação superior com perfil do usuário e menu lateral (`#sidebar`) com logo branca do STEOS e alternador de modo visual (lua/sol). |
| **`painel.php`** | **Dashboard / Home:** Exibe 4 cards interativos no topo (O.S., Compras, Cobranças, Minha Conta). Abaixo, apresenta duas tabelas de resumo (Últimas O.S. e Últimas Compras). Implementa lógica visual com badges para **Vencimento de Garantia** (Verde `#4d9c79` para válida, Vermelho `#f24c6f` para vencida) e badges coloridas por status de O.S. |
| **`os.php`** | **Listagem de O.S.:** Tabela paginada com histórico completo de ordens de serviço do cliente, filtros por status/data e botões de ação para visualizar ou imprimir. |
| **`visualizar_os.php`** | **Fatura / Resumo da O.S.:** Apresentação elegante estilo "Invoice", mostrando logo do emitente, dados da O.S., tabelas de produtos e serviços, cálculo de subtotais e descontos. Se o status for `Finalizado` ou `Aprovado`, renderiza automaticamente o **QR Code Pix** e a Chave Pix na tela. Contém modal para visualização de anexos e script de auto-impressão em popup. |
| **`detalhes_os.php`** | **Visão em Abas da O.S.:** Interface organizada em 4 abas (`nav-tabs`): *1. Detalhes da O.S.* (com editores Trumbowyg em modo somente leitura mostrando defeito, descrição e laudo técnico); *2. Produtos*; *3. Serviços*; e *4. Anexos* (listagem de fotos e arquivos com botão de download). |
| **`adicionarOs.php`** | **Abertura Self-Service de O.S.:** Formulário interativo onde o cliente inicia o atendimento. Utiliza o editor rico **Trumbowyg** (em português) para os campos de Descrição, Defeito e Observações. Conta com validação no frontend (`jquery.validate.js`). |
| **`imprimirOs.php`** | **Relatório de Impressão de O.S.:** Layout limpo, desprovido de menus ou sidebars, formatado especificamente para impressão ou geração de PDF em folha A4 pelo cliente. |
| **`compras.php`** | **Listagem de Compras:** Tabela paginada listando produtos adquiridos pelo cliente, indicando se já foram faturados e o status da garantia. |
| **`visualizar_compra.php`**| **Detalhes da Compra:** Exibe os produtos de uma venda específica, valores unitários, totais e situação financeira. |
| **`imprimirVenda.php`** | **Relatório de Impressão de Venda:** Layout de impressão limpo para recibos de compras. |
| **`cobrancas.php`** | **Painel Financeiro:** Tabela completa de faturas e boletos. Exibe data de vencimento, referência (link para O.S. ou Venda), status na Gerencianet e valor em reais. Oferece botões para visualizar boleto com código de barras (`<i class="bx bx-barcode">`), atualizar status e reenviar por e-mail. |
| **`conta.php`** | **Minha Conta:** Exibição dos dados cadastrais do cliente (nome, documento, telefone, celular, e-mail, endereço completo e data de cadastro). |
| **`editar_dados.php`** | **Edição de Perfil:** Formulário que permite ao cliente alterar seus próprios dados cadastrais e redefinir sua senha de acesso. |
| **`login.php`** | **Tela de Login:** Interface limpa e centralizada para acesso ao portal, com campos para e-mail/senha, link de "Esqueci minha senha" e botão para novo cadastro. |
| **`cadastrar.php`** | **Tela de Registro:** Formulário público de auto-cadastro para novos clientes, com validação de força de senha e formatação de CPF/CNPJ. |
| **`resetar_senha.php`** | **Esqueci a Senha (Etapa 1):** Formulário onde o cliente informa o e-mail para solicitar o token de redefinição. |
| **`token_digita.php`** | **Validar Token (Etapa 2):** Tela para inserção do token numérico/alfanumérico recebido no e-mail. |
| **`nova_senha.php`** / `clientenovasenha.php`| **Criar Nova Senha (Etapa 3):** Formulário final para digitação e confirmação da nova senha segura. |
| **`emails/`** (Subpasta)| **Templates de E-mail:** Contém os layouts em HTML formatado para envio de notificações do portal (boas-vindas, token de senha, atualização de O.S., etc.). |

---

## 6. Ecossistema JavaScript e Interatividade (`assets/js/`)

A experiência de uso no Portal do Cliente é altamente interativa e dinâmica, sustentada por bibliotecas e scripts especializados:

1. **Trumbowyg WYSIWYG (`trumbowyg.js` + `pt_br.js`)**:
   - Aplicado aos campos de texto em `adicionarOs.php` e `detalhes_os.php`.
   - Permite que o cliente formate textos com negrito, itálico, listas numeradas e marcações semânticas ao descrever defeitos ou ler laudos técnicos.
2. **jQuery Validate (`jquery.validate.js` / `validate.js`)**:
   - Realiza a validação em tempo real no frontend de formulários críticos (abertura de O.S., login, cadastro e edição de perfil), exibindo mensagens claras em português antes de enviar ao servidor.
3. **jQuery Mask & MaskMoney (`jquery.mask.min.js` / `maskmoney.js`)**:
   - Garante que campos de CPF (`000.000.000-00`), CNPJ (`00.000.000/0000-00`), CEP (`00000-000`), Celular (`(00) 00000-0000`) e valores monetários sejam preenchidos na formatação correta.
4. **SweetAlert / SweetAlert2 (`sweetalert.min.js`)**:
   - Substitui os alertas nativos do navegador por modais elegantes e animados para feedback de sucesso (ex: "O.S. cadastrada com sucesso!"), erro de login ou confirmações de exclusão.
5. **Funções Globais (`funcoesGlobal.js` + `csrf.js`)**:
   - Injeta automaticamente os cabeçalhos e tokens de proteção CSRF em todas as requisições AJAX, mantendo a navegação do cliente blindada contra ataques de falsificação de requisição.

---

## 7. Resumo: O que é Apresentado ao Cliente no Portal?

O Portal do Cliente transforma a experiência do usuário final, entregando transparência em 360 graus:

```
+-----------------------------------------------------------------------+
| 🌐 PORTAL DO CLIENTE - STEOS (ÁREA SELF-SERVICE)                      |
+-----------------------------------------------------------------------+
| 📊 DASHBOARD (PAINEL):                                                |
|  • Cards com contagem instantânea de O.S., Compras e Cobranças.      |
|  • Alertas visuais em tempo real sobre Vencimento de Garantia.        |
|                                                                       |
| 🛠️ ORDENS DE SERVIÇO:                                                 |
|  • Abertura autônoma de O.S. (com descrição rica e defeito).          |
|  • Acompanhamento de status (Aberto, Em Andamento, Aguardando Peças). |
|  • Visualização transparente do Laudo Técnico do especialista.        |
|  • Acesso completo ao checklist, peças utilizadas e valores.          |
|  • Download e visualização de fotos/anexos do equipamento.            |
|                                                                       |
| 💳 GESTÃO FINANCEIRA E PAGAMENTOS:                                    |
|  • QR Code Pix na tela para pagamento imediato de O.S. finalizadas!   |
|  • Listagem de faturas e boletos com link direto para Gerencianet.    |
|  • Impressão de relatórios e recibos em PDF / A4 com 1 clique.        |
+-----------------------------------------------------------------------+
```

---

> [!TIP]
> **Como Acessar o Portal do Cliente:**  
> O portal está disponível e funcional no seu ambiente local! Para testar a interface de login e navegar como um cliente no seu navegador, basta clicar no link abaixo:  
> 🌐 **[Acessar Portal do Cliente Conecte (STEOS)](http://192.168.1.147:8080/index.php/mine)**
