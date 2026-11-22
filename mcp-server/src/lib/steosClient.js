export class SteosHttpError extends Error {
  constructor(status, message, payload = null) {
    super(message || `Erro HTTP ${status} na API STEOS`);
    this.name = 'SteosHttpError';
    this.status = status;
    this.payload = payload;
  }
}

/**
 * Converte data YYYY-MM-DD para DD/MM/YYYY (exigido por OsController::index_post/index_put).
 * Mantém valores já em DD/MM/YYYY ou vazios intactos.
 */
export function toSteosDateBR(dateStr) {
  if (dateStr === undefined || dateStr === null) {
    return '';
  }
  const trimmed = String(dateStr).trim();
  if (!trimmed) {
    return '';
  }
  const isoMatch = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})(?:[T\s].*)?$/);
  if (isoMatch) {
    return `${isoMatch[3]}/${isoMatch[2]}/${isoMatch[1]}`;
  }
  return trimmed;
}

function buildApiUrl(baseUrl, path, query) {
  const normalizedBase = String(baseUrl || '').replace(/\/+$/, '');
  const cleanPath = String(path || '').replace(/^\/+/, '');
  const basePath = `${normalizedBase}/index.php/api/v1${cleanPath ? `/${cleanPath}` : ''}`;

  if (!query || typeof query !== 'object') {
    return basePath;
  }

  const params = new URLSearchParams();
  for (const [key, value] of Object.entries(query)) {
    if (value !== undefined && value !== null && value !== '') {
      params.set(key, String(value));
    }
  }

  const qs = params.toString();
  return qs ? `${basePath}?${qs}` : basePath;
}

async function parseResponseBody(response) {
  const rawText = await response.text();
  if (!rawText) {
    return null;
  }
  try {
    return JSON.parse(rawText);
  } catch {
    return {
      status: false,
      message: `Resposta não-JSON da API STEOS (HTTP ${response.status}).`,
      rawText: rawText.slice(0, 500),
    };
  }
}

function extractErrorMessage(payload, fallbackStatus) {
  if (payload && typeof payload === 'object') {
    if (typeof payload.message === 'string' && payload.message.trim()) {
      return payload.message.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    }
    if (typeof payload.error === 'string' && payload.error.trim()) {
      return payload.error.trim();
    }
  }
  return `Falha na requisição à API STEOS (HTTP ${fallbackStatus}).`;
}

function isTokenExpiredResponse(status, payload) {
  if (status === 403) {
    return true;
  }
  if (status !== 401) {
    return false;
  }
  const msg = extractErrorMessage(payload, status);
  return /token|login|expir|forbidden|n[aã]o definida/i.test(msg);
}

/**
 * Cliente administrativo da API v1 do STEOS (/api/v1/* exceto /client*).
 * Possui cache de token de serviço com lock contra thundering herd.
 */
export class SteosAdminClient {
  constructor({ baseUrl, serviceEmail, servicePassword }) {
    this.baseUrl = String(baseUrl || '').replace(/\/+$/, '');
    this.serviceEmail = serviceEmail || '';
    this.servicePassword = servicePassword || '';
    this.serviceToken = null;
    this.obtainedAt = 0;
    this.loginPromise = null;
    this.refreshPromise = null;
  }

