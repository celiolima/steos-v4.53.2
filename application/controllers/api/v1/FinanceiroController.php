<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class FinanceiroController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('financeiro_model');
    }

    private function applyLancamentoFilters()
    {
        $tipo = trim((string) $this->get('tipo', true));
        $baixado = $this->get('baixado', true);
        $de = trim((string) $this->get('vencimento_de', true));
        $ate = trim((string) $this->get('vencimento_ate', true));
        $centro = trim((string) $this->get('centro_de_gastos', true));
        $classificacao = trim((string) $this->get('classificacao_fin', true));
        $grupo = trim((string) $this->get('grupo_finaceiro', true));
        $formaPgto = trim((string) $this->get('forma_pgto', true));

        if ($tipo !== '' && $tipo !== 'Todos') {
            $this->db->where('lancamentos.tipo', $tipo);
        }
        if ($baixado !== null && $baixado !== '' && $baixado !== 'Todos') {
            $this->db->where('lancamentos.baixado', (int) $baixado);
        }
        if ($de !== '') {
            $this->db->where('lancamentos.data_vencimento >=', $de);
        }
        if ($ate !== '') {
            $this->db->where('lancamentos.data_vencimento <=', $ate);
        }
        if ($centro !== '') {
            $this->db->where('lancamentos.centro_de_gastos', $centro);
        }
        if ($classificacao !== '') {
            $this->db->where('lancamentos.classificacao_fin', $classificacao);
        }
        if ($grupo !== '') {
            $this->db->where('lancamentos.grupo_finaceiro', $grupo);
        }
        if ($formaPgto !== '') {
            $this->db->where('lancamentos.forma_pgto', $formaPgto);
        }
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vLancamento')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Lançamentos Financeiros.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if ($id && is_numeric($id)) {
            $lancamento = $this->db->select('lancamentos.*, usuarios.nome as usuario_nome')
                ->from('lancamentos')
                ->join('usuarios', 'usuarios.idUsuarios = lancamentos.usuarios_id', 'left')
                ->where('lancamentos.idLancamentos', (int) $id)
                ->limit(1)
                ->get()
                ->row();

            if (! $lancamento) {
                $this->response([
                    'status' => false,
                    'message' => 'Lançamento não encontrado.',
                ], REST_Controller::HTTP_NOT_FOUND);
            }

            $lancamento->anexos = $this->financeiro_model->getAnexos((int) $id);

            $this->response([
                'status' => true,
                'message' => 'Detalhes do Lançamento',
                'result' => $lancamento,
            ], REST_Controller::HTTP_OK);
        }

        $perPage = (int) ($this->get('perPage', true) ?: 20);
        $page = max(0, (int) $this->get('page', true));
        $start = $page * $perPage;

        $this->db->select('lancamentos.*, usuarios.nome as usuario_nome');
        $this->db->from('lancamentos');
        $this->db->join('usuarios', 'usuarios.idUsuarios = lancamentos.usuarios_id', 'left');
        $this->applyLancamentoFilters();
        $this->db->order_by('lancamentos.data_vencimento', 'DESC');
        $this->db->order_by('lancamentos.idLancamentos', 'DESC');
        $this->db->limit($perPage, $start);
        $results = $this->db->get()->result();

        $this->response([
            'status' => true,
            'message' => 'Listando Lançamentos Financeiros',
            'result' => $results,
        ], REST_Controller::HTTP_OK);
    }

    public function resumo_get()
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vLancamento')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Lançamentos Financeiros.',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $estatisticas = $this->financeiro_model->getEstatisticasFinanceiro2();

        $this->response([
            'status' => true,
            'message' => 'Resumo Financeiro',
            'result' => $estatisticas,
        ], REST_Controller::HTTP_OK);
    }
}
