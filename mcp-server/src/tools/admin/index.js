import { z } from 'zod';
import { logToolExecution } from '../../lib/authMiddleware.js';
import { toSteosDateBR } from '../../lib/steosClient.js';

const positiveInt = (description) =>
  z.coerce.number().int().positive().describe(description);

const nonNegativeInt = (description) =>
  z.coerce.number().int().min(0).describe(description);

const paginationShape = {
  page: nonNegativeInt('Página (base 0, padrão 0)').optional(),
  perPage: z.coerce
    .number()
    .int()
    .min(1)
    .max(100)
    .describe('Itens por página (1 a 100, padrão 20)')
    .optional(),
};

const READ_ONLY_ANNOTATIONS = {
  readOnlyHint: true,
  destructiveHint: false,
  idempotentHint: true,
  openWorldHint: false,
};

const WRITE_ANNOTATIONS = {
  readOnlyHint: false,
  destructiveHint: false,
  idempotentHint: false,
  openWorldHint: false,
};

const FISCAL_WRITE_ANNOTATIONS = {
  readOnlyHint: false,
  destructiveHint: true,
  idempotentHint: false,
  openWorldHint: true,
};

export function formatToolSuccess(data) {
  return {
    content: [
      {
        type: 'text',
        text: JSON.stringify(data, null, 2),
      },
    ],
  };
}

export function formatToolError(err) {
  const payload = {
    error: true,
    message: err instanceof Error ? err.message : String(err || 'Erro desconhecido'),
  };

  if (err && typeof err === 'object') {
    if (err.status !== undefined) {
      payload.status = err.status;
    }
    if (err.payload !== undefined && err.payload !== null) {
      payload.details = err.payload;
    }
  }

  return {
    content: [
      {
        type: 'text',
        text: JSON.stringify(payload, null, 2),
      },
    ],
    isError: true,
  };
}

function compactObject(obj) {
  const result = {};
  for (const [key, value] of Object.entries(obj)) {
    if (value !== undefined) {
      result[key] = value;
    }
  }
  return result;
}

function withAdminTool(toolName, handler) {
  return async (args = {}) => {
    const startedAt = Date.now();
    try {
      const data = await handler(args ?? {});
      logToolExecution({
        scope: 'admin',
        tool: toolName,
        status: 'ok',
        durationMs: Date.now() - startedAt,
      });
      return formatToolSuccess(data);
    } catch (err) {
      logToolExecution({
        scope: 'admin',
        tool: toolName,
        status: 'error',
        durationMs: Date.now() - startedAt,
        detail: err instanceof Error ? err.message : String(err),
      });
      return formatToolError(err);
    }
  };
}