  async loginService() {
    if (this.loginPromise) {
      return this.loginPromise;
    }

    this.loginPromise = (async () => {
      if (!this.serviceEmail || !this.servicePassword) {
        throw new SteosHttpError(
          500,
          'Credenciais de serviço STEOS_SERVICE_EMAIL / STEOS_SERVICE_PASSWORD não configuradas no mcp-server.'
        );
      }

      const url = buildApiUrl(this.baseUrl, 'login');
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          email: this.serviceEmail,
          password: this.servicePassword,
        }),
        signal: AbortSignal.timeout(25_000),
      });

      const payload = await parseResponseBody(response);
      const token = payload?.result?.access_token;

      if (!response.ok || !token) {
        this.serviceToken = null;
        this.obtainedAt = 0;
        throw new SteosHttpError(
          response.status || 401,
          extractErrorMessage(payload, response.status || 401),
          payload
        );
      }

      this.serviceToken = token;
      this.obtainedAt = Date.now();
      return token;
    })();

    try {
      return await this.loginPromise;
    } finally {
      this.loginPromise = null;
    }
  }

  async refreshServiceToken() {
    if (this.refreshPromise) {
      return this.refreshPromise;
    }

    this.refreshPromise = (async () => {
      if (!this.serviceToken) {
        return await this.loginService();
      }

      try {
        const url = buildApiUrl(this.baseUrl, 'reGenToken');
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${this.serviceToken}`,
          },
          signal: AbortSignal.timeout(25_000),
        });

        const payload = await parseResponseBody(response);
        const newToken = payload?.result?.access_token;

        if (response.ok && newToken) {
          this.serviceToken = newToken;
          this.obtainedAt = Date.now();
          return newToken;
        }
      } catch {
        // Fallback para novo login completo abaixo
      }

      this.serviceToken = null;
      this.obtainedAt = 0;
      return await this.loginService();
    })();

    try {
      return await this.refreshPromise;
    } finally {
      this.refreshPromise = null;
    }
  }

  async getServiceToken() {
    if (this.serviceToken) {
      return this.serviceToken;
    }
    return await this.loginService();
  }

  async executeFetch({ method, url, token, body, timeoutMs }) {
    const headers = {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    };

    const init = {
      method,
      headers,
      signal: AbortSignal.timeout(timeoutMs),
    };

    if (body !== undefined && method !== 'GET' && method !== 'HEAD') {
      headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(body);
    }

    const response = await fetch(url, init);
    const payload = await parseResponseBody(response);
    return { response, payload };
  }

  async request({ method = 'GET', path = '', query, body, overrideToken, timeoutMs } = {}) {
    const cleanPath = String(path || '').replace(/^\/+/, '');
    if (cleanPath === 'client' || cleanPath.startsWith('client/')) {
      throw new Error('SteosAdminClient não deve acessar rotas do Portal do Cliente (/client*).');
    }

    const normalizedMethod = String(method || 'GET').toUpperCase();
    const effectiveTimeoutMs =
      Number.isFinite(timeoutMs) && timeoutMs > 0
        ? timeoutMs
        : cleanPath === 'nfse' || cleanPath.startsWith('nfse/')
          ? 45_000
          : 25_000;

    const url = buildApiUrl(this.baseUrl, cleanPath, query);
    const hasOverrideToken = typeof overrideToken === 'string' && overrideToken.trim() !== '';

    const token = hasOverrideToken ? overrideToken.trim() : await this.getServiceToken();
    let { response, payload } = await this.executeFetch({
      method: normalizedMethod,
      url,
      token,
      body,
      timeoutMs: effectiveTimeoutMs,
    });

    // Retry único apenas quando estiver usando o usuário de serviço (nunca com overrideToken)
    if (!hasOverrideToken && !response.ok && isTokenExpiredResponse(response.status, payload)) {
      const refreshedToken = await this.refreshServiceToken();
      ({ response, payload } = await this.executeFetch({
        method: normalizedMethod,
        url,
        token: refreshedToken,
        body,
        timeoutMs: effectiveTimeoutMs,
      }));
    }

    if (!response.ok) {
      throw new SteosHttpError(
        response.status,
        extractErrorMessage(payload, response.status),
        payload
      );
    }

    return payload;
  }
}

/**
 * Cliente isolado do Portal do Cliente (/api/v1/client*).
 * JAMAIS usa credenciais staff nem endpoints fora de /client*.
 */
export class SteosClientPortalClient {
  constructor({ baseUrl, botSecret }) {
    this.baseUrl = String(baseUrl || '').replace(/\/+$/, '');
    this.botSecret = botSecret || '';
  }

  async authenticateByDocument(documento, telefone) {
    if (!this.botSecret) {
      throw new SteosHttpError(
        500,
        'STEOS_BOT_SECRET não configurado no servidor MCP para autenticação por documento/telefone.'
      );
    }

    const url = buildApiUrl(this.baseUrl, 'client/auth-bot');
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Bot-Secret': this.botSecret,
      },
      body: JSON.stringify({ documento, telefone }),
      signal: AbortSignal.timeout(25_000),
    });

    const payload = await parseResponseBody(response);
    if (!response.ok) {
      throw new SteosHttpError(
        response.status,
        extractErrorMessage(payload, response.status),
        payload
      );
    }

    return payload;
  }

  async authenticateByEmail(email, password) {
    const url = buildApiUrl(this.baseUrl, 'client/auth');
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({ email, password }),
      signal: AbortSignal.timeout(25_000),
    });

    const payload = await parseResponseBody(response);
    if (!response.ok) {
      throw new SteosHttpError(
        response.status,
        extractErrorMessage(payload, response.status),
        payload
      );
    }

    return payload;
  }

  async request({ clientToken, method = 'GET', path = 'client', query, body, timeoutMs = 25_000 } = {}) {
    const trimmedToken = typeof clientToken === 'string' ? clientToken.trim() : '';
    if (!trimmedToken) {
      throw new SteosHttpError(
        401,
        'Sessão do cliente ausente ou expirada. Chame primeiro a tool "steos_cliente_autenticar" (documento + telefone) ou "steos_cliente_autenticar_email".'
      );
    }

    const cleanPath = String(path || '').replace(/^\/+/, '');
    if (cleanPath !== 'client' && !cleanPath.startsWith('client/')) {
      throw new Error(
        `Acesso negado: SteosClientPortalClient só pode chamar endpoints /client*, recebido "${cleanPath}".`
      );
    }

    const normalizedMethod = String(method || 'GET').toUpperCase();
    const url = buildApiUrl(this.baseUrl, cleanPath, query);
    const headers = {
      Accept: 'application/json',
      Authorization: `Bearer ${trimmedToken}`,
    };

    const init = {
      method: normalizedMethod,
      headers,
      signal: AbortSignal.timeout(timeoutMs),
    };

    if (body !== undefined && normalizedMethod !== 'GET' && normalizedMethod !== 'HEAD') {
      headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(body);
    }

    const response = await fetch(url, init);
    const payload = await parseResponseBody(response);

    if (!response.ok) {
      throw new SteosHttpError(
        response.status,
        extractErrorMessage(payload, response.status),
        payload
      );
    }

    return payload;
  }
}
