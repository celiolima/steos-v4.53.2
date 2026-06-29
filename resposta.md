ok, vamos implementar o checklist, mais primeiro temos que ajustar alguns pontos na os editar, pois é a ordem de serviço que vai gerar o checklist. analise a tebela  os, já deve existir uma flag manutPreventiva, se não existir criaremos, pois essa flag ativará dinamicamente o botão gerar checklist que ficará localizado alinhado com os botões faturar,visualizar, imprimir, whatsapp, email e adicionar os,  esse checklist será armazenado em uma nova tabela checklist e vinculada a os, e será listada na aba checklist do contrato, com as colunas , os, data, tecnico e botões para visualiza, imprimir, gerar pdf, editar e excluir.

a ordem de serviço "manuteção preventiva"  será gerada pelo contrato. Ao ativarmos o contrato,seremos obridaos a informar a data final do contrato. na aba geral do contrato,  sugirá um novo campo imput, dia da primeira visita e um botão gerar ordem de serviço  manutenção preventiva, e quando esse botão for clicado. 

1 - verificar se a data atual é menor que a data final do contrato e caucular quantas visitas preventivas mensais serão geradas.
exeplo: se falta 6 meses para o final do contrato serão criadas 6 visitas preventivas mensais, sendo a primeira no dia  indicado no campo dia da primeira visita e assim sucessivamente a cada mes.
2 - as ordens de serviços serão criadas com as informaçoes setadas da seguite forma:

nome do cilent = cliete do contrato
contrato vinculado = contrato selecionado
oparador = "operador logado"
tecnico= ""
status = A Sair|Aguard Conclusão
data inicial = data do campo dia da primeira visita
data final = data do campo dia da primeira visita
local = externo
tipo = contrato
observação = "manutenção preventiva"

pode criar um plano para implementarmos?




