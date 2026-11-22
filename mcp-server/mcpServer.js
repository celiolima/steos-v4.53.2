import path from 'node:path';
import { pathToFileURL } from 'node:url';
import express from 'express';
import cors from 'cors';
import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { StreamableHTTPServerTransport } from '@modelcontextprotocol/sdk/server/streamableHttp.js';
import { SSEServerTransport } from '@modelcontextprotocol/sdk/server/sse.js';
import { config as defaultConfig, assertHttpSecrets } from './src/config.js';
import {
  createApiKeyMiddleware,
  createRateLimiter,
  logToolExecution,
} from './src/lib/authMiddleware.js';
import {
  SteosAdminClient,
  SteosClientPortalClient,
} from './src/lib/steosClient.js';
import { registerAdminTools } from './src/tools/admin/index.js';
import { registerClientTools } from './src/tools/client/index.js';

export const SERVER_VERSION = '1.0.0';

let sharedAdminClient = null;
let sharedClientPortalClient = null;

function getSharedAdminClient(cfg = defaultConfig) {
  if (!sharedAdminClient || cfg !== defaultConfig) {
    const client = new SteosAdminClient({
      baseUrl: cfg.steosBaseUrl,
      serviceEmail: cfg.steosServiceEmail,
      servicePassword: cfg.steosServicePassword,
    });
    if (cfg === defaultConfig) {
      sharedAdminClient = client;
    }
    return client;
  }
  return sharedAdminClient;
}

function getSharedClientPortalClient(cfg = defaultConfig) {
  if (!sharedClientPortalClient || cfg !== defaultConfig) {
    const client = new SteosClientPortalClient({
      baseUrl: cfg.steosBaseUrl,
      botSecret: cfg.steosBotSecret,
    });
    if (cfg === defaultConfig) {
      sharedClientPortalClient = client;
    }
    return client;
  }
  return sharedClientPortalClient;
}

export function createAdminServer({
  overrideToken,
  configOverride,
  adminClient,
} = {}) {
  const activeConfig = configOverride || defaultConfig;
  const steosAdminClient = adminClient || getSharedAdminClient(activeConfig);

  const server = new McpServer(
    {
      name: 'steos-mcp-admin',
      version: SERVER_VERSION,
    },
    {
      capabilities: {
        tools: {},
      },
    }
  );

  registerAdminTools(server, {
    steosAdminClient,
    config: activeConfig,
    overrideToken,
  });

  return server;
}

export function createClientServer({
  defaultClientToken,
  configOverride,
  clientPortalClient,
} = {}) {
  const activeConfig = configOverride || defaultConfig;
  const steosClientPortalClient =
    clientPortalClient || getSharedClientPortalClient(activeConfig);

  const server = new McpServer(
    {
      name: 'steos-mcp-client',
      version: SERVER_VERSION,
    },
    {
      capabilities: {
        tools: {},
      },
    }
  );

  registerClientTools(server, {
    steosClientPortalClient,
    defaultClientToken,
  });

  return server;
}

