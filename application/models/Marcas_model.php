<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Marcas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll($tabela, $perpage = 0, $start = 0, $one = false)
    {
        $this->db->select('*');
        $this->db->from($tabela);
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idMarcas', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getById($id)
    {
        $this->db->where('idMarcas', $id);
        $this->db->limit(1);
        return $this->db->get('marcas')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->affected_rows() == '1';
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
