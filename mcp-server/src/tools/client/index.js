import { z } from 'zod';
import { logToolExecution } from '../../lib/authMiddleware.js';
import { formatToolError, formatToolSuccess } from '../admin/index.js';

const positiveInt = (description) =>
  z.coerce.number().int().positive().describe(description);

const nonNegativeInt = (description) =>
  z.coerce.number().int().min(0).describe(description);

const optionalSessionToken = z
  .string()
  .min(1)
  .describe('Token JWT da sessão do cliente retornado por steos_cliente_autenticar (opcional se enviado via header X-Client-Token)')
  .optional();

const clientPaginationShape = {
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

const AUTH_ANNOTATIONS = {
  readOnlyHint: false,
  destructiveHint: false,
  idempotentHint: false,
  openWorldHint: false,
};

const WRITE_ANNOTATIONS = {
  readOnlyHint: false,
  destructiveHint: false,
  idempotentHint: false,
  openWorldHint: false,
};

function withClientTool(toolName, handler) {
  return async (args = {}) => {
    const startedAt = Date.now();
    try {
      const data = await handler(args ?? {});
      logToolExecution({
        scope: 'client',
        tool: toolName,
        status: 'ok',
        durationMs: Date.now() - startedAt,
      });
      return formatToolSuccess(data);
    } catch (err) {
      logToolExecution({
        scope: 'client',
        tool: toolName,
        status: 'error',
        durationMs: Date.now() - startedAt,
        detail: err instanceof Error ? err.message : String(err),
      });
      return formatToolError(err);
    }
  };
}

function resolveClientToken(args, defaultClientToken) {
  const token = String(args?.session_token || defaultClientToken || '').trim();
  if (!token) {
    throw new Error(
      'Sessão do cliente não autenticada. Chame primeiro "steos_cliente_autenticar" (documento + telefone) ou "steos_cliente_autenticar_email" para obter o session_token.'
    );
  }
  return token;
}

export function registerClientTools(
  server,
  { steosClientPortalClient, defaultClientToken } = {}
) {
  // 1. Autenticação por CPF/CNPJ + telefone (bot WhatsApp / atendimento)
  server.registerTool(
    'steos_cliente_autenticar',
    {
      title: 'Autenticar Cliente (CPF/CNPJ + Telefone)',
      description:
        'Autentica o cliente no Portal Conecte usando documento (CPF/CNPJ) e telefone/celular cadastrados, retornando session_token exclusivo do cliente.',
      inputSchema: {
        documento: z.string().min(4).describe('CPF ou CNPJ do cliente'),
        telefone: z.string().min(4).describe('Telefone ou celular cadastrado do cliente'),
      },
      annotations: AUTH_ANNOTATIONS,
    },
    withClientTool('steos_cliente_autenticar', async (args) => {
      const response = await steosClientPortalClient.authenticateByDocument(
        args.documento,
        args.telefone
      );

      const sessionToken =
        response?.result?.access_token ??
        response?.access_token ??
        response?.session_token ??
        null;

      if (!sessionToken) {
        throw new Error(
          response?.message || 'Autenticação não retornou token de sessão do cliente.'
        );
      }

      return {
        authenticated: true,
        session_token: sessionToken,
        expires_in: response?.result?.expires_in ?? response?.expires_in ?? null,
        cliente: response?.result?.cliente ?? response?.cliente ?? null,
      };
    })
  );

  // 2. Autenticação por e-mail e senha
  server.registerTool(
    'steos_cliente_autenticar_email',
    {
      title: 'Autenticar Cliente (E-mail e Senha)',
      description:
        'Autentica o cliente no Portal Conecte usando e-mail e senha cadastrados, retornando session_token exclusivo do cliente.',
      inputSchema: {
        email: z.string().email().describe('E-mail cadastrado do cliente'),
        password: z.string().min(1).describe('Senha de acesso ao Portal do Cliente'),
      },
      annotations: AUTH_ANNOTATIONS,
    },
    withClientTool('steos_cliente_autenticar_email', async (args) => {
      const response = await steosClientPortalClient.authenticateByEmail(
        args.email,
        args.password
      );

      const sessionToken =
        response?.result?.access_token ??
        response?.access_token ??
        response?.session_token ??
        null;

      if (!sessionToken) {
        throw new Error(
          response?.message || 'Autenticação não retornou token de sessão do cliente.'
        );
      }

      return {
        authenticated: true,
        session_token: sessionToken,
      };
    })
  );

  // 3. Resumo do portal do cliente
  server.registerTool(
    'steos_cliente_resumo',
    {
      title: 'Resumo do Portal do Cliente',
      description: 'Retorna as últimas Ordens de Serviço e compras do cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_resumo', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: 'client',
      })
    )
  );

  // 4. Listar O.S. do cliente autenticado
  server.registerTool(
    'steos_cliente_listar_os',
    {
      title: 'Listar Minhas Ordens de Serviço',
      description: 'Lista apenas as Ordens de Serviço pertencentes ao cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        ...clientPaginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_listar_os', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: 'client/os',
        query: {
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  // 5. Detalhar O.S. do cliente autenticado
  server.registerTool(
    'steos_cliente_detalhar_os',
    {
      title: 'Detalhar Minha Ordem de Serviço',
      description: 'Obtém detalhes, itens, anexos e Pix de uma Ordem de Serviço do cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        osId: positiveInt('ID numérico da Ordem de Serviço do cliente'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_detalhar_os', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: `client/os/${args.osId}`,
      })
    )
  );

  // 6. Abrir solicitação de O.S. pelo cliente autenticado
  server.registerTool(
    'steos_cliente_abrir_os',
    {
      title: 'Abrir Chamado / Ordem de Serviço do Cliente',
      description: 'Abre uma nova solicitação de Ordem de Serviço em nome do cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        descricaoProduto: z
          .string()
          .min(1)
          .describe('Descrição do equipamento, produto ou solicitação de atendimento'),
        defeito: z.string().describe('Descrição do defeito ou sintoma observado').optional(),
        observacoes: z.string().describe('Observações adicionais ou horário de preferência').optional(),
      },
      annotations: WRITE_ANNOTATIONS,
    },
    withClientTool('steos_cliente_abrir_os', (args) => {
      const body = {
        descricaoProduto: args.descricaoProduto,
      };
      if (args.defeito !== undefined) {
        body.defeito = args.defeito;
      }
      if (args.observacoes !== undefined) {
        body.observacoes = args.observacoes;
      }

      return steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'POST',
        path: 'client/os',
        body,
      });
    })
  );

  // 7. Listar contratos do cliente autenticado
  server.registerTool(
    'steos_cliente_listar_contratos',
    {
      title: 'Listar Meus Contratos',
      description: 'Lista apenas os contratos pertencentes ao cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        ...clientPaginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_listar_contratos', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: 'client/contratos',
        query: {
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  // 8. Detalhar contrato do cliente autenticado
  server.registerTool(
    'steos_cliente_detalhar_contrato',
    {
      title: 'Detalhar Meu Contrato',
      description: 'Obtém detalhes somente leitura de um contrato pertencente ao cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        contratoId: positiveInt('ID numérico do contrato do cliente'),
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_detalhar_contrato', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: `client/contratos/${args.contratoId}`,
      })
    )
  );

  // 9. Listar cobranças do cliente autenticado
  server.registerTool(
    'steos_cliente_listar_cobrancas',
    {
      title: 'Listar Minhas Cobranças',
      description: 'Lista apenas as cobranças, boletos e links de pagamento pertencentes ao cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        ...clientPaginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_listar_cobrancas', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: 'client/cobrancas',
        query: {
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );

  // 10. Listar compras do cliente autenticado
  server.registerTool(
    'steos_cliente_listar_compras',
    {
      title: 'Listar Minhas Compras',
      description: 'Lista apenas as compras/vendas pertencentes ao cliente autenticado.',
      inputSchema: {
        session_token: optionalSessionToken,
        ...clientPaginationShape,
      },
      annotations: READ_ONLY_ANNOTATIONS,
    },
    withClientTool('steos_cliente_listar_compras', (args) =>
      steosClientPortalClient.request({
        clientToken: resolveClientToken(args, defaultClientToken),
        method: 'GET',
        path: 'client/compras',
        query: {
          page: args.page,
          perPage: args.perPage,
        },
      })
    )
  );
}
