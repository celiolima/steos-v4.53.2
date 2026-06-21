<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
// NOTA: Typo corrigido — era "grupo_finaceiro" no steos. Aqui usa "grupo_financeiro".
defined('BASEPATH') OR exit('No direct script access allowed');

class Classificacao_financeira_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idClassFin', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getAll()
    {
        return $this->db->get('classificacao_financeira')->result();
    }

    public function getById($id)
    {
        $this->db->where('idClassFin', $id);
        $this->db->limit(1);
        return $this->db->get('classificacao_financeira')->row();
    }

    public function getByClassFin($ClassFin)
    {
        $this->db->where('nomeClassFin', $ClassFin);
        $this->db->limit(1);
        return $this->db->get('classificacao_financeira')->row();
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
