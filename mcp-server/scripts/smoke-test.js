import assert from 'node:assert/strict';
import http from 'node:http';
import net from 'node:net';
import path from 'node:path';
import { spawn } from 'node:child_process';
import { Client } from '@modelcontextprotocol/sdk/client/index.js';
import { StreamableHTTPClientTransport } from '@modelcontextprotocol/sdk/client/streamableHttp.js';
import { StdioClientTransport } from '@modelcontextprotocol/sdk/client/stdio.js';
import { config as envConfig, MCP_ROOT_DIR } from '../src/config.js';

const ADMIN_KEY = envConfig.mcpAdminApiKey && envConfig.mcpAdminApiKey.length >= 16
  ? envConfig.mcpAdminApiKey
  : 'smoke_admin_key_1234567890_abcdef';

const CLIENT_KEY =
  envConfig.mcpClientApiKey &&
  envConfig.mcpClientApiKey.length >= 16 &&
  envConfig.mcpClientApiKey !== ADMIN_KEY
    ? envConfig.mcpClientApiKey
    : 'smoke_client_key_0987654321_zyxwvu';

const BOT_SECRET = envConfig.steosBotSecret || 'smoke_bot_secret_123456';

function getFreePort() {
  return new Promise((resolve, reject) => {
    const srv = net.createServer();
    srv.unref();
    srv.on('error', reject);
    srv.listen(0, '127.0.0.1', () => {
      const { port } = srv.address();
      srv.close(() => resolve(port));
    });
  });
}

async function isRealSteosAvailable() {
  if (process.argv.includes('--mock')) {
    return false;
  }
  if (!envConfig.steosServiceEmail || !envConfig.steosServicePassword) {
    return false;
  }
  try {
    const res = await fetch(`${envConfig.steosBaseUrl}/index.php/api/v1`, {
      signal: AbortSignal.timeout(2000),
    });
    return res.status > 0 && res.status < 500;
  } catch {
    return false;
  }
}

function createMockSteosServer() {
  const staffToken = 'mock_staff_jwt_token_v1';
  const clientToken = 'mock_client_jwt_token_v1';

  const server = http.createServer((req, res) => {
    const chunks = [];
    req.on('data', (chunk) => chunks.push(chunk));
    req.on('end', () => {
      const rawBody = Buffer.concat(chunks).toString('utf8');
      let body = {};
      if (rawBody) {
        try {
          body = JSON.parse(rawBody);
        } catch {
          body = {};
        }
      }

      const urlObj = new URL(req.url, 'http://127.0.0.1');
      const pathname = urlObj.pathname;

      const sendJson = (status, data) => {
        res.writeHead(status, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(data));
      };

      if (req.method === 'POST' && pathname === '/index.php/api/v1/login') {
        if (body.email === 'smoke@steos.local' && body.password === 'smoke-pass') {
          return sendJson(200, {
            status: true,
            message: 'Login realizado com sucesso!',
            result: { access_token: staffToken },
          });
        }
        return sendJson(401, { status: false, message: 'Os dados de acesso estão incorretos!' });
      }

      if (req.method === 'GET' && pathname === '/index.php/api/v1/reGenToken') {
        return sendJson(200, {
          status: true,
          result: { access_token: staffToken },
        });
      }

      if (req.method === 'POST' && pathname === '/index.php/api/v1/client/auth-bot') {
        if (req.headers['x-bot-secret'] !== BOT_SECRET) {
          return sendJson(401, { status: false, message: 'Segredo do bot inválido.' });
        }
        if (body.documento === '12345678909' && body.telefone === '11999990000') {
          return sendJson(200, {
            status: true,
            result: {
              access_token: clientToken,
              expires_in: 3600,
              cliente: { idClientes: 1, nomeCliente: 'Cliente Smoke Test' },
            },
          });
        }
        return sendJson(401, { status: false, message: 'Cliente não encontrado ou telefone inválido.' });
      }

      if (pathname.startsWith('/index.php/api/v1/client')) {
        if (req.headers.authorization !== `Bearer ${clientToken}`) {
          return sendJson(403, { status: false, message: 'Faça o login novamente!' });
        }
        if (pathname === '/index.php/api/v1/client/os') {
          return sendJson(200, {
            status: true,
            message: 'Listando resultados',
            result: { Os: [{ idOs: 101, status: 'Aberto' }] },
          });
        }
        return sendJson(200, { status: true, result: { Os: [], compras: [] } });
      }

      // Rotas Admin protegidas por staffToken
      if (req.headers.authorization !== `Bearer ${staffToken}`) {
        return sendJson(403, { status: false, message: 'Faça o login novamente!' });
      }

      if (req.method === 'GET' && pathname === '/index.php/api/v1') {
        return sendJson(200, {
          status: true,
          message: 'Dashboard',
          result: { countOs: 12, clientes: 5, contratos: 3 },
        });
      }

      if (req.method === 'GET' && pathname === '/index.php/api/v1/os') {
        return sendJson(200, {
          status: true,
          message: 'Listando OSs',
          result: [{ idOs: 101, status: 'Aberto', nomeCliente: 'Cliente Smoke Test' }],
        });
      }

      return sendJson(404, { status: false, message: 'Endpoint mock não encontrado.' });
    });
  });

  return server;
}

