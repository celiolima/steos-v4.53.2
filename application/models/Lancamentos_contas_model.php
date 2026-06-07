<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Lancamentos_contas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($tabela)
    {
        return $this->db->get($tabela)->result();
    }

    /**
     * Retorna todos os lançamentos com JOIN em contas.
     */
    public function getAll($tabela, $perpage = 0, $start = 0, $one = false)
    {
        $this->db->select('*');
        $this->db->from($tabela);
        $this->db->limit($perpage, $start);
        $this->db->join('contas', 'contas.idContas = lancamentos_contas.contas_id', 'left');
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    /**
     * Retorna lançamentos de uma conta específica pelo ID da conta.
     */
    public function getAllFild($tabela, $Fild, $one = false)
    {
        $this->db->select('*');
        $this->db->from($tabela);
        $this->db->where('contas_id', $Fild);
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getLastId($tabela, $array = null)
    {
        $this->db->select_max('idGasolina');
        if ($array) {
            $this->db->where($array);
        }
        $this->db->limit(1);
        return $this->db->get($tabela)->row();
    }

    public function getLastFild($tabela, $colunas, $array)
    {
        $this->db->select($colunas);
        $this->db->where($array);
        $this->db->limit(1);
        return $this->db->get($tabela)->row();
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

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
