<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Equipamentos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idEquipamentos', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            if (is_array($where)) {
                $this->db->where($where);
            } else {
                $this->db->group_start();
                $this->db->like('equipamento', $where);
                $this->db->or_like('num_serie', $where);
                $this->db->or_like('modelo', $where);
                $this->db->or_like('cor', $where);
                $this->db->or_like('marcas', $where);
                $this->db->or_like('descricao', $where);
                $this->db->group_end();
            }
        }
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getById($id)
    {
        $this->db->where('idEquipamentos', $id);
        $this->db->limit(1);
        return $this->db->get('equipamentos')->row();
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

    public function count($table, $where = '')
    {
        if ($where) {
            if (is_array($where)) {
                $this->db->where($where);
            } else {
                $this->db->group_start();
                $this->db->like('equipamento', $where);
                $this->db->or_like('num_serie', $where);
                $this->db->or_like('modelo', $where);
                $this->db->or_like('cor', $where);
                $this->db->or_like('marcas', $where);
                $this->db->or_like('descricao', $where);
                $this->db->group_end();
            }
            return $this->db->count_all_results($table);
        }
        return $this->db->count_all($table);
    }
}
