<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Tecnicos_os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($perpage = 0, $start = 0, $one = false)
    {
        $this->db->from('tecnicos_os');
        $this->db->limit($perpage, $start);
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    /**
     * Retorna todos os técnicos vinculados a uma OS pelo ID da OS.
     * Faz JOIN com tecnicos para trazer o nome do técnico.
     */
    public function getById($id)
    {
        $this->db->from('tecnicos_os');
        $this->db->where('os_id', $id);
        $this->db->join('tecnicos', 'tecnicos.idTecnicos = tecnicos_os.tecnico_id', 'left');
        return $this->db->get()->result();
    }

    public function getAll()
    {
        return $this->db->get('tecnicos_os')->result();
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