export function registerAdminTools(server, { steosAdminClient, config, overrideToken } = {}) {
  const callApi = (opts) =>
    steosAdminClient.request({
      ...opts,
      overrideToken,
    });

  // ============================================================================
  // ONDA 1 — LEITURA (23 tools, sempre registradas)
  // ============================================================================

  server.registerTool(
    'steos_dashboard_resumo',
    {
      title: 'Resumo do Dashboard STEOS',
      description:
        'Retorna contadores globais (O.S., clientes, produtos, serviços, contratos, cobranças, etc.), O.S. abertas/em andamento e produtos com estoque baixo.',
      inputSchema: {},
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_dashboard_resumo', () => callApi({ method: 'GET', path: '/' }))
  );

  server.registerTool(
    'steos_calendario_os',
    {
      title: 'Calendário de Ordens de Serviço',
      description: 'Lista eventos de O.S. para calendário com filtros opcionais de período, status e técnicos.',
      inputSchema: {
        start: z.string().describe('Data inicial YYYY-MM-DD (opcional)').optional(),
        end: z.string().describe('Data final YYYY-MM-DD (opcional)').optional(),
        status: z.string().describe('Status da O.S. (opcional)').optional(),
        tecnicos: z.string().describe('Filtro de técnicos (opcional)').optional(),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_calendario_os', (args) =>
      callApi({
        method: 'GET',
        path: 'calendario',
        query: {
          start: args.start,
          end: args.end,
          status: args.status,
          tecnicos: args.tecnicos,
        },
      })
    )
  );

  server.registerTool(
    'steos_emitente_info',
    {
      title: 'Dados do Emitente',
      description: 'Retorna o nome da aplicação e os dados cadastrais do emitente do STEOS.',
      inputSchema: {},
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_emitente_info', () => callApi({ method: 'GET', path: 'emitente' }))
  );

  server.registerTool(
    'steos_listar_clientes',
    {
      title: 'Listar Clientes',
      description: 'Lista clientes cadastrados com suporte a busca por texto e paginação.',
      inputSchema: {
        search: z.string().describe('Termo de busca (nome, documento, e-mail, etc.)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_clientes', (args) =>
      callApi({
        method: 'GET',
        path: 'clientes',
        query: {
          search: args.search,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_detalhar_cliente',
    {
      title: 'Detalhar Cliente',
      description: 'Obtém detalhes cadastrais completos de um cliente e suas Ordens de Serviço.',
      inputSchema: {
        clienteId: positiveInt('ID numérico do cliente'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_detalhar_cliente', (args) =>
      callApi({
        method: 'GET',
        path: `clientes/${args.clienteId}`,
      })
    )
  );

  server.registerTool(
    'steos_listar_produtos',
    {
      title: 'Listar Produtos',
      description: 'Lista produtos do catálogo com suporte a busca e paginação.',
      inputSchema: {
        search: z.string().describe('Termo de busca (descrição ou código de barras)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_produtos', (args) =>
      callApi({
        method: 'GET',
        path: 'produtos',
        query: {
          search: args.search,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_listar_servicos',
    {
      title: 'Listar Serviços',
      description: 'Lista serviços cadastrados com suporte a busca e paginação.',
      inputSchema: {
        search: z.string().describe('Termo de busca por nome/descrição do serviço').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_servicos', (args) =>
      callApi({
        method: 'GET',
        path: 'servicos',
        query: {
          search: args.search,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_listar_os',
    {
      title: 'Listar Ordens de Serviço',
      description:
        'Lista Ordens de Serviço com filtros de pesquisa, status, datas de abertura/fechamento, técnico, local, tipo e manutenção preventiva.',
      inputSchema: {
        search: z.string().describe('Texto de busca (cliente, descrição, número da O.S.)').optional(),
        status: z.string().describe('Status da O.S. (ex.: Aberto, Em Andamento, Finalizado, Faturado)').optional(),
        from: z.string().describe('Data inicial de abertura (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        to: z.string().describe('Data final de abertura (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        data_fech_de: z.string().describe('Data inicial de fechamento (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        data_fech_ate: z.string().describe('Data final de fechamento (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        tecnico: z.string().describe('Nome do técnico responsável').optional(),
        local: z.string().describe('Local de atendimento da O.S.').optional(),
        tipo: z.string().describe('Tipo da O.S. (ex.: Avulso, Contrato)').optional(),
        manPrevnt: z.string().describe('Filtro de manutenção preventiva ("1" para sim, "0" para não)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_os', (args) =>
      callApi({
        method: 'GET',
        path: 'os',
        query: {
          search: args.search,
          status: args.status,
          from: args.from,
          to: args.to,
          data_fech_de: args.data_fech_de,
          data_fech_ate: args.data_fech_ate,
          tecnico: args.tecnico,
          local: args.local,
          tipo: args.tipo,
          manPrevnt: args.manPrevnt,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_detalhar_os',
    {
      title: 'Detalhar Ordem de Serviço',
      description:
        'Obtém detalhes completos de uma Ordem de Serviço (produtos, serviços, anotações, anexos, técnicos, equipamentos, cobranças e checklists).',
      inputSchema: {
        osId: positiveInt('ID numérico da Ordem de Serviço'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_detalhar_os', (args) =>
      callApi({
        method: 'GET',
        path: `os/${args.osId}`,
      })
    )
  );

  server.registerTool(
    'steos_listar_checklists_os',
    {
      title: 'Listar Checklists da O.S.',
      description: 'Lista os checklists digitais e respectivos itens preenchidos vinculados a uma Ordem de Serviço.',
      inputSchema: {
        osId: positiveInt('ID numérico da Ordem de Serviço'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_checklists_os', (args) =>
      callApi({
        method: 'GET',
        path: `os/${args.osId}/checklists`,
      })
    )
  );

  server.registerTool(
    'steos_listar_contratos',
    {
      title: 'Listar Contratos',
      description: 'Lista contratos cadastrados com filtros de busca, status e cliente.',
      inputSchema: {
        search: z.string().describe('Termo de pesquisa do contrato').optional(),
        status: z.string().describe('Status do contrato (ex.: Ativo, Encerrado)').optional(),
        clientes_id: positiveInt('Filtrar por ID do cliente').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_contratos', (args) =>
      callApi({
        method: 'GET',
        path: 'contratos',
        query: {
          search: args.search,
          status: args.status,
          clientes_id: args.clientes_id,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_detalhar_contrato',
    {
      title: 'Detalhar Contrato',
      description:
        'Obtém detalhes de um contrato incluindo sistemas, checks, Ordens de Serviço, vendas, cobranças e anexos.',
      inputSchema: {
        contratoId: positiveInt('ID numérico do contrato'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_detalhar_contrato', (args) =>
      callApi({
        method: 'GET',
        path: `contratos/${args.contratoId}`,
      })
    )
  );

  server.registerTool(
    'steos_listar_cobrancas',
    {
      title: 'Listar Cobranças',
      description: 'Lista cobranças com filtros por busca, status, tipo/meio, O.S., venda e período de vencimento.',
      inputSchema: {
        search: z.string().describe('Nome ou documento do cliente').optional(),
        status: z.string().describe('Status da cobrança (ex.: PENDING, RECEIVED, CONFIRMED, OVERDUE)').optional(),
        tipo: z.string().describe('Meio de pagamento (ex.: BOLETO, PIX, CREDIT_CARD)').optional(),
        os_id: positiveInt('Filtrar por ID da O.S.').optional(),
        vendas_id: positiveInt('Filtrar por ID da venda').optional(),
        data_de: z.string().describe('Vencimento inicial (YYYY-MM-DD)').optional(),
        data_ate: z.string().describe('Vencimento final (YYYY-MM-DD)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_cobrancas', (args) =>
      callApi({
        method: 'GET',
        path: 'cobrancas',
        query: {
          search: args.search,
          status: args.status,
          tipo: args.tipo,
          os_id: args.os_id,
          vendas_id: args.vendas_id,
          data_de: args.data_de,
          data_ate: args.data_ate,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_obter_pix_cobranca',
    {
      title: 'Obter QR Code Pix da Cobrança',
      description: 'Obtém o QR Code Pix e payload Copia e Cola no Asaas para uma cobrança existente.',
      inputSchema: {
        cobrancaId: positiveInt('ID numérico da cobrança no STEOS'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_obter_pix_cobranca', (args) =>
      callApi({
        method: 'GET',
        path: `cobrancas/${args.cobrancaId}/pix`,
      })
    )
  );

  server.registerTool(
    'steos_listar_nfse',
    {
      title: 'Listar NFS-e Emitidas',
      description: 'Lista Notas Fiscais de Serviço Eletrônicas vinculadas a Ordens de Serviço com filtros opcionais.',
      inputSchema: {
        search: z.string().describe('Nome do cliente, CPF/CNPJ, número da NFS-e ou ID da O.S.').optional(),
        status: z.string().describe('Status Asaas da NFS-e (ex.: AUTHORIZED, SCHEDULED, CANCELED)').optional(),
        data_de: z.string().describe('Data inicial da O.S. (YYYY-MM-DD)').optional(),
        data_ate: z.string().describe('Data final da O.S. (YYYY-MM-DD)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_nfse', (args) =>
      callApi({
        method: 'GET',
        path: 'nfse',
        query: {
          search: args.search,
          status: args.status,
          data_de: args.data_de,
          data_ate: args.data_ate,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_consultar_nfse',
    {
      title: 'Consultar Status Atualizado da NFS-e',
      description: 'Consulta o status atualizado da NFS-e no Asaas para uma O.S. e sincroniza os dados locais.',
      inputSchema: {
        osId: positiveInt('ID numérico da Ordem de Serviço com NFS-e vinculada'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_consultar_nfse', (args) =>
      callApi({
        method: 'GET',
        path: `nfse/consultar/${args.osId}`,
      })
    )
  );

  server.registerTool(
    'steos_listar_servicos_nfse',
    {
      title: 'Listar Serviços NFS-e e Códigos Fiscais',
      description:
        'Lista serviços fiscais configurados em servicos_nfse e a tabela oficial de operationIndicatorCodes do Asaas/Portal Nacional.',
      inputSchema: {},
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_servicos_nfse', () =>
      callApi({
        method: 'GET',
        path: 'nfse/servicos',
      })
    )
  );

  server.registerTool(
    'steos_listar_financeiro',
    {
      title: 'Listar Lançamentos Financeiros',
      description: 'Lista receitas e despesas financeiras com filtros de tipo, baixa e período de vencimento.',
      inputSchema: {
        tipo: z.string().describe('Tipo do lançamento: "receita", "despesa" ou "Todos"').optional(),
        baixado: z.string().describe('Status de baixa: "1" (baixado), "0" (em aberto) ou "Todos"').optional(),
        vencimento_de: z.string().describe('Data inicial de vencimento (YYYY-MM-DD)').optional(),
        vencimento_ate: z.string().describe('Data final de vencimento (YYYY-MM-DD)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_financeiro', (args) =>
      callApi({
        method: 'GET',
        path: 'financeiro',
        query: {
          tipo: args.tipo,
          baixado: args.baixado,
          vencimento_de: args.vencimento_de,
          vencimento_ate: args.vencimento_ate,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_resumo_financeiro',
    {
      title: 'Resumo Financeiro',
      description: 'Retorna estatísticas consolidadas de receitas, despesas e saldos do módulo financeiro.',
      inputSchema: {},
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_resumo_financeiro', () =>
      callApi({
        method: 'GET',
        path: 'financeiro/resumo',
      })
    )
  );

  server.registerTool(
    'steos_listar_veiculos',
    {
      title: 'Listar Veículos da Frota',
      description: 'Lista os veículos cadastrados na frota do STEOS com paginação.',
      inputSchema: {
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_veiculos', (args) =>
      callApi({
        method: 'GET',
        path: 'veiculos',
        query: {
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_detalhar_veiculo',
    {
      title: 'Detalhar Veículo',
      description: 'Obtém detalhes de um veículo incluindo abastecimentos e logs recentes.',
      inputSchema: {
        veiculoId: positiveInt('ID numérico do veículo'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_detalhar_veiculo', (args) =>
      callApi({
        method: 'GET',
        path: `veiculos/${args.veiculoId}`,
      })
    )
  );

  server.registerTool(
    'steos_listar_vendas',
    {
      title: 'Listar Vendas',
      description: 'Lista vendas com filtros de busca, status e período.',
      inputSchema: {
        search: z.string().describe('Termo de pesquisa da venda ou cliente').optional(),
        status: z.string().describe('Status da venda (opcional)').optional(),
        from: z.string().describe('Data inicial (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        to: z.string().describe('Data final (YYYY-MM-DD ou DD/MM/YYYY)').optional(),
        ...paginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_listar_vendas', (args) =>
      callApi({
        method: 'GET',
        path: 'vendas',
        query: {
          search: args.search,
          status: args.status,
          from: args.from,
          to: args.to,
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  server.registerTool(
    'steos_detalhar_venda',
    {
      title: 'Detalhar Venda',
      description: 'Obtém detalhes de uma venda específica incluindo produtos, cobranças e total.',
      inputSchema: {
        vendaId: positiveInt('ID numérico da venda'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withAdminTool('steos_detalhar_venda', (args) =>
      callApi({
        method: 'GET',
        path: `vendas/${args.vendaId}`,
      })
    )
  );

  // ============================================================================
  // ONDA 2 — ESCRITA OPERACIONAL (6 tools, registradas apenas se enableWrite=true)
  // ============================================================================
  if (config?.enableWrite === true) {
    server.registerTool(
      'steos_criar_os',
      {
        title: 'Criar Ordem de Serviço',
        description:
          'Cria uma nova Ordem de Serviço. Converte datas YYYY-MM-DD automaticamente para DD/MM/YYYY exigido pela API STEOS.',
        inputSchema: {
          clientes_id: positiveInt('ID do cliente'),
          usuarios_id: positiveInt('ID do usuário/técnico responsável'),
          status: z.string().min(1).describe('Status inicial (ex.: Aberto, Orçamento, Em Andamento)'),
          dataInicial: z.string().min(8).describe('Data inicial (YYYY-MM-DD ou DD/MM/YYYY)'),
          dataFinal: z.string().min(8).describe('Data final prevista (YYYY-MM-DD ou DD/MM/YYYY)'),
          descricaoProduto: z.string().describe('Descrição do equipamento/produto ou serviço').optional(),
          defeito: z.string().describe('Defeito relatado').optional(),
          observacoes: z.string().describe('Observações gerais').optional(),
          laudoTecnico: z.string().describe('Laudo técnico').optional(),
          garantia: nonNegativeInt('Dias de garantia (número inteiro)').optional(),
          contratos_id: positiveInt('ID do contrato vinculado (opcional)').optional(),
          local: z.string().describe('Local do atendimento').optional(),
          tipo: z.string().describe('Tipo da O.S. (ex.: Avulso, Contrato)').optional(),
          manutPreventiva: z.coerce
            .number()
            .int()
            .min(0)
            .max(1)
            .describe('1 para manutenção preventiva, 0 para normal')
            .optional(),
          prioridade: z.string().describe('Prioridade (opcional)').optional(),
          classificacao: z.string().describe('Classificação (opcional)').optional(),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_criar_os', (args) =>
        callApi({
          method: 'POST',
          path: 'os',
          body: compactObject({
            clientes_id: args.clientes_id,
            usuarios_id: args.usuarios_id,
            status: args.status,
            dataInicial: toSteosDateBR(args.dataInicial),
            dataFinal: toSteosDateBR(args.dataFinal),
            descricaoProduto: args.descricaoProduto,
            defeito: args.defeito,
            observacoes: args.observacoes,
            laudoTecnico: args.laudoTecnico,
            garantia: args.garantia !== undefined ? String(args.garantia) : undefined,
            contratos_id: args.contratos_id,
            local: args.local,
            tipo: args.tipo,
            manutPreventiva: args.manutPreventiva,
            prioridade: args.prioridade,
            classificacao: args.classificacao,
          }),
        })
      )
    );

    server.registerTool(
      'steos_atualizar_os',
      {
        title: 'Atualizar Ordem de Serviço',
        description:
          'Atualiza uma Ordem de Serviço existente (PUT /os/:id). Exige os campos obrigatórios do OsController e converte datas para DD/MM/YYYY.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          dataInicial: z.string().min(8).describe('Data inicial (YYYY-MM-DD ou DD/MM/YYYY)'),
          dataFinal: z.string().min(8).describe('Data final (YYYY-MM-DD ou DD/MM/YYYY)'),
          status: z.string().min(1).describe('Novo status da O.S.'),
          clientes_id: positiveInt('ID do cliente'),
          usuarios_id: positiveInt('ID do usuário responsável'),
          descricaoProduto: z.string().describe('Descrição do produto/equipamento').optional(),
          defeito: z.string().describe('Defeito relatado').optional(),
          defeito_encontrado: z.string().describe('Defeito constatado').optional(),
          observacoes: z.string().describe('Observações').optional(),
          laudoTecnico: z.string().describe('Laudo técnico').optional(),
          garantia: nonNegativeInt('Dias de garantia').optional(),
          contratos_id: positiveInt('ID do contrato vinculado').optional(),
          local: z.string().describe('Local do atendimento').optional(),
          tipo: z.string().describe('Tipo da O.S.').optional(),
          manutPreventiva: z.coerce.number().int().min(0).max(1).describe('1 preventiva, 0 normal').optional(),
          prioridade: z.string().describe('Prioridade').optional(),
          classificacao: z.string().describe('Classificação').optional(),
          faturado: z.coerce.number().int().min(0).max(1).describe('1 faturado, 0 não faturado').optional(),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_atualizar_os', (args) =>
        callApi({
          method: 'PUT',
          path: `os/${args.osId}`,
          body: compactObject({
            dataInicial: toSteosDateBR(args.dataInicial),
            dataFinal: toSteosDateBR(args.dataFinal),
            status: args.status,
            clientes_id: args.clientes_id,
            usuarios_id: args.usuarios_id,
            descricaoProduto: args.descricaoProduto,
            defeito: args.defeito,
            defeito_encontrado: args.defeito_encontrado,
            observacoes: args.observacoes,
            laudoTecnico: args.laudoTecnico,
            garantia: args.garantia !== undefined ? String(args.garantia) : undefined,
            contratos_id: args.contratos_id,
            local: args.local,
            tipo: args.tipo,
            manutPreventiva: args.manutPreventiva,
            prioridade: args.prioridade,
            classificacao: args.classificacao,
            faturado: args.faturado,
          }),
        })
      )
    );

    server.registerTool(
      'steos_adicionar_anotacao_os',
      {
        title: 'Adicionar Anotação na O.S.',
        description: 'Registra uma nova anotação técnica ou operacional em uma Ordem de Serviço.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          anotacao: z.string().min(1).describe('Texto da anotação'),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_adicionar_anotacao_os', (args) =>
        callApi({
          method: 'POST',
          path: `os/${args.osId}/anotacoes`,
          body: { anotacao: args.anotacao },
        })
      )
    );

    server.registerTool(
      'steos_adicionar_produto_os',
      {
        title: 'Adicionar Produto na O.S.',
        description: 'Vincula um produto a uma Ordem de Serviço informando quantidade e preço unitário.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          idProduto: positiveInt('ID do produto no catálogo'),
          quantidade: z.coerce.number().positive().describe('Quantidade (> 0)'),
          preco: z.coerce.number().min(0).describe('Preço unitário (>= 0)'),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_adicionar_produto_os', (args) =>
        callApi({
          method: 'POST',
          path: `os/${args.osId}/produtos`,
          body: {
            idProduto: args.idProduto,
            quantidade: args.quantidade,
            preco: args.preco,
          },
        })
      )
    );

    server.registerTool(
      'steos_adicionar_servico_os',
      {
        title: 'Adicionar Serviço na O.S.',
        description: 'Vincula um serviço a uma Ordem de Serviço informando quantidade e preço unitário.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          idServico: positiveInt('ID do serviço no catálogo'),
          quantidade: z.coerce.number().positive().describe('Quantidade (> 0)'),
          preco: z.coerce.number().min(0).describe('Preço unitário (>= 0)'),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_adicionar_servico_os', (args) =>
        callApi({
          method: 'POST',
          path: `os/${args.osId}/servicos`,
          body: {
            idServico: args.idServico,
            quantidade: args.quantidade,
            preco: args.preco,
          },
        })
      )
    );

    server.registerTool(
      'steos_criar_cliente',
      {
        title: 'Criar Cliente',
        description: 'Cadastra um novo cliente (ou fornecedor) no STEOS.',
        inputSchema: {
          nomeCliente: z.string().min(1).describe('Nome completo ou Razão Social do cliente'),
          documento: z.string().describe('CPF ou CNPJ válido').optional(),
          telefone: z.string().describe('Telefone fixo').optional(),
          celular: z.string().describe('Celular/WhatsApp').optional(),
          email: z.string().describe('E-mail do cliente').optional(),
          rua: z.string().describe('Logradouro').optional(),
          numero: z.string().describe('Número').optional(),
          complemento: z.string().describe('Complemento').optional(),
          bairro: z.string().describe('Bairro').optional(),
          cidade: z.string().describe('Cidade').optional(),
          estado: z.string().describe('UF (2 letras)').optional(),
          cep: z.string().describe('CEP').optional(),
          contato: z.string().describe('Nome do contato principal').optional(),
          fornecedor: z.boolean().describe('Se true, marca também como fornecedor').optional(),
        },
        annotations: WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_criar_cliente', (args) =>
        callApi({
          method: 'POST',
          path: 'clientes',
          body: compactObject({
            nomeCliente: args.nomeCliente,
            documento: args.documento,
            telefone: args.telefone,
            celular: args.celular,
            email: args.email,
            rua: args.rua,
            numero: args.numero,
            complemento: args.complemento,
            bairro: args.bairro,
            cidade: args.cidade,
            estado: args.estado,
            cep: args.cep,
            contato: args.contato,
            fornecedor: args.fornecedor,
          }),
        })
      )
    );
  }

  // ============================================================================
  // ONDA 3 — FISCAL CRÍTICA (2 tools, registradas apenas se enableFiscalWrite=true)
  // ============================================================================
  if (config?.enableFiscalWrite === true) {
    server.registerTool(
      'steos_emitir_nfse',
      {
        title: 'Emitir/Agendar NFS-e para O.S. (Operação Fiscal Irreversível)',
        description:
          'AÇÃO FISCAL CRÍTICA: Agenda/emite NFS-e no Asaas/Portal Nacional para a Ordem de Serviço. Exige confirm=true explícito.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          confirm: z
            .literal(true)
            .describe('Confirmação explícita obrigatória (true) para autorizar emissão fiscal'),
          servico_nfse_id: positiveInt('ID do serviço fiscal padrão em servicos_nfse').optional(),
          value: z.coerce.number().positive().describe('Valor da NFS-e (sobrescreve valorTotal da O.S.)').optional(),
          aliquota_iss: z.coerce.number().min(0).describe('Alíquota ISS (%)').optional(),
          effective_date: z.string().describe('Data de competência/emissão YYYY-MM-DD').optional(),
          service_description: z.string().describe('Discriminação dos serviços na nota').optional(),
          observations: z.string().describe('Observações da NFS-e').optional(),
          retain_iss: z.boolean().describe('Se true, indica retenção de ISS na fonte').optional(),
          charge_id: z.string().describe('ID da cobrança Asaas (payment) para vincular').optional(),
          installment_id: z.string().describe('ID do parcelamento Asaas (installment) para vincular').optional(),
        },
        annotations: FISCAL_WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_emitir_nfse', (args) => {
        if (args.confirm !== true) {
          throw new Error('Operação fiscal bloqueada: o parâmetro "confirm" deve ser estritamente true.');
        }

        return callApi({
          method: 'POST',
          path: `nfse/emitir/${args.osId}`,
          body: compactObject({
            servico_nfse_id: args.servico_nfse_id,
            value: args.value,
            aliquota_iss: args.aliquota_iss,
            effective_date: args.effective_date,
            service_description: args.service_description,
            observations: args.observations,
            retain_iss: args.retain_iss,
            charge_id: args.charge_id,
            installment_id: args.installment_id,
          }),
        });
      })
    );

    server.registerTool(
      'steos_cancelar_nfse',
      {
        title: 'Cancelar NFS-e da O.S. (Operação Fiscal Irreversível)',
        description:
          'AÇÃO FISCAL CRÍTICA: Solicita o cancelamento da NFS-e vinculada à Ordem de Serviço no Asaas/Prefeitura. Exige confirm=true explícito.',
        inputSchema: {
          osId: positiveInt('ID numérico da Ordem de Serviço'),
          confirm: z
            .literal(true)
            .describe('Confirmação explícita obrigatória (true) para autorizar cancelamento fiscal'),
          cancelOnlyOnAsaas: z
            .boolean()
            .describe('Se true, solicita cancelamento somente no Asaas')
            .optional(),
        },
        annotations: FISCAL_WRITE_ANNOTATIONS,
      },
      withAdminTool('steos_cancelar_nfse', (args) => {
        if (args.confirm !== true) {
          throw new Error('Operação fiscal bloqueada: o parâmetro "confirm" deve ser estritamente true.');
        }

        return callApi({
          method: 'POST',
          path: `nfse/cancelar/${args.osId}`,
          body: compactObject({
            cancelOnlyOnAsaas: args.cancelOnlyOnAsaas,
          }),
        });
      })
    );
  }
}