async function waitForHealth(baseUrl, timeoutMs = 10_000) {
  const deadline = Date.now() + timeoutMs;
  while (Date.now() < deadline) {
    try {
      const res = await fetch(`${baseUrl}/health`, { signal: AbortSignal.timeout(1000) });
      if (res.ok) {
        return await res.json();
      }
    } catch {
      // aguarda servidor subir
    }
    await new Promise((r) => setTimeout(r, 150));
  }
  throw new Error(`Timeout aguardando ${baseUrl}/health responder.`);
}

async function startHttpServerProcess(envOverrides) {
  const port = await getFreePort();
  const baseUrl = `http://127.0.0.1:${port}`;
  const stderrChunks = [];

  const child = spawn(process.execPath, [path.join(MCP_ROOT_DIR, 'mcpServer.js'), '--streamable-http'], {
    cwd: MCP_ROOT_DIR,
    env: {
      ...process.env,
      PORT: String(port),
      MCP_ADMIN_API_KEY: ADMIN_KEY,
      MCP_CLIENT_API_KEY: CLIENT_KEY,
      STEOS_BOT_SECRET: BOT_SECRET,
      MCP_ENABLE_WRITE: 'true',
      MCP_ENABLE_FISCAL_WRITE: 'false',
      ...envOverrides,
    },
    stdio: ['ignore', 'pipe', 'pipe'],
  });

  child.stderr.on('data', (d) => stderrChunks.push(d.toString('utf8')));
  child.stdout.on('data', (d) => stderrChunks.push(d.toString('utf8')));

  try {
    const health = await waitForHealth(baseUrl);
    return {
      baseUrl,
      health,
      stop: async () => {
        if (!child.killed) {
          child.kill('SIGTERM');
          await new Promise((resolve) => {
            child.once('exit', resolve);
            setTimeout(() => {
              if (!child.killed) child.kill('SIGKILL');
              resolve();
            }, 3000);
          });
        }
      },
    };
  } catch (err) {
    child.kill('SIGKILL');
    throw new Error(`${err.message}\nLogs:\n${stderrChunks.join('')}`);
  }
}

