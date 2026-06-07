<?php
// CI3 — MIGRAÇÃO: Port direto do steos — Fase 3
defined('BASEPATH') OR exit('No direct script access allowed');

class Gasolina_model extends CI_Model
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
     * Retorna registros de gasolina com JOIN em veículos.
     */
    public function getAll($tabela, $perpage = 0, $start = 0, $one = false)
    {
        $this->db->select('*');
        $this->db->from($tabela);
        $this->db->limit($perpage, $start);
        $this->db->join('veiculos', 'veiculos.idVeiculos = gasolina.veiculos_id', 'left');
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    public function getName($tabela, $nomeVeiculo)
    {
        $this->db->select('*');
        $this->db->where('nome', $nomeVeiculo);
        $this->db->limit(1);
        return $this->db->get($tabela)->row();
    }

    public function getById($tabela, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->limit(1);
        return $this->db->get($tabela)->row();
    }

    public function getByIdByFild($id, $Fild)
    {
        $this->db->select($Fild);
        $this->db->where('idGasolina', $id);
        $this->db->limit(1);
        return $this->db->get('gasolina')->row();
    }

    /**
     * Retorna histórico de abastecimento de um veículo específico.
     */
    public function getByIdEx($idEx, $perpage = 0, $start = 0, $one = false)
    {
        $this->db->select('*');
        $this->db->from('gasolina');
        $this->db->where('veiculos_id', $idEx);
        $query = $this->db->get();
        return $query->result();
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
