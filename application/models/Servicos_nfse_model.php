<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Servicos_nfse_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idServicosNfse', 'desc');
        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }
        if ($where) {
            $this->db->group_start();
            $this->db->like('nome_servico', $where);
            $this->db->or_like('codigo_servico_municipal', $where);
            $this->db->or_like('codigo_nbs', $where);
            $this->db->group_end();
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('idServicosNfse', $id);
        $this->db->limit(1);

        return $this->db->get('servicos_nfse')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
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

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
