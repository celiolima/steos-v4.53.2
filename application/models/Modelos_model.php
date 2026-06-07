<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3

class Modelos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lista modelos com JOIN em usuários para exibir o criador.
     */
    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ', usuarios.nome, usuarios.idUsuarios');
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        $this->db->join('usuarios', 'usuarios.idUsuarios = modelos.usuarios_id');
        $this->db->order_by('idModelos', 'asc');
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getAll()
    {
        $this->db->select('*');
        $this->db->from('modelos');
        return $this->db->get()->result();
    }

    /**
     * Retorna modelo com dados do usuário criador (telefone, email, nome).
     */
    public function getById($id)
    {
        $this->db->select('modelos.*, usuarios.telefone, usuarios.email, usuarios.nome');
        $this->db->from('modelos');
        $this->db->join('usuarios', 'usuarios.idUsuarios = modelos.usuarios_id');
        $this->db->where('modelos.idModelos', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * Retorna garantia de OS com dados completos para impressão.
     */
    public function getByIdOsGarantia($id)
    {
        $this->db->select('garantias.*, clientes.nomeCliente, os.idOS as idOs, os.dataFinal as osDataFinal,
         usuarios.telefone as tecnicoTelefone, usuarios.email as tecnicoEmail, usuarios.nome as tecnicoName');
        $this->db->from('garantias');
        $this->db->join('os', 'os.garantias_id = garantias.idGarantias');
        $this->db->join('clientes', 'os.clientes_id = clientes.idClientes');
        $this->db->join('usuarios', 'os.usuarios_id = usuarios.idUsuarios');
        $this->db->where('garantias.idGarantias', $id);
        $this->db->limit(1);
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
        return $this->db->affected_rows() >= 0;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        return $this->db->affected_rows() == '1';
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
