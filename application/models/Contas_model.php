<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Contas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idContas', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getAll($table)
    {
        $this->db->select('*');
        $this->db->from($table);
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->where('idContas', $id);
        $this->db->limit(1);
        return $this->db->get('contas')->row();
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $returnId ? $this->db->insert_id() : true;
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
