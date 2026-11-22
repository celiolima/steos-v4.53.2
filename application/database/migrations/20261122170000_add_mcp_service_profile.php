<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_mcp_service_profile extends CI_Migration
{
    public function up()
    {
        if (! $this->db->table_exists('permissoes') || ! $this->db->table_exists('usuarios')) {
            return;
        }

        $allKeys = [
            'aCliente', 'eCliente', 'dCliente', 'vCliente',
            'aProduto', 'eProduto', 'dProduto', 'vProduto',
            'aServico', 'eServico', 'dServico', 'vServico',
            'aOs', 'eOs', 'dOs', 'vOs',
            'aVenda', 'eVenda', 'dVenda', 'vVenda',
            'aArquivo', 'eArquivo', 'dArquivo', 'vArquivo',
            'aPagamento', 'ePagamento', 'dPagamento', 'vPagamento',
            'aLancamento', 'eLancamento', 'dLancamento', 'vLancamento',
            'cUsuario', 'cEmitente', 'cPermissao', 'cBackup', 'cAuditoria', 'cEmail', 'cSistema',
            'rCliente', 'rProduto', 'rServico', 'rOs', 'rVenda', 'rFinanceiro', 'rComissao',
            'vGarantia', 'aGarantia', 'eGarantia', 'dGarantia',
            'aCobranca', 'eCobranca', 'dCobranca', 'vCobranca',
            'aVeiculo', 'eVeiculo', 'dVeiculo', 'vVeiculo',
            'vContrato', 'aContrato', 'eContrato', 'dContrato',
            'vNfse', 'aNfse', 'eNfse', 'dNfse',
            'vEquipamento', 'aEquipamento', 'eEquipamento', 'dEquipamento',
            'vSistema', 'aSistema', 'eSistema', 'dSistema',
            'vModelo', 'aModelo', 'eModelo', 'dModelo',
            'vTecnico', 'aTecnico', 'eTecnico', 'dTecnico',
            'vConta', 'aConta', 'eConta', 'dConta',
            'vCadFinanceiro', 'aCadFinanceiro', 'eCadFinanceiro', 'dCadFinanceiro',
            'vLogsAsaas', 'cAsaas',
        ];

        $enabledKeys = [
            'vCliente', 'vProduto', 'vServico', 'vOs', 'vVenda',
            'vGarantia', 'vArquivo', 'vLancamento', 'vCobranca', 'vVeiculo',
            'vContrato', 'vNfse', 'vEquipamento', 'vSistema', 'vModelo',
            'vTecnico', 'vConta', 'vCadFinanceiro', 'vLogsAsaas', 'vPagamento',
            'aCliente', 'eCliente', 'aOs', 'eOs',
            'aNfse', 'eNfse', 'dNfse',
        ];

        $perms = array_fill_keys($allKeys, null);
        foreach ($enabledKeys as $key) {
            if (! array_key_exists($key, $perms)) {
                throw new RuntimeException('Chave de permissão desconhecida: ' . $key);
            }
            $perms[$key] = '1';
        }

        if (count($perms) !== 92) {
            throw new RuntimeException('Número inválido de chaves de permissão: ' . count($perms));
        }

        $existingPerm = $this->db->where('nome', 'API MCP Admin')->limit(1)->get('permissoes')->row();
        if ($existingPerm) {
            $permissaoId = (int) $existingPerm->idPermissao;
        } else {
            $this->db->insert('permissoes', [
                'nome' => 'API MCP Admin',
                'permissoes' => json_encode($perms),
                'situacao' => 1,
                'data' => date('Y-m-d'),
            ]);
            $permissaoId = (int) $this->db->insert_id();
        }

        if ($permissaoId <= 0) {
            return;
        }

        $existingUser = $this->db->where('email', 'mcp.service@stesistemas.com')->limit(1)->get('usuarios')->row();
        if (! $existingUser) {
            $rawPwd = ! empty($_ENV['MCP_SERVICE_INIT_PASSWORD'])
                ? (string) $_ENV['MCP_SERVICE_INIT_PASSWORD']
                : bin2hex(random_bytes(24));

            $this->db->insert('usuarios', [
                'nome' => 'Agente MCP STEOS',
                'rg' => '',
                'cpf' => '00000000000',
                'cep' => '00000-000',
                'rua' => 'Integração MCP',
                'numero' => 'S/N',
                'bairro' => 'Sistema',
                'cidade' => 'Sistema',
                'estado' => 'SP',
                'email' => 'mcp.service@stesistemas.com',
                'senha' => password_hash($rawPwd, PASSWORD_DEFAULT),
                'telefone' => '0000000000',
                'celular' => '',
                'situacao' => 1,
                'dataCadastro' => date('Y-m-d'),
                'permissoes_id' => $permissaoId,
                'dataExpiracao' => '2035-12-31',
                'tecnico' => '0',
            ]);
        }
    }

    public function down()
    {
        if ($this->db->table_exists('usuarios')) {
            $this->db->where('email', 'mcp.service@stesistemas.com');
            $this->db->delete('usuarios');
        }

        if ($this->db->table_exists('permissoes')) {
            $this->db->where('nome', 'API MCP Admin');
            $this->db->where('idPermissao !=', 1);
            $this->db->delete('permissoes');
        }
    }
}
