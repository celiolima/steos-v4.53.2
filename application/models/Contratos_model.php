<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contratos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ', clientes.nomeCliente, clientes.celular as celular_cliente');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = contratos.clientes_id');
        $this->db->limit($perpage, $start);
        $this->db->order_by('idContratos', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();
        $result =  !$one ? $query->result() : $query->row();
        return $result;
    }

    public function getContratos($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ', clientes.nomeCliente, clientes.celular as celular_cliente');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = contratos.clientes_id');

        if ($where) {
            if (array_key_exists('contrato', $where) && !empty($where['contrato'])) {
                $this->db->where('contratos.idContratos', $where['contrato']);
            }
            if (array_key_exists('pesquisa', $where) && !empty($where['pesquisa'])) {
                $this->db->like('clientes.nomeCliente', $where['pesquisa']);
            }
            if (array_key_exists('nome', $where) && !empty($where['nome'])) {
                $this->db->like('contratos.nomeContratos', $where['nome']);
            }
            if (array_key_exists('status', $where) && !empty($where['status'])) {
                if ($where['status'] == 'Ativo' || $where['status'] == '1') {
                    $this->db->where_in('contratos.status', ['Ativo', '1']);
                } elseif ($where['status'] == 'Inativo' || $where['status'] == '0') {
                    $this->db->where_in('contratos.status', ['Inativo', '0']);
                } else {
                    $this->db->where('contratos.status', $where['status']);
                }
            }
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('idContratos', 'desc');

        $query = $this->db->get();
        $result =  !$one ? $query->result() : $query->row();
        return $result;
    }

    public function getById($id)
    {
        $this->db->select('contratos.*, clientes.nomeCliente, clientes.documento, clientes.telefone, clientes.celular, clientes.email, clientes.rua, clientes.numero, clientes.bairro, clientes.cidade, clientes.estado, clientes.cep');
        $this->db->from('contratos');
        $this->db->join('clientes', 'clientes.idClientes = contratos.clientes_id');
        $this->db->where('idContratos', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
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

    public function getOsByContrato($idContrato)
    {
        $this->db->select('os.*, usuarios.nome, (SELECT tecnicoName FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs LIMIT 1) as tecnicoName', false);
        $this->db->from('os');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->where('os.contratos_id', $idContrato);
        $this->db->order_by('os.idOs', 'desc');
        return $this->db->get()->result();
    }

    public function getVendasByContrato($idContrato)
    {
        $this->db->select('vendas.*, usuarios.nome');
        $this->db->from('vendas');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
        $this->db->where('vendas.contratos_id', $idContrato);
        $this->db->order_by('vendas.idVendas', 'desc');
        return $this->db->get()->result();
    }

    public function count($table, $where = [])
    {
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = contratos.clientes_id', 'left');

        if ($where) {
            if (array_key_exists('contrato', $where) && !empty($where['contrato'])) {
                $this->db->where('contratos.idContratos', $where['contrato']);
            }
            if (array_key_exists('pesquisa', $where) && !empty($where['pesquisa'])) {
                $this->db->like('clientes.nomeCliente', $where['pesquisa']);
            }
            if (array_key_exists('nome', $where) && !empty($where['nome'])) {
                $this->db->like('contratos.nomeContratos', $where['nome']);
            }
            if (array_key_exists('status', $where) && !empty($where['status'])) {
                if ($where['status'] == 'Ativo' || $where['status'] == '1') {
                    $this->db->where_in('contratos.status', ['Ativo', '1']);
                } elseif ($where['status'] == 'Inativo' || $where['status'] == '0') {
                    $this->db->where_in('contratos.status', ['Inativo', '0']);
                } else {
                    $this->db->where('contratos.status', $where['status']);
                }
            }
        }

        return $this->db->count_all_results();
    }

    public function getCobrancasByContrato($idContrato)
    {
        // Busca as cobranças atreladas às O.S. ou Vendas deste contrato
        $this->db->select('cobrancas.*');
        $this->db->from('cobrancas');
        $this->db->group_start();
        $this->db->where("cobrancas.os_id IN (SELECT idOs FROM os WHERE contratos_id = $idContrato)", NULL, FALSE);
        $this->db->or_where("cobrancas.vendas_id IN (SELECT idVendas FROM vendas WHERE contratos_id = $idContrato)", NULL, FALSE);
        $this->db->group_end();
        $this->db->order_by('cobrancas.idCobranca', 'desc');
        return $this->db->get()->result();
    }

    // ==========================================
    // ANEXOS
    // ==========================================

    public function anexar($contrato, $anexo, $url, $thumb, $path)
    {
        $this->db->set('anexo', $anexo);
        $this->db->set('url', $url);
        $this->db->set('thumb', $thumb);
        $this->db->set('path', $path);
        $this->db->set('contrato_id', $contrato);

        return $this->db->insert('anexos');
    }

    public function getAnexos($idContrato)
    {
        $this->db->where('contrato_id', $idContrato);
        return $this->db->get('anexos')->result();
    }

    // ==========================================
    // SISTEMAS E CHECKS
    // ==========================================

    public function autoCompleteSistema($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('sistemas');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | R$ ' . $row['preco'], 'id' => $row['idSistemas'], 'preco' => $row['preco']];
            }
            echo json_encode($row_set);
        }
    }

    public function getSistemasByContrato($idContrato)
    {
        $this->db->select('sistemas_contratos.*, sistemas.nome');
        $this->db->from('sistemas_contratos');
        $this->db->join('sistemas', 'sistemas.idSistemas = sistemas_contratos.sistemas_id');
        $this->db->where('sistemas_contratos.contratos_id', $idContrato);
        return $this->db->get()->result();
    }

    public function getChecksBySistemaContrato($idSistemaContrato)
    {
        // Sincronização Dinâmica: Busca o sistema atrelado para achar o ID do sistema base
        $sc = $this->db->where('idSistemas_contratos', $idSistemaContrato)->get('sistemas_contratos')->row();
        if ($sc) {
            // Busca os checks configurados na base global deste sistema
            $checksBase = $this->db->where('sistemas_id', $sc->sistemas_id)->get('sistemas_checks')->result();
            foreach ($checksBase as $cb) {
                // Se a descrição do check base não existe para este contrato, insere
                $existe = $this->db->where('sistemas_contratos_id', $idSistemaContrato)
                                   ->where('descricao', $cb->descricao)
                                   ->get('sistemas_contratos_checks')->row();
                if (!$existe) {
                    $this->db->insert('sistemas_contratos_checks', [
                        'sistemas_contratos_id' => $idSistemaContrato,
                        'descricao' => $cb->descricao,
                        'concluido' => 0
                    ]);
                }
            }
        }

        // Agora retorna todos os checks do contrato (os recém-sincronizados + os extras manuais)
        $this->db->where('sistemas_contratos_id', $idSistemaContrato);
        return $this->db->get('sistemas_contratos_checks')->result();
    }
}