async function main() {
  const useReal = await isRealSteosAvailable();
  let mockServer = null;
  let steosEnv = {};

  if (useReal) {
    console.log(`[SMOKE] Usando backend STEOS REAL em ${envConfig.steosBaseUrl}`);
    steosEnv = {
      STEOS_BASE_URL: envConfig.steosBaseUrl,
      STEOS_SERVICE_EMAIL: envConfig.steosServiceEmail,
      STEOS_SERVICE_PASSWORD: envConfig.steosServicePassword,
    };
  } else {
    mockServer = createMockSteosServer();
    await new Promise((resolve) => mockServer.listen(0, '127.0.0.1', resolve));
    const mockPort = mockServer.address().port;
    const mockBaseUrl = `http://127.0.0.1:${mockPort}`;
    console.log(`[SMOKE] STEOS real indisponível ou --mock ativo; usando Mock HTTP local em ${mockBaseUrl}`);
    steosEnv = {
      STEOS_BASE_URL: mockBaseUrl,
      STEOS_SERVICE_EMAIL: 'smoke@steos.local',
      STEOS_SERVICE_PASSWORD: 'smoke-pass',
    };
  }

  let primary = null;
  let fiscalServer = null;

  try {
    primary = await startHttpServerProcess({
      ...steosEnv,
      MCP_ENABLE_FISCAL_WRITE: 'false',
    });

    // 1. /health -> 200
    const healthRes = await fetch(`${primary.baseUrl}/health`);
    assert.equal(healthRes.status, 200, '/health deve retornar HTTP 200');
    const healthJson = await healthRes.json();
    assert.equal(healthJson.status, 'ok');
    assert.equal(healthJson.service, 'steos-mcp');
    assert.equal(healthJson.mode.fiscalWrite, false);
    console.log('PASS [1/8] GET /health -> 200 OK');

    // 2. /mcp/admin sem chave -> 401; com chave de client -> 401
    const adminNoAuth = await fetch(`${primary.baseUrl}/mcp/admin`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: '{}',
    });
    assert.equal(adminNoAuth.status, 401, '/mcp/admin sem chave deve retornar 401');

    const adminWrongAuth = await fetch(`${primary.baseUrl}/mcp/admin`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${CLIENT_KEY}`,
      },
      body: '{}',
    });
    assert.equal(adminWrongAuth.status, 401, '/mcp/admin com chave de client deve retornar 401');

    const adminGetStateless = await fetch(`${primary.baseUrl}/mcp/admin`, {
      method: 'GET',
      headers: { Authorization: `Bearer ${ADMIN_KEY}` },
    });
    assert.equal(adminGetStateless.status, 405, 'GET /mcp/admin stateless deve retornar 405');
    console.log('PASS [2/8] /mcp/admin bloqueia sem chave / chave client (401) e GET retorna 405');

    // 3. /mcp/client sem chave -> 401; com chave de admin -> 401
    const clientNoAuth = await fetch(`${primary.baseUrl}/mcp/client`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: '{}',
    });
    assert.equal(clientNoAuth.status, 401, '/mcp/client sem chave deve retornar 401');

    const clientWrongAuth = await fetch(`${primary.baseUrl}/mcp/client`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'x-api-key': ADMIN_KEY,
      },
      body: '{}',
    });
    assert.equal(clientWrongAuth.status, 401, '/mcp/client com chave de admin deve retornar 401');
    console.log('PASS [3/8] /mcp/client bloqueia sem chave / chave admin (401)');

    // 4. Handshake initialize + tools/list + chamada steos_dashboard_resumo e steos_listar_os em /mcp/admin
    const adminClient = new Client({ name: 'smoke-admin-client', version: '1.0.0' });
    const adminTransport = new StreamableHTTPClientTransport(
      new URL(`${primary.baseUrl}/mcp/admin`),
      {
        requestInit: {
          headers: { Authorization: `Bearer ${ADMIN_KEY}` },
        },
      }
    );
    await adminClient.connect(adminTransport);
    const adminToolsRes = await adminClient.listTools();
    const adminToolNames = adminToolsRes.tools.map((t) => t.name);
    assert.equal(adminToolNames.length, 29, 'Admin com write=true, fiscalWrite=false deve registrar 29 tools');
    assert.ok(adminToolNames.includes('steos_dashboard_resumo'));
    assert.ok(adminToolNames.includes('steos_listar_os'));
    assert.ok(!adminToolNames.includes('steos_emitir_nfse'), 'steos_emitir_nfse não pode existir com flag false');

    const dashCall = await adminClient.callTool({ name: 'steos_dashboard_resumo', arguments: {} });
    assert.ok(!dashCall.isError, `steos_dashboard_resumo falhou: ${JSON.stringify(dashCall)}`);
    const dashData = JSON.parse(dashCall.content[0].text);
    assert.equal(dashData.status, true);

    const osCall = await adminClient.callTool({ name: 'steos_listar_os', arguments: { page: 0, perPage: 5 } });
    assert.ok(!osCall.isError, `steos_listar_os falhou: ${JSON.stringify(osCall)}`);
    const osData = JSON.parse(osCall.content[0].text);
    assert.equal(osData.status, true);

    await adminClient.close();
    console.log('PASS [4/8] /mcp/admin initialize + tools/list (29) + steos_dashboard_resumo + steos_listar_os');

    // 5. Fiscal flag false (já verificado acima) + flag true sem confirm=true rejeitado!
    fiscalServer = await startHttpServerProcess({
      ...steosEnv,
      MCP_ENABLE_FISCAL_WRITE: 'true',
    });
    const fiscalClient = new Client({ name: 'smoke-fiscal-client', version: '1.0.0' });
    const fiscalTransport = new StreamableHTTPClientTransport(
      new URL(`${fiscalServer.baseUrl}/mcp/admin`),
      {
        requestInit: {
          headers: { Authorization: `Bearer ${ADMIN_KEY}` },
        },
      }
    );
    await fiscalClient.connect(fiscalTransport);
    const fiscalToolsRes = await fiscalClient.listTools();
    assert.equal(fiscalToolsRes.tools.length, 31, 'Admin com fiscalWrite=true deve ter 31 tools');
    const rejectedNfse = await fiscalClient.callTool({
      name: 'steos_emitir_nfse',
      arguments: { osId: 1, confirm: false },
    });
    assert.equal(rejectedNfse.isError, true, 'steos_emitir_nfse sem confirm:true deve ser rejeitada');
    await fiscalClient.close();
    console.log('PASS [5/8] steos_emitir_nfse ausente com flag=false e rejeitada sem confirm=true com flag=true');

    // 6. /mcp/client tools/list (10 tools) + auth inválido + auth válido + listar O.S.
    const portalClient = new Client({ name: 'smoke-portal-client', version: '1.0.0' });
    const portalTransport = new StreamableHTTPClientTransport(
      new URL(`${primary.baseUrl}/mcp/client`),
      {
        requestInit: {
          headers: { Authorization: `Bearer ${CLIENT_KEY}` },
        },
      }
    );
    await portalClient.connect(portalTransport);
    const portalToolsRes = await portalClient.listTools();
    assert.equal(portalToolsRes.tools.length, 10, '/mcp/client deve registrar exatamente 10 tools');

    const invalidAuth = await portalClient.callTool({
      name: 'steos_cliente_autenticar',
      arguments: { documento: '00000000000', telefone: '0000000000' },
    });
    assert.equal(invalidAuth.isError, true, 'steos_cliente_autenticar inválido deve falhar limpo (isError=true)');

    const docToTest = useReal ? process.env.SMOKE_TEST_CLIENT_DOC : '12345678909';
    const phoneToTest = useReal ? process.env.SMOKE_TEST_CLIENT_PHONE : '11999990000';

    if (docToTest && phoneToTest) {
      const validAuth = await portalClient.callTool({
        name: 'steos_cliente_autenticar',
        arguments: { documento: docToTest, telefone: phoneToTest },
      });
      assert.ok(!validAuth.isError, `steos_cliente_autenticar válido falhou: ${JSON.stringify(validAuth)}`);
      const authPayload = JSON.parse(validAuth.content[0].text);
      assert.equal(authPayload.authenticated, true);
      assert.ok(authPayload.session_token, 'session_token deve ser retornado');

      const clientOsList = await portalClient.callTool({
        name: 'steos_cliente_listar_os',
        arguments: { session_token: authPayload.session_token, page: 0, perPage: 5 },
      });
      assert.ok(!clientOsList.isError, `steos_cliente_listar_os falhou: ${JSON.stringify(clientOsList)}`);
      console.log('PASS [6/8] /mcp/client tools/list (10) + auth inválido + auth válido + steos_cliente_listar_os');
    } else {
      console.log(
        'PASS [6/8] /mcp/client tools/list (10) + auth inválido OK (fluxo válido SKIP: defina SMOKE_TEST_CLIENT_DOC e SMOKE_TEST_CLIENT_PHONE)'
      );
    }
    await portalClient.close();

    // 7. GET /sse/admin -> header text/event-stream + evento endpoint: /messages/admin?sessionId=...
    const sseAbort = new AbortController();
    const sseRes = await fetch(`${primary.baseUrl}/sse/admin`, {
      headers: { Authorization: `Bearer ${ADMIN_KEY}` },
      signal: sseAbort.signal,
    });
    assert.equal(sseRes.status, 200);
    assert.ok(
      (sseRes.headers.get('content-type') || '').includes('text/event-stream'),
      'GET /sse/admin deve retornar text/event-stream'
    );

    const reader = sseRes.body.getReader();
    const decoder = new TextDecoder();
    let sseChunk = '';
    while (!sseChunk.includes('sessionId=')) {
      const { value, done } = await reader.read();
      if (done) break;
      sseChunk += decoder.decode(value, { stream: true });
    }
    assert.ok(sseChunk.includes('event: endpoint'), 'SSE deve emitir event: endpoint');
    const sessionMatch = sseChunk.match(/data:\s*(\/messages\/admin\?sessionId=([^\s\r\n]+))/);
    assert.ok(sessionMatch, `Evento endpoint inesperado: ${sseChunk}`);

    // Garantia adicional de isolamento: sessionId de admin não pode funcionar em /messages/client
    const crossPost = await fetch(`${primary.baseUrl}/messages/client?sessionId=${encodeURIComponent(sessionMatch[2])}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${CLIENT_KEY}`,
      },
      body: JSON.stringify({ jsonrpc: '2.0', id: 1, method: 'tools/list' }),
    });
    assert.equal(crossPost.status, 404, 'sessionId de admin não pode ser aceita em /messages/client');

    sseAbort.abort();
    await reader.cancel().catch(() => {});
    console.log('PASS [7/8] GET /sse/admin -> text/event-stream + endpoint /messages/admin?sessionId=... e isolamento SSE');

    // 8. STDIO -> initialize + tools/list
    const stdioClient = new Client({ name: 'smoke-stdio-client', version: '1.0.0' });
    const stdioTransport = new StdioClientTransport({
      command: process.execPath,
      args: [path.join(MCP_ROOT_DIR, 'mcpServer.js')],
      cwd: MCP_ROOT_DIR,
      stderr: 'pipe',
      env: {
        ...process.env,
        ...steosEnv,
        MCP_ENABLE_WRITE: 'true',
        MCP_ENABLE_FISCAL_WRITE: 'false',
      },
    });
    await stdioClient.connect(stdioTransport);
    const stdioTools = await stdioClient.listTools();
    assert.equal(stdioTools.tools.length, 29, 'STDIO admin deve listar 29 tools');
    await stdioClient.close();
    console.log('PASS [8/8] STDIO initialize + tools/list (29 tools) sem corrupção de stdout');

    console.log('\nTodos os 8 smoke tests passaram com sucesso!');
  } finally {
    if (fiscalServer) await fiscalServer.stop();
    if (primary) await primary.stop();
    if (mockServer) {
      await new Promise((resolve) => mockServer.close(resolve));
    }
  }
}

main().catch((err) => {
  console.error('\n[SMOKE FAIL]', err);
  process.exit(1);
});
