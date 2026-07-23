# Prova de Conceito: Checklist via WhatsApp (Bot)

## Arquivo
`checklist_botImpressaoDispMovelEviaZapSalvaAsin.html`

## Objetivo
Trata-se de um artefato avulso, utilizado como uma interface front-end rica (PoC) para viabilizar o preenchimento de relatórios e checklists em dispositivos móveis.

## Funcionalidades Chave
- **Canvas de Assinatura Digital:** Permite que o técnico e o cliente assinem a tela do celular (touch/stylus) e converta o traço em Base64 para gravação no banco de dados (`os_checklists.assinatura_cliente`).
- **Integração Front-end WhatsApp:** Gera dinamicamente um payload formatado e evoca a API do WhatsApp (ex: `wa.me`) para envio do resumo do serviço diretamente para o cliente em campo.
- **Renderização e Responsividade:** Layout CSS adaptado para ser impresso ou convertido em PDF diretamente da visão Mobile.

## Integração Futura
- Este script deverá ser convertido para uma View oficial (`application/views/os/`) ou um plugin encapsulado na lógica do Controller `Os.php` ou `Contratos.php` nas rotas de `imprimir_checklist`.
