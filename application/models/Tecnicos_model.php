<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Tecnicos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($perpage = 0, $start = 0, $one = false)
    {
        $this->db->from('tecnicos');
        $this->db->limit($perpage, $start);
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getAllTipos()
    {
        $this->db->select('*');
        $this->db->from('tecnicos');
        $this->db->where('ativo', 1);
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->where('idTecnicos', $id);
        $this->db->limit(1);
        return $this->db->get('tecnicos')->row();
    }

    public function getAll()
    {
        return $this->db->get('tecnicos')->result();
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
