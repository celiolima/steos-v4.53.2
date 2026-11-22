<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_granular_permissions extends CI_Migration
{
    public function up()
    {
        if (! $this->db->table_exists('permissoes')) {
            return;
        }

        $newKeys = [
            'vContrato', 'aContrato', 'eContrato', 'dContrato',
            'vNfse', 'aNfse', 'eNfse', 'dNfse',
            'vEquipamento', 'aEquipamento', 'eEquipamento', 'dEquipamento',
            'vSistema', 'aSistema', 'eSistema', 'dSistema',
            'vModelo', 'aModelo', 'eModelo', 'dModelo',
            'vTecnico', 'aTecnico', 'eTecnico', 'dTecnico',
            'vConta', 'aConta', 'eConta', 'dConta',
            'vCadFinanceiro', 'aCadFinanceiro', 'eCadFinanceiro', 'dCadFinanceiro',
            'rComissao', 'vLogsAsaas', 'cAsaas',
        ];

        $pagamentoKeys = ['aPagamento', 'ePagamento', 'dPagamento', 'vPagamento'];

        $rows = $this->db->get('permissoes')->result();
        foreach ($rows as $row) {
            $perms = json_decode((string) $row->permissoes, true);
            if (! is_array($perms)) {
                $unserialized = @unserialize((string) $row->permissoes);
                $perms = is_array($unserialized) ? $unserialized : [];
            }

            $isAdmin = ((int) $row->idPermissao === 1)
                || (strtoupper(trim((string) $row->nome)) === 'ADMINISTRADOR');

            if ($isAdmin) {
                foreach (array_merge($newKeys, $pagamentoKeys) as $key) {
                    $perms[$key] = '1';
                }
            } else {
                $inheritMap = [
                    'vContrato' => null,
                    'aContrato' => null,
                    'eContrato' => null,
                    'dContrato' => null,

                    'vNfse' => $this->permVal($perms, 'vLancamento'),
                    'aNfse' => $this->permVal($perms, 'aLancamento'),
                    'eNfse' => $this->permVal($perms, 'eLancamento'),
                    'dNfse' => $this->permVal($perms, 'dLancamento'),

                    'vConta' => $this->permVal($perms, 'vLancamento'),
                    'aConta' => $this->permVal($perms, 'aLancamento'),
                    'eConta' => $this->permVal($perms, 'eLancamento'),
                    'dConta' => $this->permVal($perms, 'dLancamento'),

                    'vCadFinanceiro' => $this->permVal($perms, 'vLancamento'),
                    'aCadFinanceiro' => $this->permVal($perms, 'aLancamento'),
                    'eCadFinanceiro' => $this->permVal($perms, 'eLancamento'),
                    'dCadFinanceiro' => $this->permVal($perms, 'dLancamento'),

                    'vLogsAsaas' => $this->permVal($perms, 'vLancamento'),
                    'cAsaas'     => $this->permVal($perms, 'cPermissao'),

                    'vTecnico' => $this->permVal($perms, 'cUsuario'),
                    'aTecnico' => $this->permVal($perms, 'cUsuario'),
                    'eTecnico' => $this->permVal($perms, 'cUsuario'),
                    'dTecnico' => $this->permVal($perms, 'cUsuario'),

                    'vEquipamento' => $this->permVal($perms, 'vOs'),
                    'aEquipamento' => $this->permVal($perms, 'aProduto'),
                    'eEquipamento' => $this->permVal($perms, 'eProduto'),
                    'dEquipamento' => $this->permVal($perms, 'dProduto'),

                    'vSistema' => $this->permVal($perms, 'vOs'),
                    'aSistema' => null,
                    'eSistema' => null,
                    'dSistema' => null,

                    'vModelo' => $this->permVal($perms, 'vGarantia'),
                    'aModelo' => $this->permVal($perms, 'aGarantia'),
                    'eModelo' => $this->permVal($perms, 'eGarantia'),
                    'dModelo' => $this->permVal($perms, 'dGarantia'),

                    'rComissao' => $this->permVal($perms, 'rFinanceiro'),
                ];

                foreach ($inheritMap as $key => $val) {
                    if (! array_key_exists($key, $perms)) {
                        $perms[$key] = $val;
                    }
                }
            }

            $this->db->where('idPermissao', $row->idPermissao);
            $this->db->update('permissoes', [
                'permissoes' => json_encode($perms),
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->table_exists('permissoes')) {
            return;
        }

        $newKeys = [
            'vContrato', 'aContrato', 'eContrato', 'dContrato',
            'vNfse', 'aNfse', 'eNfse', 'dNfse',
            'vEquipamento', 'aEquipamento', 'eEquipamento', 'dEquipamento',
            'vSistema', 'aSistema', 'eSistema', 'dSistema',
            'vModelo', 'aModelo', 'eModelo', 'dModelo',
            'vTecnico', 'aTecnico', 'eTecnico', 'dTecnico',
            'vConta', 'aConta', 'eConta', 'dConta',
            'vCadFinanceiro', 'aCadFinanceiro', 'eCadFinanceiro', 'dCadFinanceiro',
            'rComissao', 'vLogsAsaas', 'cAsaas',
        ];

        $rows = $this->db->get('permissoes')->result();
        foreach ($rows as $row) {
            $perms = json_decode((string) $row->permissoes, true);
            if (! is_array($perms)) {
                $unserialized = @unserialize((string) $row->permissoes);
                $perms = is_array($unserialized) ? $unserialized : [];
            }

            foreach ($newKeys as $key) {
                unset($perms[$key]);
            }

            $this->db->where('idPermissao', $row->idPermissao);
            $this->db->update('permissoes', [
                'permissoes' => json_encode($perms),
            ]);
        }
    }

    private function permVal(array $perms, string $sourceKey)
    {
        return ! empty($perms[$sourceKey]) && (string) $perms[$sourceKey] === '1' ? '1' : null;
    }
}
