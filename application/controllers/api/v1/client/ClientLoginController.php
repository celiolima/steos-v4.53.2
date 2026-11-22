<?php

/*
    Ivan Sarkozin
    https://github.com/sarkozin
*/

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ClientLoginController extends REST_Controller
{
    private const BOT_RATE_LIMIT_WINDOW = 900;

    private const BOT_RATE_LIMIT_MAX_ATTEMPTS = 5;

    private const BOT_TOKEN_TTL = 1800;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conecte_model');
        $this->load->model('Audit_model');
        $this->load->library('Authorization_Token');
    }

    public function login_post()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        $_POST = (array) json_decode((string) file_get_contents('php://input'), true);
        $email = (string) $this->input->post('email', true);
        $password = (string) $this->input->post('password', true);

        $cliente = $this->check_credentials($email);

        if (! $cliente) {
            $this->response([
                'result' => false,
                'message' => 'Usuário não encontrado.',
            ], REST_Controller::HTTP_UNAUTHORIZED);

            return;
        }

        if (! password_verify($password, (string) $cliente->senha)) {
            $this->response([
                'status' => false,
                'message' => 'Os dados de acesso estão incorretos.',
            ], REST_Controller::HTTP_UNAUTHORIZED);

            return;
        }

        $tokenData = [
            'uid' => $cliente->idClientes,
            'nome' => $cliente->nomeCliente,
            'email' => $cliente->email,
            'type' => 'client',
        ];

        $result = [
            'access_token' => $this->authorization_token->generateToken($tokenData),
        ];

        $ip = $this->input->ip_address() ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $log_data = [
            'usuario' => mb_substr((string) $cliente->nomeCliente, 0, 80),
            'tarefa' => mb_substr('Cliente ' . $cliente->nomeCliente . ' efetuou login pelo APP', 0, 255),
            'data' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'ip' => $ip,
        ];
        $this->Audit_model->add($log_data);

        $this->response([
            'status' => true,
            'message' => 'Login bem-sucedido',
            'result' => $result,
        ], REST_Controller::HTTP_OK);
    }

    public function auth_bot_post()
    {
        $expectedSecret = $_ENV['API_BOT_SECRET'] ?? getenv('API_BOT_SECRET') ?: '';
        $providedSecret = $this->extractHeader('X-Bot-Secret');

        if ($expectedSecret === '' || ! hash_equals((string) $expectedSecret, (string) $providedSecret)) {
            $this->response([
                'status' => false,
                'message' => 'Acesso não autorizado.',
            ], REST_Controller::HTTP_FORBIDDEN);
        }

        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (! is_array($payload)) {
            $payload = [];
        }

        $rawDocumento = isset($payload['documento']) && is_scalar($payload['documento']) ? (string) $payload['documento'] : '';
        $rawTelefone = isset($payload['telefone']) && is_scalar($payload['telefone']) ? (string) $payload['telefone'] : '';

        $docDigits = (string) preg_replace('/\D+/', '', $rawDocumento);
        $phoneDigits = (string) preg_replace('/\D+/', '', $rawTelefone);

        if (
            ! ((strlen($docDigits) === 11 || strlen($docDigits) === 14) && strlen($phoneDigits) >= 8)
            || preg_match('/^(\d)\1+$/', $docDigits) === 1
        ) {
            $this->response([
                'status' => false,
                'message' => 'Informe um documento (CPF/CNPJ) e telefone válidos.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $ip = $this->input->ip_address() ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $rlFile = rtrim(sys_get_temp_dir(), '/\\') . '/steos_bot_rl_' . sha1($ip . '|' . $docDigits) . '.json';
        $now = time();

        if ($this->isRateLimited($rlFile, $now)) {
            $this->response([
                'status' => false,
                'message' => 'Muitas tentativas inválidas. Aguarde 15 minutos.',
            ], REST_Controller::HTTP_TOO_MANY_REQUESTS);
        }

        $this->db->where("REPLACE(REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', ''), ' ', '') =", $docDigits);
        $this->db->limit(1);
        $cliente = $this->db->get('clientes')->row();

        $phoneMatches = $cliente
            && (
                $this->phonesMatch($phoneDigits, $cliente->celular ?? null)
                || $this->phonesMatch($phoneDigits, $cliente->telefone ?? null)
            );

        if (! $cliente || ! $phoneMatches) {
            $this->recordRateLimitFailure($rlFile, $now);

            $this->Audit_model->add([
                'usuario' => 'Bot/MCP',
                'tarefa' => 'Falha de autenticação Bot/MCP para documento ' . $this->maskDocument($docDigits),
                'data' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'ip' => $ip,
            ]);

            $this->response([
                'status' => false,
                'message' => 'Documento ou telefone não conferem com o cadastro.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $this->clearRateLimit($rlFile);

        $tokenData = [
            'uid' => $cliente->idClientes,
            'nome' => $cliente->nomeCliente,
            'email' => $cliente->email,
            'type' => 'client',
            'exp_ttl' => self::BOT_TOKEN_TTL,
        ];

        $token = $this->authorization_token->generateToken($tokenData);

        $this->Audit_model->add([
            'usuario' => mb_substr((string) $cliente->nomeCliente, 0, 80),
            'tarefa' => mb_substr('Cliente ' . $cliente->nomeCliente . ' autenticado via Bot/MCP', 0, 255),
            'data' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'ip' => $ip,
        ]);

        $this->response([
            'status' => true,
            'message' => 'Cliente autenticado com sucesso.',
            'result' => [
                'access_token' => $token,
                'expires_in' => self::BOT_TOKEN_TTL,
                'cliente' => [
                    'idClientes' => (int) $cliente->idClientes,
                    'nomeCliente' => $cliente->nomeCliente,
                    'email' => $cliente->email,
                ],
            ],
        ], REST_Controller::HTTP_OK);
    }

    private function check_credentials($email)
    {
        $this->db->where('email', $email);
        $this->db->limit(1);

        return $this->db->get('clientes')->row();
    }

    private function extractHeader(string $headerName): string
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
        if (! empty($_SERVER[$serverKey]) && is_string($_SERVER[$serverKey])) {
            return $_SERVER[$serverKey];
        }

        $headers = $this->input->request_headers();
        if (is_array($headers)) {
            $target = strtolower($headerName);
            foreach ($headers as $name => $value) {
                if (strtolower(trim((string) $name)) === $target && is_scalar($value)) {
                    return (string) $value;
                }
            }
        }

        return '';
    }

    private function phonesMatch(string $inputDigits, ?string $storedPhone): bool
    {
        $input = (string) preg_replace('/\D+/', '', $inputDigits);
        $stored = (string) preg_replace('/\D+/', '', (string) $storedPhone);

        if (strlen($input) < 8 || strlen($stored) < 8) {
            return false;
        }

        if (strlen($input) >= 12 && strpos($input, '55') === 0) {
            $input = substr($input, 2);
        }

        if (strlen($stored) >= 12 && strpos($stored, '55') === 0) {
            $stored = substr($stored, 2);
        }

        if (strlen($input) < 8 || strlen($stored) < 8) {
            return false;
        }

        if (strlen($input) >= 10 && strlen($stored) >= 10) {
            if (substr($input, 0, 2) !== substr($stored, 0, 2)) {
                return false;
            }
        }

        return substr($input, -8) === substr($stored, -8);
    }

    private function readRateLimitAttempts(string $rlFile, int $now): array
    {
        if (! is_file($rlFile)) {
            return [];
        }

        $raw = @file_get_contents($rlFile);
        if (! is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded) || ! isset($decoded['attempts']) || ! is_array($decoded['attempts'])) {
            return [];
        }

        $cutoff = $now - self::BOT_RATE_LIMIT_WINDOW;
        $valid = [];
        foreach ($decoded['attempts'] as $ts) {
            if (is_int($ts) || is_numeric($ts)) {
                $tsInt = (int) $ts;
                if ($tsInt >= $cutoff) {
                    $valid[] = $tsInt;
                }
            }
        }

        return $valid;
    }

    private function isRateLimited(string $rlFile, int $now): bool
    {
        return count($this->readRateLimitAttempts($rlFile, $now)) >= self::BOT_RATE_LIMIT_MAX_ATTEMPTS;
    }

    private function recordRateLimitFailure(string $rlFile, int $now): void
    {
        $attempts = $this->readRateLimitAttempts($rlFile, $now);
        $attempts[] = $now;
        @file_put_contents($rlFile, json_encode(['attempts' => $attempts]), LOCK_EX);
    }

    private function clearRateLimit(string $rlFile): void
    {
        if (is_file($rlFile)) {
            @unlink($rlFile);
        }
    }

    private function maskDocument(string $docDigits): string
    {
        $len = strlen($docDigits);
        if ($len <= 5) {
            return str_repeat('*', $len);
        }

        return substr($docDigits, 0, 3) . str_repeat('*', $len - 5) . substr($docDigits, -2);
    }
}
