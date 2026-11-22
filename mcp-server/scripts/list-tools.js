import { Client } from '@modelcontextprotocol/sdk/client/index.js';
import { InMemoryTransport } from '@modelcontextprotocol/sdk/inMemory.js';
import { config as envConfig } from '../src/config.js';
import { createAdminServer, createClientServer } from '../mcpServer.js';

async function listToolsForServer(server, label) {
  const [clientTransport, serverTransport] = InMemoryTransport.createLinkedPair();
  const client = new Client({
    name: `steos-list-tools-${label}`,
    version: '1.0.0',
  });

  await server.connect(serverTransport);
  await client.connect(clientTransport);

  const response = await client.listTools();

  await client.close();
  await server.close();

  return response.tools || [];
}

async function main() {
  const adminCurrent = await listToolsForServer(
    createAdminServer({ configOverride: envConfig }),
    'admin-current'
  );
  const adminWave1 = await listToolsForServer(
    createAdminServer({
      configOverride: { ...envConfig, enableWrite: false, enableFiscalWrite: false },
    }),
    'admin-wave1'
  );
  const adminWave12 = await listToolsForServer(
    createAdminServer({
      configOverride: { ...envConfig, enableWrite: true, enableFiscalWrite: false },
    }),
    'admin-wave1-2'
  );
  const adminAll = await listToolsForServer(
    createAdminServer({
      configOverride: { ...envConfig, enableWrite: true, enableFiscalWrite: true },
    }),
    'admin-all'
  );
  const clientTools = await listToolsForServer(
    createClientServer({ configOverride: envConfig }),
    'client'
  );

  const wave1Names = new Set(adminWave1.map((t) => t.name));
  const wave2Names = new Set(
    adminWave12.filter((t) => !wave1Names.has(t.name)).map((t) => t.name)
  );
  const wave3Names = new Set(
    adminAll
      .filter((t) => !wave1Names.has(t.name) && !wave2Names.has(t.name))
      .map((t) => t.name)
  );

  console.log('=== STEOS MCP Server — Inventário de Tools ===');
  console.log(
    `Ambiente atual (.env): MCP_ENABLE_WRITE=${envConfig.enableWrite} | MCP_ENABLE_FISCAL_WRITE=${envConfig.enableFiscalWrite}`
  );
  console.log(`Admin (configuração atual): ${adminCurrent.length} tools`);
  console.log('');

  console.log(`--- Admin Onda 1 (Leitura): ${wave1Names.size} tools ---`);
  for (const tool of adminWave1) {
    console.log(`  - ${tool.name} (${tool.title || ''})`);
  }
  console.log('');

  console.log(`--- Admin Onda 2 (Escrita Operacional): ${wave2Names.size} tools ---`);
  for (const tool of adminWave12.filter((t) => wave2Names.has(t.name))) {
    console.log(`  - ${tool.name} (${tool.title || ''})`);
  }
  console.log('');

  console.log(`--- Admin Onda 3 (Fiscal Crítica): ${wave3Names.size} tools ---`);
  for (const tool of adminAll.filter((t) => wave3Names.has(t.name))) {
    console.log(`  - ${tool.name} (${tool.title || ''})`);
  }
  console.log('');

  console.log(`--- Portal do Cliente (/mcp/client): ${clientTools.length} tools ---`);
  for (const tool of clientTools) {
    console.log(`  - ${tool.name} (${tool.title || ''})`);
  }
  console.log('');

  console.log('=== Resumo de Contagem ===');
  console.log(`Onda 1 (Leitura):            ${wave1Names.size}`);
  console.log(`Onda 2 (Escrita):            ${wave2Names.size}`);
  console.log(`Onda 3 (Fiscal):             ${wave3Names.size}`);
  console.log(`Total Admin Completo:        ${adminAll.length}`);
  console.log(`Total Cliente (/mcp/client): ${clientTools.length}`);

  if (
    wave1Names.size !== 23 ||
    wave2Names.size !== 6 ||
    wave3Names.size !== 2 ||
    adminAll.length !== 31 ||
    clientTools.length !== 10
  ) {
    console.error('ERRO: Contagem de tools divergente do esperado (23 / 6 / 2 / 31 / 10).');
    process.exit(1);
  }
}

main().catch((err) => {
  console.error('Erro ao listar tools MCP:', err);
  process.exit(1);
});
