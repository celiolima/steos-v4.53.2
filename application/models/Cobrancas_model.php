<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cobrancas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _applyCobrancasFilters($where = [])
    {
        if (empty($where) || !is_array($where)) {
            return;
        }

        // 1. Pesquisa por Nome do Cliente / Documento
        if (!empty($where['pesquisa'])) {
            $this->db->group_start();
            $this->db->like('clientes.nomeCliente', $where['pesquisa']);
            $this->db->or_like('clientes.documento', $where['pesquisa']);
            $this->db->group_end();
        }

        // 2. Ordem de Serviço
        if (!empty($where['os_id'])) {
            $os_clean = preg_replace('/[^0-9]/', '', $where['os_id']);
            if (!empty($os_clean)) {
                $this->db->where('cobrancas.os_id', $os_clean);
            }
        }

        // 3. Venda / Fatura
        if (!empty($where['vendas_id'])) {
            $venda_clean = preg_replace('/[^0-9]/', '', $where['vendas_id']);
            if (!empty($venda_clean)) {
                $this->db->where('cobrancas.vendas_id', $venda_clean);
            }
        }

        // 4. Tipo (payment_method)
        if (!empty($where['tipo']) && $where['tipo'] !== 'Todos') {
            $this->db->where('cobrancas.payment_method', $where['tipo']);
        }

        // 5. Status
        if (!empty($where['status']) && $where['status'] !== 'Todos') {
            $this->db->where('cobrancas.status', $where['status']);
        }

        // 6. Período de Vencimento (expire_at)
        if (!empty($where['data_de'])) {
            $this->db->where('cobrancas.expire_at >=', $where['data_de']);
        }
        if (!empty($where['data_ate'])) {
            $this->db->where('cobrancas.expire_at <=', $where['data_ate']);
        }

        // 7. Valor Mínimo / Máximo (em centavos no banco)
        if (!empty($where['valor_de'])) {
            $this->db->where('cobrancas.total >=', $where['valor_de']);
        }
        if (!empty($where['valor_ate'])) {
            $this->db->where('cobrancas.total <=', $where['valor_ate']);
        }
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ', clientes.nomeCliente as cliente_nome, clientes.idClientes');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id', 'left');
        $this->db->limit($perpage, $start);
        $this->db->order_by('cobrancas.idCobranca', 'desc');
        if ($where) {
            if (is_array($where)) {
                $this->_applyCobrancasFilters($where);
            } else {
                $this->db->where($where);
            }
        }

        $query = $this->db->get();
        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('cobrancas.*, clientes.*');
        $this->db->from('cobrancas');
        $this->db->where('cobrancas.idCobranca', $id);
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByOs($id)
    {
        $this->db->select('cobrancas.*, clientes.*, os.*');
        $this->db->distinct();
        $this->db->from('cobrancas');
        $this->db->join('os', 'os.idOs = cobrancas.os_id');
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id', 'left');
        $this->db->where('cobrancas.charge_id', $id);

        return $this->db->get()->row();
    }

    public function getByVendas($id)
    {
        $this->db->select('cobrancas.*, clientes.*, vendas.*');
        $this->db->distinct();
        $this->db->from('cobrancas');
        $this->db->join('vendas', 'vendas.idVendas = cobrancas.vendas_id');
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id', 'left');
        $this->db->where('cobrancas.charge_id', $id);

        return $this->db->get()->row();
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            if ($returnId == true) {
                return $this->db->insert_id($table);
            }

            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table, $where = '')
    {
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = cobrancas.clientes_id', 'left');
        if ($where) {
            if (is_array($where)) {
                $this->_applyCobrancasFilters($where);
            } else {
                $this->db->where($where);
            }
        }
        return $this->db->count_all_results();
    }

    public function atualizarStatus($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

        $result = $this->PaymentGateway->atualizarDados($cobranca->idCobranca);

        return $result;
    }

    public function confirmarPagamento($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

        $result = $this->PaymentGateway->confirmarPagamento($cobranca->idCobranca);

        return $result;
    }

    public function cancelarPagamento($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

        $result = $this->PaymentGateway->cancelar($cobranca->idCobranca);

        return $result;
    }

    public function enviarEmail($idCobranca)
    {
        $cobranca = $this->getById($idCobranca);
        if (empty($cobranca)) {
            return $this->session->set_flashdata('error', 'Cobrança não existe!');
        }

        $gatewayDePagamento = $cobranca->payment_gateway;
        $this->load->library("Gateways/$gatewayDePagamento", null, 'PaymentGateway');

        $result = $this->PaymentGateway->enviarPorEmail($cobranca->idCobranca);

        return $result;
    }
}
