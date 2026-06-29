<?php
// MIGRAÇÃO STEOS → STEOS v4.53.2
// Os_model.php — BASE: steos (dominante) + métodos exclusivos preservados do steos
// Regra: steos domina; métodos do steos não presentes no steos são adicionados ao final

use Piggly\Pix\StaticPayload;

class Os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ─── GET BASE ────────────────────────────────────────────
    // Base: idêntico em ambos — mantido do steos (mais limpo)
    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ',clientes.nomeCliente, clientes.celular as celular_cliente');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->limit($perpage, $start);
        $this->db->order_by('idOs', 'desc');
        if ($where) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    // ─── APLICAÇÃO DE FILTROS OS ─────────────────────────────
    protected function _applyOsFilters($where = [])
    {
        if (empty($where) || !is_array($where)) {
            return;
        }

        // 1. Número da OS
        if (!empty($where['os'])) {
            if (is_array($where['os'])) {
                $this->db->where_in('os.idOs', $where['os']);
            } else {
                $os_clean = preg_replace('/[^0-9,]/', '', $where['os']);
                if (strpos($os_clean, ',') !== false) {
                    $os_arr = array_filter(explode(',', $os_clean));
                    if (!empty($os_arr)) {
                        $this->db->where_in('os.idOs', $os_arr);
                    }
                } elseif (!empty($os_clean)) {
                    $this->db->where('os.idOs', $os_clean);
                }
            }
        }

        // 2. Técnico
        if (!empty($where['tecnico']) && $where['tecnico'] !== 'Todos' && $where['tecnico'] !== 'Nome do Tecnico') {
            if (is_array($where['tecnico'])) {
                $this->db->where("EXISTS (SELECT 1 FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs AND tecnicos_os.tecnicoName IN (".implode(',', array_map([$this->db, 'escape'], $where['tecnico']))."))", null, false);
            } else {
                $this->db->where("EXISTS (SELECT 1 FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs AND tecnicos_os.tecnicoName = ".$this->db->escape($where['tecnico']).")", null, false);
            }
        }

        // 3. Local
        if (!empty($where['local']) && $where['local'] !== 'Todos') {
            if (is_array($where['local'])) {
                $this->db->where_in('os.local', $where['local']);
            } else {
                $this->db->where('os.local', $where['local']);
            }
        }

        // 4. Tipo
        if (!empty($where['tipo']) && $where['tipo'] !== 'Todos') {
            if (is_array($where['tipo'])) {
                $this->db->where_in('os.tipo', $where['tipo']);
            } else {
                $this->db->where('os.tipo', $where['tipo']);
            }
        }

        // 5. Vendedor / Usuário Responsável
        if (!empty($where['vendedor']) && $where['vendedor'] !== 'Todos') {
            if (is_array($where['vendedor'])) {
                $this->db->where_in('usuarios.nome', $where['vendedor']);
            } else {
                $this->db->where('usuarios.nome', $where['vendedor']);
            }
        }

        // 6. Status
        if (!empty($where['status']) && $where['status'] !== 'Todos') {
            if (is_array($where['status'])) {
                $this->db->where_in('os.status', $where['status']);
            } else {
                $this->db->where('os.status', $where['status']);
            }
        }

        // 7. Cliente (Pesquisa por Nome)
        if (!empty($where['pesquisa'])) {
            $this->db->like('clientes.nomeCliente', $where['pesquisa']);
        }

        // 8. Observação
        if (!empty($where['observacao'])) {
            $this->db->like('os.observacoes', $where['observacao']);
        }

        // 9. A faturar
        if (!empty($where['afaturar'])) {
            if (is_array($where['afaturar'])) {
                $this->db->where_in('os.afaturar', $where['afaturar']);
            } else {
                $this->db->where('os.afaturar', 1);
            }
        }

        // 10. Manut Preventiva
        if (!empty($where['manPrevnt'])) {
            $this->db->where('os.manutPreventiva', 1);
        }

        // 11. Período (Data Inicial e Final de Emissão/Abertura)
        if (!empty($where['de'])) {
            $de = $where['de'];
            if (strpos($de, ':') === false) {
                $de .= ' 00:00:00';
            }
            $this->db->where('os.dataInicial >=', $de);
        }
        if (!empty($where['ate'])) {
            $ate = $where['ate'];
            if (strpos($ate, ':') === false) {
                $ate .= ' 23:59:59';
            }
            $this->db->where('os.dataInicial <=', $ate);
        }
    }

    // ─── GET OS (STEOS DOMINANTE) ─────────────────────────────
    public function getOs($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array', $param8 = null, $tecnicoLogado = null)
    {
        $this->db->select($fields . ',clientes.idClientes, clientes.nomeCliente, clientes.celular as celular_cliente, usuarios.nome, garantias.*, COALESCE((SELECT tecnicoName FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs LIMIT 1), usuarios.nome) as tecnico_responsavel, (SELECT tecnicoName FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs LIMIT 1) as tecnicoName', false);
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');

        $this->_applyOsFilters($where);

        if ($tecnicoLogado && !empty($tecnicoLogado)) {
            $this->db->where("EXISTS (SELECT 1 FROM tecnicos_os WHERE tecnicos_os.os_id = os.idOs AND tecnicos_os.tecnicoName = ".$this->db->escape($tecnicoLogado).")", null, false);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('os.idOs', 'desc');

        $query = $this->db->get();
        $result = !$one ? $query->result() : $query->row();
        return $result;
    }

    // ─── GET OS COMISSAO (EXCLUSIVO STEOS) ───────────────────
    public function getOsComissao($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $tecnico = false, $array = 'array')
    {
        $lista_clientes = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->limit(15);
                $clientes = $this->db->get('clientes')->result();
                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }

        if (array_key_exists('tecnico', $where)) {
            $this->db->select($fields . ',clientes.idClientes, clientes.nomeCliente, clientes.celular as celular_cliente, usuarios.nome, garantias.*,tecnicos_os.tecnicoName');
        } else {
            $this->db->select($fields . ',clientes.idClientes, clientes.nomeCliente, clientes.celular as celular_cliente, usuarios.nome, garantias.*');
        }

        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->join('produtos_os', 'produtos_os.os_id = os.idOs', 'left');
        $this->db->join('servicos_os', 'servicos_os.os_id = os.idOs', 'left');
        $this->db->join('tecnicos_os', 'tecnicos_os.os_id = os.idOs', 'left');

        $status = ['Finalizado', 'Faturado'];
        $this->db->where_in('status', $status);

        if (array_key_exists('tecnico', $where)) {
            $this->db->where_in('tecnicos_os.tecnicoName', $where['tecnico']);
        }
        if (array_key_exists('local', $where)) {
            $this->db->where_in('local', $where['local']);
        }
        if (array_key_exists('tipo', $where)) {
            $this->db->where_in('tipo', $where['tipo']);
        }
        if (array_key_exists('vendedor', $where)) {
            $this->db->where_in('vendedor', $where['vendedor']);
        }
        if (array_key_exists('de', $where)) {
            $this->db->where('dataInicial >=', $where['de']);
        }
        if (array_key_exists('ate', $where)) {
            $this->db->where('dataFinal <=', $where['ate']);
        }

        $this->db->order_by('os.idOs', 'desc');
        $this->db->group_by('os.idOs');
        $query = $this->db->get();
        return !$one ? $query->result() : $query->row();
    }

    // ─── GET BY ID (STEOS DOMINANTE) ─────────────────────────
    // Steos adiciona joins em produtos_os e servicos_os + campos subTotal
    public function getById($id)
    {
        $this->db->select('os.*, clientes.*, produtos_os.subTotal as produtos_subTotal, servicos_os.subTotal as servicos_subTotal, clientes.celular as celular_cliente, clientes.telefone as telefone_cliente, clientes.contato as contato_cliente, garantias.refGarantia, garantias.textoGarantia, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->join('produtos_os', 'produtos_os.os_id = os.idOs', 'left');
        $this->db->join('servicos_os', 'servicos_os.os_id = os.idOs', 'left');
        $this->db->where('os.idOs', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    // ─── GET OS NOME TECNICOS (EXCLUSIVO STEOS) ──────────────
    public function getOsNomeTecnicos($id = null)
    {
        $this->db->select('os.*');
        $this->db->from('tecnicos_os');
        $this->db->join('tecnicos', 'tecnicos.idTecnicos = tecnicos_os.tecnico_id');
        $this->db->join('os', 'os.idOs = tecnicos_os.os_id');
        $this->db->where('nome', 'Dario');
        return $this->db->get()->result();
    }

    // ─── GET BY ID COBRANCAS ─────────────────────────────────
    public function getByIdCobrancas($id)
    {
        $this->db->select('os.*, clientes.*, clientes.celular as celular_cliente, garantias.refGarantia, garantias.textoGarantia, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome,cobrancas.os_id,cobrancas.idCobranca,cobrancas.status');
        $this->db->from('os');
        $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
        $this->db->join('cobrancas', 'cobrancas.os_id = os.idOs');
        $this->db->join('garantias', 'garantias.idGarantias = os.garantias_id', 'left');
        $this->db->where('os.idOs', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    // ─── GET BY ID ASSINATURA (EXCLUSIVO STEOS) ──────────────
    public function getByIdAssinatura($id)
    {
        $this->db->select('*');
        $this->db->from('assinatura');
        $this->db->where('assinatura.idAssinatura', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getByIdAssinaturaExtr($id)
    {
        $this->db->select('*');
        $this->db->from('assinatura');
        $this->db->where('assinatura.os_id', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    // ─── GET PRODUTOS / SERVICOS ──────────────────────────────
    public function getProdutos($id = null)
    {
        $this->db->select('produtos_os.*, produtos.*');
        $this->db->from('produtos_os');
        $this->db->join('produtos', 'produtos.idProdutos = produtos_os.produtos_id');
        $this->db->where('os_id', $id);
        return $this->db->get()->result();
    }

    public function getServicos($id = null)
    {
        $this->db->select('servicos_os.*, servicos.nome, servicos.preco as precoVenda');
        $this->db->from('servicos_os');
        $this->db->join('servicos', 'servicos.idServicos = servicos_os.servicos_id');
        $this->db->where('os_id', $id);
        return $this->db->get()->result();
    }

    // ─── CRUD ─────────────────────────────────────────────────
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

    public function count($table, $where = [])
    {
        if ($table === 'os' && !empty($where)) {
            $this->db->from('os');
            $this->db->join('clientes', 'clientes.idClientes = os.clientes_id');
            $this->db->join('usuarios', 'usuarios.idUsuarios = os.usuarios_id');
            $this->_applyOsFilters($where);
            return $this->db->count_all_results();
        }
        return $this->db->count_all($table);
    }

    // ─── AUTOCOMPLETE ────────────────────────────────────────
    // limit(25) do steos mantido (mais resultados que o steos limit=5)
    public function autoCompleteProduto($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('codDeBarra', $q);
        $this->db->or_like('descricao', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['descricao'] . ' | Preço: R$ ' . $row['precoVenda'] . ' | Estoque: ' . $row['estoque'], 'estoque' => $row['estoque'], 'id' => $row['idProdutos'], 'preco' => $row['precoVenda']];
            }
            echo json_encode($row_set);
        }
    }

    // autoCompleteProdutoSaida com filtro 'saida=1' — STEOS (não existe no steos)
    public function autoCompleteProdutoSaida($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('codDeBarra', $q);
        $this->db->or_like('descricao', $q);
        $this->db->where('saida', 1);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['descricao'] . ' | Preço: R$ ' . $row['precoVenda'] . ' | Estoque: ' . $row['estoque'], 'estoque' => $row['estoque'], 'id' => $row['idProdutos'], 'preco' => $row['precoVenda']];
            }
            echo json_encode($row_set);
        }
    }

    // autoCompleteCliente — MERGE: steos base + campo 'documento' do steos
    public function autoCompleteCliente($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nomeCliente', $q);
        $this->db->or_like('telefone', $q);
        $this->db->or_like('celular', $q);
        $this->db->or_like('documento', $q); // STEOS: busca por documento também
        $query = $this->db->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nomeCliente'] . ' | Telefone: ' . $row['telefone'] . ' | Celular: ' . $row['celular'] . ' | Documento: ' . $row['documento'], 'id' => $row['idClientes']];
            }
            echo json_encode($row_set);
        } else {
            $row_set[] = ['label' => 'Não encontrado na base'];
            echo json_encode($row_set);
        }
    }

    // autoCompleteEquipamentos — EXCLUSIVO STEOS
    public function autoCompleteEquipamentos($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('equipamento', $q);
        $this->db->or_like('num_serie', $q);
        $this->db->or_like('cor', $q);
        $query = $this->db->get('equipamentos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label'      => $row['equipamento'] . ' | Serie: ' . $row['num_serie'] . ' | Modelo: ' . $row['modelo'],
                    'id'         => $row['idEquipamentos'],
                    'equipamento' => $row['equipamento'],
                    'num_serie'  => $row['num_serie'],
                    'modelo'     => $row['modelo'],
                    'cor'        => $row['cor'],
                    'descricao'  => $row['descricao'],
                    'potencia'   => $row['potencia'],
                    'voltagem'   => $row['voltagem'],
                    'marcas'     => $row['marcas'],
                ];
            }
            echo json_encode($row_set);
        } else {
            $row_set[] = ['label' => 'Não encontrado na base'];
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | Telefone: ' . $row['telefone'], 'id' => $row['idUsuarios']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteTermoGarantia($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('LOWER(refGarantia)', $q);
        $query = $this->db->get('garantias');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['refGarantia'], 'id' => $row['idGarantias']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteServico($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nome', $q);
        $query = $this->db->get('servicos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | Preço: R$ ' . $row['preco'], 'id' => $row['idServicos'], 'preco' => $row['preco']];
            }
            echo json_encode($row_set);
        }
    }

    // ─── ANEXOS / ANOTAÇÕES ───────────────────────────────────
    public function anexar($os, $anexo, $url, $thumb, $path)
    {
        $this->db->set('anexo', $anexo);
        $this->db->set('url', $url);
        $this->db->set('thumb', $thumb);
        $this->db->set('path', $path);
        $this->db->set('os_id', $os);
        return $this->db->insert('anexos');
    }

    public function getAnexos($os)
    {
        $this->db->where('os_id', $os);
        return $this->db->get('anexos')->result();
    }

    public function getAnotacoes($os)
    {
        $this->db->where('os_id', $os);
        $this->db->order_by('idAnotacoes', 'desc');
        return $this->db->get('anotacoes_os')->result();
    }

    public function getCobrancas($id = null)
    {
        $this->db->select('cobrancas.*');
        $this->db->from('cobrancas');
        $this->db->where('os_id', $id);
        return $this->db->get()->result();
    }

    // ─── UTILIDADES ──────────────────────────────────────────
    public function criarTextoWhats($textoBase, $troca)
    {
        $procura   = ['{CLIENTE_NOME}', '{NUMERO_OS}', '{STATUS_OS}', '{VALOR_OS}', '{DESCRI_PRODUTOS}', '{EMITENTE}', '{TELEFONE_EMITENTE}', '{OBS_OS}', '{DEFEITO_OS}', '{LAUDO_OS}', '{DATA_FINAL}', '{DATA_INICIAL}', '{DATA_GARANTIA}'];
        $textoBase = str_replace($procura, $troca, $textoBase);
        $textoBase = strip_tags($textoBase);
        $textoBase = htmlentities(urlencode($textoBase));
        return $textoBase;
    }

    public function valorTotalOS($id = null)
    {
        $totalServico  = 0;
        $totalProdutos = 0;
        $valorDesconto = 0;
        if ($servicos = $this->getServicos($id)) {
            foreach ($servicos as $s) {
                $preco        = $s->preco ?: $s->precoVenda;
                $totalServico = $totalServico + ($preco * ($s->quantidade ?: 1));
            }
        }
        if ($produtos = $this->getProdutos($id)) {
            foreach ($produtos as $p) {
                $totalProdutos = $totalProdutos + $p->subTotal;
            }
        }
        if ($valorDescontoBD = $this->getById($id)) {
            $valorDesconto = $valorDescontoBD->valor_desconto;
        }
        return ['totalServico' => $totalServico, 'totalProdutos' => $totalProdutos, 'valor_desconto' => $valorDesconto];
    }

    public function isEditable($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            return false;
        }
        if ($os = $this->getById($id)) {
            $osT = (int) ($os->status === 'Faturado' || $os->status === 'Cancelado' || $os->faturado == 1);
            if ($osT) {
                return $this->data['configuration']['control_editos'] == '1';
            }
        }
        return true;
    }

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }
        $result = $this->valorTotalOS($id);
        $amount = $result['valor_desconto'] != 0
            ? round(floatval($result['valor_desconto']), 2)
            : round(floatval($result['totalServico'] + $result['totalProdutos']), 2);

        if ($amount <= 0) {
            return;
        }
        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }

    public function faturarOs($os_id, $dataLancamento, $dadosOs)
    {
        $this->db->trans_start();

        $this->db->insert('lancamentos', $dataLancamento);
        $idLancamentos = $this->db->insert_id();

        if ($idLancamentos) {
            $this->db->where('idOs', $os_id);
            $this->db->update('os', $dadosOs);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
