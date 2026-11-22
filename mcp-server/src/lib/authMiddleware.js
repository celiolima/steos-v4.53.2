import crypto from 'node:crypto';

const SENSITIVE_KEY_RE =
  /^(authorization|x-api-key|x-steos-token|x-client-token|x-bot-secret|token|access_token|session_token|password|senha|secret|api_?key)$/i;

/**
 * Compara duas strings em tempo constante via digest SHA-256
 * para não vazar comprimento nem prefixos via timing attack.
 */
export function timingSafeEqualStr(a, b) {
  if (typeof a !== 'string' || typeof b !== 'string') {
    return false;
  }
  const hashA = crypto.createHash('sha256').update(a, 'utf8').digest();
  const hashB = crypto.createHash('sha256').update(b, 'utf8').digest();
  return crypto.timingSafeEqual(hashA, hashB);
}

/**
 * Extrai chave de API de Authorization: Bearer <key> ou x-api-key.
 */
export function extractApiKey(req) {
  const authHeader =
    typeof req.header === 'function'
      ? req.header('authorization')
      : req.headers?.authorization;

  if (typeof authHeader === 'string') {
    const match = authHeader.match(/^Bearer\s+(.+)$/i);
    if (match && match[1]) {
      return match[1].trim();
    }
  }

  const xApiKey =
    typeof req.header === 'function'
      ? req.header('x-api-key')
      : req.headers?.['x-api-key'];

  if (Array.isArray(xApiKey)) {
    return String(xApiKey[0] || '').trim();
  }
  if (typeof xApiKey === 'string') {
    return xApiKey.trim();
  }

  return '';
}

/**
 * Cria middleware de autenticação dedicado para um único escopo ('admin' ou 'client').
 */
export function createApiKeyMiddleware(expectedKey, scopeName) {
  const normalizedExpected = typeof expectedKey === 'string' ? expectedKey.trim() : '';

  return function apiKeyAuthMiddleware(req, res, next) {
    if (!normalizedExpected) {
      return res.status(401).json({
        error: 'Unauthorized',
        message: `Autenticação do escopo "${scopeName}" não configurada no servidor.`,
      });
    }

    const providedKey = extractApiKey(req);
    if (!providedKey || !timingSafeEqualStr(providedKey, normalizedExpected)) {
      return res.status(401).json({
        error: 'Unauthorized',
        message: `Chave de API ausente ou inválida para o escopo "${scopeName}".`,
      });
    }

    return next();
  };
}

/**
 * Rate limiter em memória por IP + escopo com limpeza automática de janelas expiradas.
 */
export function createRateLimiter({ windowMs = 60_000, max = 120, scope = 'default' } = {}) {
  const effectiveWindowMs = Number.isFinite(windowMs) && windowMs > 0 ? windowMs : 60_000;
  const effectiveMax = Number.isFinite(max) && max > 0 ? max : 120;
  const buckets = new Map();

  const cleanupInterval = setInterval(() => {
    const now = Date.now();
    for (const [key, entry] of buckets.entries()) {
      if (now >= entry.resetAt) {
        buckets.delete(key);
      }
    }
  }, Math.max(effectiveWindowMs, 30_000));

  if (typeof cleanupInterval.unref === 'function') {
    cleanupInterval.unref();
  }

  return function rateLimiterMiddleware(req, res, next) {
    const now = Date.now();
    const clientIp = req.ip || req.socket?.remoteAddress || 'unknown';
    const bucketKey = `${clientIp}::${scope}`;

    let entry = buckets.get(bucketKey);
    if (!entry || now >= entry.resetAt) {
      entry = { count: 0, resetAt: now + effectiveWindowMs };
      buckets.set(bucketKey, entry);
    }

    entry.count += 1;
    const remaining = Math.max(0, effectiveMax - entry.count);
    const retryAfterSec = Math.max(1, Math.ceil((entry.resetAt - now) / 1000));

    res.setHeader('X-RateLimit-Limit', String(effectiveMax));
    res.setHeader('X-RateLimit-Remaining', String(remaining));
    res.setHeader('X-RateLimit-Reset', String(Math.ceil(entry.resetAt / 1000)));

    if (entry.count > effectiveMax) {
      res.setHeader('Retry-After', String(retryAfterSec));
      return res.status(429).json({
        error: 'Too Many Requests',
        message: `Limite de ${effectiveMax} requisições por minuto excedido para o escopo "${scope}".`,
      });
    }

    return next();
  };
}

function redactSensitiveString(str) {
  return str
    .replace(/Bearer\s+[A-Za-z0-9\-._~+/]+=*/gi, 'Bearer [REDACTED]')
    .replace(/\beyJ[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\b/g, '[JWT_REDACTED]')
    .replace(/\b\d{3}\.\d{3}\.\d{3}-\d{2}\b/g, '***.***.***-**')
    .replace(/\b\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}\b/g, '**.***.***/****-**')
    .replace(/\b\d{14}\b/g, '**************')
    .replace(/\b\d{11}\b/g, '***********');
}

function sanitizeLogValue(value, depth = 0) {
  if (depth > 4) {
    return '[TRUNCATED]';
  }
  if (value === null || value === undefined) {
    return value;
  }
  if (typeof value === 'string') {
    const redacted = redactSensitiveString(value);
    return redacted.length > 300 ? `${redacted.slice(0, 300)}...` : redacted;
  }
  if (typeof value === 'number' || typeof value === 'boolean') {
    return value;
  }
  if (Array.isArray(value)) {
    return value.slice(0, 20).map((item) => sanitizeLogValue(item, depth + 1));
  }
  if (typeof value === 'object') {
    const out = {};
    for (const [key, val] of Object.entries(value)) {
      if (SENSITIVE_KEY_RE.test(key)) {
        out[key] = '[REDACTED]';
      } else {
        out[key] = sanitizeLogValue(val, depth + 1);
      }
    }
    return out;
  }
  return String(value);
}

/**
 * Grava auditoria estruturada em JSON exclusivamente em stderr
 * para nunca corromper o stream JSON-RPC em stdout do modo STDIO.
 */
export function logToolExecution({ scope, tool, status, durationMs, detail }) {
  const payload = {
    ts: new Date().toISOString(),
    level: status === 'error' ? 'error' : 'info',
    scope: scope || 'unknown',
    tool: tool || 'unknown',
    status: status || 'ok',
    durationMs: Number.isFinite(durationMs) ? Math.round(durationMs) : 0,
  };

  if (detail !== undefined) {
    payload.detail = sanitizeLogValue(detail);
  }

  process.stderr.write(`${JSON.stringify(payload)}\n`);
}
