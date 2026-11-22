import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
export const MCP_ROOT_DIR = path.resolve(__dirname, '..');

// Carrega mcp-server/.env sem sobrescrever variáveis já injetadas pelo ambiente/Docker
dotenv.config({
  path: path.resolve(MCP_ROOT_DIR, '.env'),
  override: false,
});

function parsePositiveInt(rawValue, fallback) {
  const parsed = Number.parseInt(String(rawValue ?? ''), 10);
  return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
}

function parseCorsOrigin(rawValue) {
  const value = String(rawValue ?? '*').trim();
  if (!value || value === '*') {
    return '*';
  }
  if (value.includes(',')) {
    const list = value
      .split(',')
      .map((item) => item.trim())
      .filter(Boolean);
    return list.length > 0 ? list : '*';
  }
  return value;
}

export function loadConfig(env = process.env) {
  return {
    port: parsePositiveInt(env.PORT, 3001),
    nodeEnv: String(env.NODE_ENV || 'development').trim(),
    steosBaseUrl: String(env.STEOS_BASE_URL || 'http://localhost:8080')
      .trim()
      .replace(/\/+$/, ''),
    steosServiceEmail: String(env.STEOS_SERVICE_EMAIL || '').trim(),
    steosServicePassword: String(env.STEOS_SERVICE_PASSWORD || ''),
    steosBotSecret: String(env.STEOS_BOT_SECRET || '').trim(),
    mcpAdminApiKey: String(env.MCP_ADMIN_API_KEY || '').trim(),
    mcpClientApiKey: String(env.MCP_CLIENT_API_KEY || '').trim(),
    enableWrite: String(env.MCP_ENABLE_WRITE || '').trim().toLowerCase() === 'true',
    enableFiscalWrite: String(env.MCP_ENABLE_FISCAL_WRITE || '').trim().toLowerCase() === 'true',
    corsOrigin: parseCorsOrigin(env.MCP_CORS_ORIGIN),
    rateLimitPerMin: parsePositiveInt(env.MCP_RATE_LIMIT_PER_MIN, 120),
  };
}

export const config = loadConfig();

export function assertHttpSecrets(cfg = config) {
  if (!cfg.mcpAdminApiKey || cfg.mcpAdminApiKey.length < 16) {
    throw new Error(
      'Configuração inválida: MCP_ADMIN_API_KEY é obrigatória e deve ter ao menos 16 caracteres.'
    );
  }
  if (!cfg.mcpClientApiKey || cfg.mcpClientApiKey.length < 16) {
    throw new Error(
      'Configuração inválida: MCP_CLIENT_API_KEY é obrigatória e deve ter ao menos 16 caracteres.'
    );
  }
  if (cfg.mcpAdminApiKey === cfg.mcpClientApiKey) {
    throw new Error(
      'Configuração inválida: MCP_ADMIN_API_KEY e MCP_CLIENT_API_KEY devem ser estritamente diferentes entre si.'
    );
  }
}
