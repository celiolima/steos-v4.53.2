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

    protected function _applyCobrancasFilters($where = [])
    {
        if (empty($where) || !is_array($where)) {
            return;
        }

        // 1. Ordem de Serviço
        if (!empty($where['os_id'])) {
            $os_clean = preg_replace('/[^0-9]/', '', $where['os_id']);
            if (!empty($os_clean)) {
                $this->db->where('cobrancas.os_id', $os_clean);
            }
        }

        // 2. Venda / Fatura
        if (!empty($where['vendas_id'])) {
            $venda_clean = preg_replace('/[^0-9]/', '', $where['vendas_id']);
            if (!empty($venda_clean)) {
                $this->db->where('cobrancas.vendas_id', $venda_clean);
            }
        }

        // 3. Tipo (payment_method)
        if (!empty($where['tipo']) && $where['tipo'] !== 'Todos') {
            $this->db->where('cobrancas.payment_method', $where['tipo']);
        }

        // 4. Status
        if (!empty($where['status']) && $where['status'] !== 'Todos') {
            $this->db->where('cobrancas.status', $where['status']);
        }

        // 5. Período de Vencimento (expire_at)
        if (!empty($where['data_de'])) {
            $this->db->where('cobrancas.expire_at >=', $where['data_de']);
        }
        if (!empty($where['data_ate'])) {
            $this->db->where('cobrancas.expire_at <=', $where['data_ate']);
        }

        // 6. Valor Mínimo / Máximo (em centavos no banco)
        if (!empty($where['valor_de'])) {
            $this->db->where('cobrancas.total >=', $where['valor_de']);
        }
        if (!empty($where['valor_ate'])) {
            $this->db->where('cobrancas.total <=', $where['valor_ate']);
        }
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

    protected function _applyNfseFilters($where = [])
    {
        if (empty($where) || !is_array($where)) {
            return;
        }

        if (!empty($where['pesquisa'])) {
            $pesquisa = $where['pesquisa'];
            $this->db->group_start();
            $this->db->like('os.asaas_invoice_number', $pesquisa);
            if (is_numeric($pesquisa)) {
                $this->db->or_where('os.idOs', $pesquisa);
            }
            $this->db->group_end();
        }

        if (!empty($where['status']) && $where['status'] !== 'Todos') {
            $this->db->where('os.asaas_invoice_status', $where['status']);
        }

        if (!empty($where['data_de'])) {
            $deArr = explode('/', $where['data_de']);
            if (count($deArr) == 3) {
                $this->db->where('os.dataInicial >=', $deArr[2] . '-' . $deArr[1] . '-' . $deArr[0]);
            } else {
                $this->db->where('os.dataInicial >=', $where['data_de']);
            }
        }
        if (!empty($where['data_ate'])) {
            $ateArr = explode('/', $where['data_ate']);
            if (count($ateArr) == 3) {
                $this->db->where('os.dataInicial <=', $ateArr[2] . '-' . $ateArr[1] . '-' . $ateArr[0]);
            } else {
                $this->db->where('os.dataInicial <=', $where['data_ate']);
            }
        }
    }

    public function getNfse($table, $fields, $where, $perpage, $start, $one, $array, $cliente)
    {
        $this->db->select($fields);
        $this->db->from('os'); // The actual table is 'os'
        $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');
        $this->db->where('os.clientes_id', $cliente);
        $this->db->where('os.asaas_invoice_status IS NOT NULL');
        $this->db->where('os.asaas_invoice_status !=', '');
        
        $this->db->limit($perpage, $start);
        $this->db->order_by('os.idOs', 'desc');
        
        if ($where) {
            if (is_array($where)) {
                $this->_applyNfseFilters($where);
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
        if ($table === 'nfse') {
            $this->db->from('os');
        } else {
            $this->db->from($table);
        }
        
        if ($table === 'os' || $table === 'nfse') {
            $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios', 'left');
        } elseif ($table === 'cobrancas') {
            $this->db->join('clientes', 'cobrancas.clientes_id = clientes.idClientes', 'left');
        }
        $this->db->where('clientes_id', $cliente);
        if ($where) {
            if (is_array($where) && $table === 'os') {
                $this->_applyOsFilters($where);
            } elseif (is_array($where) && $table === 'nfse') {
                $this->_applyNfseFilters($where);
            } elseif (is_array($where) && $table === 'cobrancas') {
                $this->_applyCobrancasFilters($where);
            } else {
                $this->db->where($where);
            }
        }
        
        if ($table === 'nfse') {
            $this->db->where('os.asaas_invoice_status IS NOT NULL');
            $this->db->where('os.asaas_invoice_status !=', '');
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
    public function clienteTemContrato($cliente_id)
    {
        $this->db->where('clientes_id', $cliente_id);
        $this->db->where('contratos_id IS NOT NULL');
        $query = $this->db->get('os');
        return $query->num_rows() > 0;
    }

    public function getGraficoOsPrioridade($cliente_id)
    {
        $sql = "SELECT prioridade, 
                       SUM(
                           CASE 
                               WHEN valorTotal > 0 THEN valorTotal 
                               ELSE (
                                   COALESCE((SELECT SUM(preco * quantidade) FROM produtos_os WHERE os_id = os.idOs), 0) + 
                                   COALESCE((SELECT SUM(preco * quantidade) FROM servicos_os WHERE os_id = os.idOs), 0) - 
                                   COALESCE(desconto, 0)
                               ) 
                           END
                       ) as total
                FROM os
                WHERE clientes_id = ? AND contratos_id IS NOT NULL AND status = 'Negociação'
                GROUP BY prioridade";
                
        return $this->db->query($sql, [$cliente_id])->result();
    }

    public function getGraficoOsClassificacao($cliente_id)
    {
        $sql = "SELECT classificacao, 
                       SUM(
                           CASE 
                               WHEN valorTotal > 0 THEN valorTotal 
                               ELSE (
                                   COALESCE((SELECT SUM(preco * quantidade) FROM produtos_os WHERE os_id = os.idOs), 0) + 
                                   COALESCE((SELECT SUM(preco * quantidade) FROM servicos_os WHERE os_id = os.idOs), 0) - 
                                   COALESCE(desconto, 0)
                               ) 
                           END
                       ) as total
                FROM os
                WHERE clientes_id = ? AND contratos_id IS NOT NULL AND status = 'Negociação'
                GROUP BY classificacao";
                
        return $this->db->query($sql, [$cliente_id])->result();
    }
}

/* End of file conecte_model.php */
/* Location: ./application/models/conecte_model.php */
