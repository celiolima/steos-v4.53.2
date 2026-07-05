<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conecte_model extends CI_Model
{
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

    public function getLastOs($cliente)
    {
        $this->db->select('os.*, usuarios.nome, COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos, COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos', false);
        $this->db->from('os');
        $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');
        $this->db->where('clientes_id', $cliente);
        $this->db->limit(10);
        $this->db->order_by('idOs', 'desc');

        return $this->db->get()->result();
    }

    public function getLastCompras($cliente)
    {
        $this->db->select('vendas.*,usuarios.nome');
        $this->db->from('vendas');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
        $this->db->order_by('idVendas', 'desc');
        $this->db->where('clientes_id', $cliente);
        $this->db->limit(10);
        $this->db->order_by('idVendas', 'desc');

        return $this->db->get()->result();
    }

    public function getCompras($table, $fields, $where, $perpage, $start, $one, $array, $cliente)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->join('usuarios', 'vendas.usuarios_id = usuarios.idUsuarios', 'left');
        $this->db->order_by('idVendas', 'desc');
        $this->db->where('clientes_id', $cliente);
        $this->db->limit($perpage, $start);
        $this->db->order_by('idVendas', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getCobrancas($table, $fields, $where, $perpage, $start, $one, $array, $cliente)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->join('clientes', 'cobrancas.clientes_id = clientes.idClientes', 'left');
        $this->db->where('clientes_id', $cliente);
        $this->db->order_by('expire_at', 'desc');
        $this->db->limit($perpage, $start);
        $this->db->order_by('idCobranca', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    protected function _applyOsFilters($where = [])
    {
        if (empty($where) || !is_array($where)) {
            return;
        }

        // 1. Número da OS
        if (!empty($where['os'])) {
            $os_clean = preg_replace('/[^0-9,]/', '', $where['os']);
            if (strpos($os_clean, ',') !== false) {
                $os_arr = array_filter(explode(',', $os_clean));
                if (!empty($os_arr)) {
                    $this->db->where_in('os.idOs', $os_arr);
                }
            } elseif (!empty($os_clean)) {
                $this->db->where('os.idOs', $os_clean);
            }
        }

        // 2. Técnico / Responsável
        if (!empty($where['tecnico']) && $where['tecnico'] !== 'Todos' && $where['tecnico'] !== 'Todos técnicos') {
            $this->db->where("(usuarios.nome = " . $this->db->escape($where['tecnico']) . " OR EXISTS (SELECT 1 FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs AND tecnicos_os.tecnicoName = " . $this->db->escape($where['tecnico']) . "))", null, false);
        }

        // 3. Período (Data Inicial e Final)
        if (!empty($where['de'])) {
            $de = $where['de'];
            if (strpos($de, ':') === false) {
                $de .= ' 00:00:00';
            }
            $this->db->where('os.dataInicial >=', $de);
        }
        if (!empty($where['ate'])) {
            $ate = $where['ate'];
            if (strpos($ate, ':') === false) {
                $ate .= ' 23:59:59';
            }
            $this->db->where('os.dataInicial <=', $ate);
        }
    }

    public function getOs($table, $fields, $where, $perpage, $start, $one, $array, $cliente)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');
        $this->db->where('clientes_id', $cliente);
        $this->db->limit($perpage, $start);
        $this->db->order_by('idOs', 'desc');
        if ($where) {
            if (is_array($where)) {
                $this->_applyOsFilters($where);
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
        $this->db->select('os.*, clientes.*, clientes.celular as celular_cliente, garantias.refGarantia, garantias.textoGarantia, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->where('os.idOs', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function count($table, $cliente, $where = '')
    {
        $this->db->from($table);
        if ($table === 'os') {
            $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');
        }
        $this->db->where('clientes_id', $cliente);
        if ($where) {
            if (is_array($where) && $table === 'os') {
                $this->_applyOsFilters($where);
            } else {
                $this->db->where($where);
            }
        }

        return $this->db->count_all_results();
    }

    public function getDados()
    {
        $this->db->where('idclientes', $this->session->userdata('cliente_id'));
        $this->db->limit(1);

        return $this->db->get('clientes')->row();
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

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }

        $result = $this->valorTotalOS($id);
        $amount = $result['valor_desconto'] != 0 ? round(floatval($result['valor_desconto']), 2) : round(floatval($result['totalServico'] + $result['totalProdutos']), 2);

        if ($amount <= 0) {
            return;
        }

        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }
}

/* End of file conecte_model.php */
/* Location: ./application/models/conecte_model.php */