export function createHttpApp(activeConfig = defaultConfig) {
  assertHttpSecrets(activeConfig);

  const app = express();
  app.disable('x-powered-by');
  app.set('trust proxy', 1);

  app.use(
    cors({
      origin: activeConfig.corsOrigin,
      methods: ['GET', 'POST', 'DELETE', 'OPTIONS'],
      allowedHeaders: [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-API-Key',
        'X-Steos-Token',
        'X-Client-Token',
        'Mcp-Session-Id',
        'Mcp-Protocol-Version',
        'Last-Event-ID',
      ],
      exposedHeaders: ['Mcp-Session-Id', 'Allow'],
    })
  );

  app.use(express.json({ limit: '1mb' }));

  // Tratamento limpo para payloads JSON malformados
  app.use((err, _req, res, next) => {
    if (err instanceof SyntaxError && 'body' in err) {
      return res.status(400).json({
        jsonrpc: '2.0',
        error: {
          code: -32700,
          message: 'Parse error: JSON inválido no corpo da requisição.',
        },
        id: null,
      });
    }
    return next(err);
  });

  const adminRateLimiter = createRateLimiter({
    windowMs: 60_000,
    max: activeConfig.rateLimitPerMin,
    scope: 'admin',
  });
  const clientRateLimiter = createRateLimiter({
    windowMs: 60_000,
    max: activeConfig.rateLimitPerMin,
    scope: 'client',
  });

  const requireAdminApiKey = createApiKeyMiddleware(activeConfig.mcpAdminApiKey, 'admin');
  const requireClientApiKey = createApiKeyMiddleware(activeConfig.mcpClientApiKey, 'client');

  // Mapas de sessão SSE estritamente isolados por escopo
  const adminSseSessions = new Map();
  const clientSseSessions = new Map();

  app.get('/health', (_req, res) => {
    res.status(200).json({
      status: 'ok',
      service: 'steos-mcp',
      version: SERVER_VERSION,
      mode: {
        write: activeConfig.enableWrite,
        fiscalWrite: activeConfig.enableFiscalWrite,
      },
      endpoints: {
        streamableHttp: ['/mcp/admin', '/mcp/client'],
        legacySse: ['/sse/admin', '/sse/client'],
      },
    });
  });

  // ==========================================================================
  // Streamable HTTP Stateless (/mcp/admin e /mcp/client)
  // ==========================================================================

  app.post('/mcp/admin', adminRateLimiter, requireAdminApiKey, async (req, res) => {
    const overrideToken = (req.header('x-steos-token') || '').trim() || undefined;
    const server = createAdminServer({
      overrideToken,
      configOverride: activeConfig,
    });
    const transport = new StreamableHTTPServerTransport({
      sessionIdGenerator: undefined,
    });

    let closed = false;
    const cleanup = () => {
      if (closed) {
        return;
      }
      closed = true;
      transport.close().catch(() => {});
      server.close().catch(() => {});
    };
    res.on('close', cleanup);

    try {
      await server.connect(transport);
      await transport.handleRequest(req, res, req.body);
    } catch (err) {
      logToolExecution({
        scope: 'admin',
        tool: 'streamable_http',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      cleanup();
      if (!res.headersSent) {
        res.status(500).json({
          jsonrpc: '2.0',
          error: {
            code: -32603,
            message: 'Erro interno ao processar requisição MCP admin.',
          },
          id: null,
        });
      }
    }
  });

  app.post('/mcp/client', clientRateLimiter, requireClientApiKey, async (req, res) => {
    const defaultClientToken = (req.header('x-client-token') || '').trim() || undefined;
    const server = createClientServer({
      defaultClientToken,
      configOverride: activeConfig,
    });
    const transport = new StreamableHTTPServerTransport({
      sessionIdGenerator: undefined,
    });

    let closed = false;
    const cleanup = () => {
      if (closed) {
        return;
      }
      closed = true;
      transport.close().catch(() => {});
      server.close().catch(() => {});
    };
    res.on('close', cleanup);

    try {
      await server.connect(transport);
      await transport.handleRequest(req, res, req.body);
    } catch (err) {
      logToolExecution({
        scope: 'client',
        tool: 'streamable_http',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      cleanup();
      if (!res.headersSent) {
        res.status(500).json({
          jsonrpc: '2.0',
          error: {
            code: -32603,
            message: 'Erro interno ao processar requisição MCP client.',
          },
          id: null,
        });
      }
    }
  });

  const statelessMethodNotAllowed = (_req, res) => {
    res.setHeader('Allow', 'POST');
    res.status(405).json({
      jsonrpc: '2.0',
      error: {
        code: -32000,
        message: 'Method not allowed in stateless mode.',
      },
      id: null,
    });
  };

  app.get('/mcp/admin', adminRateLimiter, requireAdminApiKey, statelessMethodNotAllowed);
  app.delete('/mcp/admin', adminRateLimiter, requireAdminApiKey, statelessMethodNotAllowed);
  app.get('/mcp/client', clientRateLimiter, requireClientApiKey, statelessMethodNotAllowed);
  app.delete('/mcp/client', clientRateLimiter, requireClientApiKey, statelessMethodNotAllowed);

  // ==========================================================================
  // SSE Legado (/sse/admin + /messages/admin e /sse/client + /messages/client)
  // ==========================================================================

  app.get('/sse/admin', adminRateLimiter, requireAdminApiKey, async (req, res) => {
    const overrideToken = (req.header('x-steos-token') || '').trim() || undefined;
    const transport = new SSEServerTransport('/messages/admin', res);
    const server = createAdminServer({
      overrideToken,
      configOverride: activeConfig,
    });
    const sessionId = transport.sessionId;

    let cleaned = false;
    const cleanup = () => {
      if (cleaned) {
        return;
      }
      cleaned = true;
      adminSseSessions.delete(sessionId);
      server.close().catch(() => {});
    };

    adminSseSessions.set(sessionId, { server, transport, cleanup });
    transport.onclose = cleanup;
    res.on('close', cleanup);

    try {
      await server.connect(transport);
    } catch (err) {
      cleanup();
      logToolExecution({
        scope: 'admin',
        tool: 'sse_connect',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      if (!res.headersSent) {
        res.status(500).json({ error: 'Internal Server Error', message: 'Falha ao iniciar SSE admin.' });
      }
    }
  });

  app.post('/messages/admin', adminRateLimiter, requireAdminApiKey, async (req, res) => {
    const sessionId = String(req.query.sessionId || '').trim();
    const session = sessionId ? adminSseSessions.get(sessionId) : undefined;
    if (!session) {
      return res.status(404).json({
        error: 'Not Found',
        message: 'Sessão SSE admin não encontrada ou expirada.',
      });
    }

    try {
      await session.transport.handlePostMessage(req, res, req.body);
    } catch (err) {
      logToolExecution({
        scope: 'admin',
        tool: 'sse_message',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      if (!res.headersSent) {
        res.status(500).json({ error: 'Internal Server Error', message: 'Falha ao processar mensagem SSE admin.' });
      }
    }
  });

  app.get('/sse/client', clientRateLimiter, requireClientApiKey, async (req, res) => {
    const defaultClientToken = (req.header('x-client-token') || '').trim() || undefined;
    const transport = new SSEServerTransport('/messages/client', res);
    const server = createClientServer({
      defaultClientToken,
      configOverride: activeConfig,
    });
    const sessionId = transport.sessionId;

    let cleaned = false;
    const cleanup = () => {
      if (cleaned) {
        return;
      }
      cleaned = true;
      clientSseSessions.delete(sessionId);
      server.close().catch(() => {});
    };

    clientSseSessions.set(sessionId, { server, transport, cleanup });
    transport.onclose = cleanup;
    res.on('close', cleanup);

    try {
      await server.connect(transport);
    } catch (err) {
      cleanup();
      logToolExecution({
        scope: 'client',
        tool: 'sse_connect',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      if (!res.headersSent) {
        res.status(500).json({ error: 'Internal Server Error', message: 'Falha ao iniciar SSE client.' });
      }
    }
  });

  app.post('/messages/client', clientRateLimiter, requireClientApiKey, async (req, res) => {
    const sessionId = String(req.query.sessionId || '').trim();
    const session = sessionId ? clientSseSessions.get(sessionId) : undefined;
    if (!session) {
      return res.status(404).json({
        error: 'Not Found',
        message: 'Sessão SSE client não encontrada ou expirada.',
      });
    }

    try {
      await session.transport.handlePostMessage(req, res, req.body);
    } catch (err) {
      logToolExecution({
        scope: 'client',
        tool: 'sse_message',
        status: 'error',
        durationMs: 0,
        detail: err instanceof Error ? err.message : String(err),
      });
      if (!res.headersSent) {
        res.status(500).json({ error: 'Internal Server Error', message: 'Falha ao processar mensagem SSE client.' });
      }
    }
  });

  return {
    app,
    closeSessions: async () => {
      for (const session of [...adminSseSessions.values(), ...clientSseSessions.values()]) {
        try {
          await session.transport.close();
        } catch {
          // ignore
        }
        try {
          await session.server.close();
        } catch {
          // ignore
        }
      }
      adminSseSessions.clear();
      clientSseSessions.clear();
    },
  };
}

async function startStdioMode({ clientScope }) {
  const server = clientScope ? createClientServer() : createAdminServer();
  const transport = new StdioServerTransport();

  const shutdown = async () => {
    try {
      await transport.close();
    } catch {
      // ignore
    }
    try {
      await server.close();
    } catch {
      // ignore
    }
    process.exit(0);
  };

  process.once('SIGINT', shutdown);
  process.once('SIGTERM', shutdown);

  await server.connect(transport);
  logToolExecution({
    scope: clientScope ? 'client' : 'admin',
    tool: 'stdio_boot',
    status: 'ok',
    durationMs: 0,
    detail: 'Servidor MCP STEOS iniciado em modo STDIO.',
  });
}

async function startHttpMode() {
  const { app, closeSessions } = createHttpApp(defaultConfig);

  const httpServer = app.listen(defaultConfig.port, () => {
    logToolExecution({
      scope: 'system',
      tool: 'http_boot',
      status: 'ok',
      durationMs: 0,
      detail: {
        port: defaultConfig.port,
        nodeEnv: defaultConfig.nodeEnv,
        write: defaultConfig.enableWrite,
        fiscalWrite: defaultConfig.enableFiscalWrite,
        steosBaseUrl: defaultConfig.steosBaseUrl,
      },
    });
  });

  let shuttingDown = false;
  const shutdown = async (signal) => {
    if (shuttingDown) {
      return;
    }
    shuttingDown = true;
    logToolExecution({
      scope: 'system',
      tool: 'http_shutdown',
      status: 'ok',
      durationMs: 0,
      detail: `Encerrando servidor MCP (${signal})`,
    });

    await closeSessions();
    httpServer.close(() => {
      process.exit(0);
    });
    setTimeout(() => process.exit(0), 3000).unref();
  };

  process.once('SIGINT', () => shutdown('SIGINT'));
  process.once('SIGTERM', () => shutdown('SIGTERM'));
}

function isMainModule() {
  if (!process.argv[1]) {
    return false;
  }
  return import.meta.url === pathToFileURL(path.resolve(process.argv[1])).href;
}

if (isMainModule()) {
  const args = new Set(process.argv.slice(2));
  const useHttp = args.has('--streamable-http');
  const clientScope = args.has('--client');

  const runner = useHttp ? startHttpMode() : startStdioMode({ clientScope });
  runner.catch((err) => {
    logToolExecution({
      scope: 'system',
      tool: 'boot',
      status: 'error',
      durationMs: 0,
      detail: err instanceof Error ? err.message : String(err),
    });
    process.exit(1);
  });
}
