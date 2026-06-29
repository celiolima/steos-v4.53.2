<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contratos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('contratos_model');
        $this->load->model('clientes_model');
        $this->load->model('tecnicos_model');
        $this->data['menuContrato'] = 'Contratos';

        if (!$this->db->field_exists('tecnico_id', 'contratos')) {
            $this->load->dbforge();
            $this->dbforge->add_column('contratos', [
                'tecnico_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true
                ]
            ]);
        }
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $contrato = $this->input->get('contrato');
        $nome = $this->input->get('nome');
        $status = $this->input->get('status');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($contrato) {
            $where_array['contrato'] = $contrato;
        }
        if ($nome) {
            $where_array['nome'] = $nome;
        }
        if ($status) {
            $where_array['status'] = $status;
        }

        $this->data['configuration']['base_url'] = site_url('contratos/gerenciar/');
        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = '?' . http_build_query($where_array);
            $this->data['configuration']['first_url'] = $this->data['configuration']['base_url'] . '?' . http_build_query($where_array);
        }
        $this->data['configuration']['total_rows'] = $this->contratos_model->count('contratos', $where_array);

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->contratos_model->getContratos('contratos', 'contratos.*', $where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'contratos/contratos';

        return $this->layout();
    }

    public function adicionar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('clientes_id', 'Cliente', 'required');
        $this->form_validation->set_rules('tipoContrato', 'Tipo de Contrato', 'required');
        $this->form_validation->set_rules('nomeContratos', 'Nome do Contrato', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');

            if ($dataInicial) {
                $dataInicial = explode('/', $dataInicial);
                $dataInicial = $dataInicial[2] . '-' . $dataInicial[1] . '-' . $dataInicial[0];
            }

            if ($dataFinal) {
                $dataFinal = explode('/', $dataFinal);
                $dataFinal = $dataFinal[2] . '-' . $dataFinal[1] . '-' . $dataFinal[0];
            } else {
                $dataFinal = null;
            }

            $data = [
                'clientes_id' => $this->input->post('clientes_id'),
                'tipoContrato' => $this->input->post('tipoContrato'),
                'tecnico_id' => $this->input->post('tecnico_id') ?: null,
                'nomeContratos' => $this->input->post('nomeContratos'),
                'descricaoContratos' => $this->input->post('descricaoContratos'),
                'dataInicial' => $dataInicial,
                'dataFinal' => $dataFinal,
                'valorContrato' => $this->input->post('valorContrato') ?: 0,
                'valorDesconto' => $this->input->post('valorDesconto') ?: 0,
                'valorTotal' => $this->input->post('valorTotal') ?: 0,
                'status' => $this->input->post('status') ?: 'Negociação',
            ];

            if ($this->contratos_model->add('contratos', $data) == true) {
                $this->session->set_flashdata('success', 'Contrato adicionado com sucesso!');
                log_info('Adicionou um Contrato');
                redirect(site_url('contratos/gerenciar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['view'] = 'contratos/adicionarContrato';
        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3)) {
            $this->session->set_flashdata('error', 'Item não selecionado.');
            redirect(site_url('contratos/gerenciar/'));
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('clientes_id', 'Cliente', 'required');
        $this->form_validation->set_rules('tipoContrato', 'Tipo de Contrato', 'required');
        $this->form_validation->set_rules('nomeContratos', 'Nome do Contrato', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');

            if ($dataInicial) {
                $dataInicial = explode('/', $dataInicial);
                $dataInicial = $dataInicial[2] . '-' . $dataInicial[1] . '-' . $dataInicial[0];
            }

            if ($dataFinal) {
                $dataFinal = explode('/', $dataFinal);
                $dataFinal = $dataFinal[2] . '-' . $dataFinal[1] . '-' . $dataFinal[0];
            } else {
                $dataFinal = null;
            }

            $valorContrato = $this->input->post('valorContrato') ?: 0;
            $valorDesconto = $this->input->post('valorDesconto') ?: 0;
            $valorAcrescimo = $this->input->post('valorAcrescimo') ?: 0;

            // Sanitize Brazilian money format: 3.000,00 -> 3000.00
            $valorContrato = str_replace(['R$', ' ', '.'], '', $valorContrato);
            $valorContrato = str_replace(',', '.', $valorContrato);

            $valorDesconto = str_replace(['R$', ' ', '.'], '', $valorDesconto);
            $valorDesconto = str_replace(',', '.', $valorDesconto);

            $valorAcrescimo = str_replace(['R$', ' ', '.'], '', $valorAcrescimo);
            $valorAcrescimo = str_replace(',', '.', $valorAcrescimo);

            $data = [
                'clientes_id' => $this->input->post('clientes_id'),
                'tipoContrato' => $this->input->post('tipoContrato'),
                'tecnico_id' => $this->input->post('tecnico_id') ?: null,
                'nomeContratos' => $this->input->post('nomeContratos'),
                'descricaoContratos' => $this->input->post('descricaoContratos'),
                'dataInicial' => $dataInicial,
                'dataFinal' => $dataFinal,
                'valorContrato' => $valorContrato,
                'tipoDesconto' => $this->input->post('tipoDesconto') ?: 'R$',
                'valorDesconto' => $valorDesconto,
                'tipoAcrescimo' => $this->input->post('tipoAcrescimo') ?: 'R$',
                'valorAcrescimo' => $valorAcrescimo,
                'status' => $this->input->post('status'),
            ];

            if ($this->contratos_model->edit('contratos', $data, 'idContratos', $this->input->post('idContratos')) == true) {
                // Força o recálculo do total com base nos sistemas atuais e no novo base/desconto
                $this->recalcularValorTotalContrato($this->input->post('idContratos'));
                
                $this->session->set_flashdata('success', 'Contrato editado com sucesso!');
                log_info('Editou um Contrato - ID: ' . $this->input->post('idContratos'));
                redirect(site_url('contratos/editar/') . $this->input->post('idContratos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['result'] = $this->contratos_model->getById($this->uri->segment(3));
        
        // Puxando os dados atrelados para as abas
        $this->data['os'] = $this->contratos_model->getOsByContrato($this->uri->segment(3));
        $this->data['vendas'] = $this->contratos_model->getVendasByContrato($this->uri->segment(3));
        $this->data['sistemas_contrato'] = $this->contratos_model->getSistemasByContrato($this->uri->segment(3));
        $this->data['faturas'] = $this->contratos_model->getCobrancasByContrato($this->uri->segment(3));
        $this->data['anexos'] = $this->contratos_model->getAnexos($this->uri->segment(3));
        $this->data['checklists'] = $this->db->where('contratos_id', $this->uri->segment(3))->order_by('idChecklist', 'DESC')->get('os_checklists')->result();

        $this->data['view'] = 'contratos/editarContrato';
        return $this->layout();
    }

    public function excluir()
    {
        $id =  $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir contrato.');
            redirect(site_url('contratos/gerenciar/'));
        }

        // Excluir Anexos Fisicos e do Banco
        $this->load->model('Contratos_model');
        $anexos = $this->Contratos_model->getAnexos($id);
        foreach ($anexos as $a) {
            if (file_exists($a->path . DIRECTORY_SEPARATOR . $a->anexo)) {
                unlink($a->path . DIRECTORY_SEPARATOR . $a->anexo);
            }
            if ($a->thumb != null && file_exists($a->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $a->thumb)) {
                unlink($a->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $a->thumb);
            }
            $this->db->where('idAnexos', $a->idAnexos)->delete('anexos');
        }

        // Excluir dependências de Sistemas do Contrato
        $sistemas_contrato = $this->Contratos_model->getSistemasByContrato($id);
        foreach ($sistemas_contrato as $sc) {
            $this->db->where('sistemas_contratos_id', $sc->idSistemas_contratos)->delete('sistemas_contratos_checks');
        }
        $this->db->where('contratos_id', $id)->delete('sistemas_contratos');

        // Tenta excluir o contrato (Se tiver OS ou Venda atrelada, o MySQL vai bloquear via Constraint, 
        // o que é o correto para não perder integridade financeira/histórica).
        $this->contratos_model->delete('contratos', 'idContratos', $id);

        $this->session->set_flashdata('success', 'Contrato excluído com sucesso!');
        log_info('Excluiu um Contrato - ID: ' . $id);
        redirect(site_url('contratos/gerenciar/'));
    }

    public function auto_complete_cliente()
    {
        $term = $this->input->get('term');
        $this->db->like('nomeCliente', $term);
        $this->db->limit(10);
        $query = $this->db->get('clientes');
        $json = [];
        foreach ($query->result() as $row) {
            $json[] = ['id' => $row->idClientes, 'label' => $row->nomeCliente];
        }
        echo json_encode($json);
    }
    
    // Novo metodo AJAX para buscar os contratos de um cliente
    public function get_contratos_por_cliente()
    {
        $clientes_id = $this->input->get('clientes_id');
        $this->db->where('clientes_id', $clientes_id);
        $this->db->where_in('status', ['Ativo', '1', 'Negociação']); // Permite vincular contratos Ativos e em Negociação
        $query = $this->db->get('contratos');
        $json = [];
        foreach ($query->result() as $row) {
            $json[] = ['id' => $row->idContratos, 'nome' => $row->nomeContratos . ' (' . $row->tipoContrato . ')'];
        }
        echo json_encode($json);
    }

    // ==========================================
    // SISTEMAS E CHECKS
    // ==========================================

    public function autoCompleteSistema()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->contratos_model->autoCompleteSistema($q);
        }
    }

    public function adicionarSistema()
    {
        $preco = $this->input->post('preco');
        $preco = str_replace(',', '', $preco);

        $idSistema = $this->input->post('idSistema');
        $idContrato = $this->input->post('idContratosSistemas'); // vem do form na view
        $local = $this->input->post('local');
        
        $data = [
            'sistemas_id' => $idSistema,
            'contratos_id' => $idContrato,
            'local' => $local,
            'subTotal' => $preco,
        ];

        if ($this->contratos_model->add('sistemas_contratos', $data) == true) {
            $sistemas_contratos_id = $this->db->insert_id(); // precisa do ID para inserir os checks

            // Copia os checks padrão do sistema para este contrato
            $checks = $this->db->where('sistemas_id', $idSistema)->get('sistemas_checks')->result();
            foreach ($checks as $check) {
                $checkData = [
                    'sistemas_contratos_id' => $sistemas_contratos_id,
                    'descricao' => $check->descricao,
                    'concluido' => 0
                ];
                $this->db->insert('sistemas_contratos_checks', $checkData);
            }

            // Atualiza o valorTotal do contrato no BD
            $this->recalcularValorTotalContrato($idContrato);

            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function excluirSistema()
    {
        $id = $this->input->post('idSistemas_contratos');

        // Pega o id do contrato antes de deletar para poder recalcular
        $sistemaVinculado = $this->db->where('idSistemas_contratos', $id)->get('sistemas_contratos')->row();
        $idContrato = $sistemaVinculado ? $sistemaVinculado->contratos_id : null;

        // Primeiro exclui os checks vinculados
        $this->contratos_model->delete('sistemas_contratos_checks', 'sistemas_contratos_id', $id);

        if ($this->contratos_model->delete('sistemas_contratos', 'idSistemas_contratos', $id)) {
            if ($idContrato) {
                $this->recalcularValorTotalContrato($idContrato);
            }
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    private function recalcularValorTotalContrato($idContrato)
    {
        $contrato = $this->db->where('idContratos', $idContrato)->get('contratos')->row();
        if (!$contrato) return;

        // Pega a soma dos sistemas
        $this->db->select_sum('subTotal');
        $this->db->where('contratos_id', $idContrato);
        $soma = $this->db->get('sistemas_contratos')->row()->subTotal;
        $totalSistemas = $soma ? (float)$soma : 0;

        // A Base do contrato é rigorosamente a soma dos sistemas vinculados
        $base = $totalSistemas;
        $subtotal = $base;
        
        $descontoDigitado = (float)$contrato->valorDesconto;
        $acrescimoDigitado = (float)$contrato->valorAcrescimo;
        
        $descontoFormatado = 0;
        if ($contrato->tipoDesconto == '%') {
            $descontoFormatado = $subtotal * ($descontoDigitado / 100);
        } else {
            $descontoFormatado = $descontoDigitado;
        }

        $acrescimoFormatado = 0;
        if ($contrato->tipoAcrescimo == '%') {
            $acrescimoFormatado = $subtotal * ($acrescimoDigitado / 100);
        } else {
            $acrescimoFormatado = $acrescimoDigitado;
        }

        $novoTotal = $subtotal - $descontoFormatado + $acrescimoFormatado;
        if ($novoTotal < 0) $novoTotal = 0;

        $this->db->where('idContratos', $idContrato);
        $this->db->update('contratos', [
            'valorContrato' => $base, // Atualiza a base para refletir a soma dos sistemas
            'valorTotal' => $novoTotal
        ]);
    }

    public function adicionarCheckManual()
    {
        $idSistemaContrato = $this->input->post('sistemas_contratos_id');
        $descricao = $this->input->post('descricao');

        $data = [
            'sistemas_contratos_id' => $idSistemaContrato,
            'descricao' => $descricao,
            'concluido' => 0
        ];

        if ($this->contratos_model->add('sistemas_contratos_checks', $data) == true) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function atualizarCheck()
    {
        $idCheck = $this->input->post('idCheck');
        $status = $this->input->post('status'); // 1 ou 0

        $data = ['concluido' => $status];

        if ($this->contratos_model->edit('sistemas_contratos_checks', $data, 'idSistemas_contratos_checks', $idCheck)) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function anexar()
    {
        $this->load->library('upload');
        $this->load->library('image_lib');

        $directory = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'CONTRATOS-' . $this->input->post('idContrato');

        // If it exist, check if it's a directory
        if (! is_dir($directory . DIRECTORY_SEPARATOR . 'thumbs')) {
            // make directory for images and thumbs
            try {
                mkdir($directory . DIRECTORY_SEPARATOR . 'thumbs', 0755, true);
            } catch (Exception $e) {
                echo json_encode(['result' => false, 'mensagem' => $e->getMessage()]);
                exit();
            }
        }

        $upload_conf = [
            'upload_path' => $directory,
            'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG|pdf|PDF|cdr|CDR|docx|DOCX|txt|heic|HEIC|webp|WEBP', // formatos permitidos para anexos de contratos
            'max_size' => 0,
        ];

        $this->upload->initialize($upload_conf);

        foreach ($_FILES['userfile'] as $key => $val) {
            $i = 1;
            foreach ($val as $v) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $v;
                $i++;
            }
        }
        unset($_FILES['userfile']);

        $error = [];
        $success = [];

        foreach ($_FILES as $field_name => $file) {
            if (empty($file['name'])) {
                continue;
            }

            if (! $this->upload->do_upload($field_name)) {
                $error['upload'][] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();

                // Gera um nome de arquivo aleatório mantendo a extensão original
                $new_file_name = uniqid() . '.' . pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $new_file_path = $upload_data['file_path'] . $new_file_name;

                rename($upload_data['full_path'], $new_file_path);

                if ($upload_data['is_image'] == 1) {
                    $resize_conf = [
                        'source_image' => $new_file_path,
                        'new_image' => $upload_data['file_path'] . 'thumbs' . DIRECTORY_SEPARATOR . 'thumb_' . $new_file_name,
                        'width' => 200,
                        'height' => 125,
                    ];

                    $this->image_lib->initialize($resize_conf);

                    if (! $this->image_lib->resize()) {
                        $error['resize'][] = $this->image_lib->display_errors();
                    } else {
                        $success[] = $upload_data;
                        $this->load->model('Contratos_model');
                        $result = $this->Contratos_model->anexar($this->input->post('idContrato'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'CONTRATOS-' . $this->input->post('idContrato')), 'thumb_' . $new_file_name, $directory);
                        if (! $result) {
                            $error['db'][] = 'Erro ao inserir no banco de dados.';
                        }
                    }
                } else {
                    $success[] = $upload_data;

                    $this->load->model('Contratos_model');

                    $result = $this->Contratos_model->anexar($this->input->post('idContrato'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'CONTRATOS-' . $this->input->post('idContrato')), '', $directory);
                    if (! $result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            }
        }

        if (count($error) > 0) {
            echo json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]);
        } else {
            log_info('Adicionou anexo(s) a um Contrato. ID (Contrato): ' . $this->input->post('idContrato'));
            echo json_encode(['result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso.']);
        }
    }

    public function excluirAnexo($id = null)
    {
        if ($id == null || ! is_numeric($id)) {
            echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
        } else {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();
            $idContrato = $this->input->post('idContrato');

            unlink($file->path . DIRECTORY_SEPARATOR . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb);
            }

            if ($this->contratos_model->delete('anexos', 'idAnexos', $id) == true) {
                log_info('Removeu anexo de um Contrato. ID (Contrato): ' . $idContrato);
                echo json_encode(['result' => true, 'mensagem' => 'Anexo excluído com sucesso.']);
            } else {
                echo json_encode(['result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.']);
            }
        }
    }

    public function downloadanexo($id = null)
    {
        if ($id != null && is_numeric($id)) {
            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos', 1)->row();

            $this->load->library('zip');
            $path = $file->path;
            $this->zip->read_file($path . '/' . $file->anexo);
            $this->zip->download('file' . date('d-m-Y-H.i.s') . '.zip');
        }
    }

    public function checklist($id = null)
    {
        if (! $id) {
            redirect(site_url('contratos/gerenciar/'));
        }

        $this->data['result'] = $this->contratos_model->getById($id);
        if (!$this->data['result']) {
            redirect(site_url('contratos/gerenciar/'));
        }

        // Recupera os sistemas atrelados ao contrato (estes representam as Linhas / Locais)
        $sistemas_contrato = $this->contratos_model->getSistemasByContrato($id);
        
        $matriz = [];
        // Agrupa por Sistema (ex: "PORTA AUTOMATICA")
        if($sistemas_contrato){
            foreach($sistemas_contrato as $sc){
                $nomeSistema = trim(strtoupper($sc->nome));
                if(!isset($matriz[$nomeSistema])){
                    $matriz[$nomeSistema] = [
                        'locais' => [],
                        'checks' => []
                    ];
                    // Busca os checks genéricos deste sistema para formar as Colunas
                    $checksBase = $this->db->where('sistemas_id', $sc->sistemas_id)->get('sistemas_checks')->result();
                    foreach($checksBase as $cb){
                        $matriz[$nomeSistema]['checks'][] = $cb->descricao;
                    }
                }
                
                // Adiciona o local atual como uma linha
                $matriz[$nomeSistema]['locais'][] = $sc->local ? strtoupper($sc->local) : 'LOCAL NÃO DEFINIDO';
            }
        }
        
        $this->data['matriz'] = $matriz;
        
        // Puxa anexos (se precisar para logos, etc)
        $this->data['anexos'] = $this->contratos_model->getAnexos($id);

        $this->load->view('contratos/checklist_html', $this->data);
    }

    public function gerarOsPreventivas()
    {
        $idContratos = $this->input->post('idContratos');
        $dataPrimeiraVisita = $this->input->post('dataPrimeiraVisita');

        $contrato = $this->contratos_model->getById($idContratos);

        if (!$contrato || !$contrato->dataFinal || strtotime($contrato->dataFinal) <= time()) {
            $this->session->set_flashdata('error', 'O contrato não possui Data Final ou já está vencido.');
            redirect(site_url('contratos/editar/') . $idContratos);
            return;
        }

        // Converter dd/mm/yyyy para yyyy-mm-dd
        $dtArr = explode('/', $dataPrimeiraVisita);
        if (count($dtArr) == 3) {
            $dataCalculada = $dtArr[2] . '-' . $dtArr[1] . '-' . $dtArr[0];
        } else {
            $dataCalculada = date('Y-m-d');
        }
        
        $dataFinal = $contrato->dataFinal;
        $usuarioLogado = $this->session->userdata('id_admin') ?: $this->session->userdata('id');
        if (!$usuarioLogado) {
            $usuarioLogado = 1; // Fallback
        }
        
        $valorMensal = (float)($contrato->valorTotal ?: ($contrato->valorContrato ?: 0));
        $nomeServico = 'MANUTENÇÃO PREVENTIVA ';

        $srv = $this->db->where('nome', $nomeServico)->or_where('nome', trim($nomeServico))->get('servicos')->row();
        if (!$srv) {
            $this->db->insert('servicos', [
                'nome' => $nomeServico,
                'descricao' => 'Serviço de manutenção preventiva gerado via contrato',
                'preco' => $valorMensal
            ]);
            $idServico = $this->db->insert_id();
        } else {
            $idServico = $srv->idServicos;
        }

        $osCriadas = 0;

        while (strtotime($dataCalculada) <= strtotime($dataFinal)) {
            $dataOs = [
                'dataInicial' => $dataCalculada . ' 08:00:00',
                'dataFinal' => $dataCalculada . ' 18:00:00',
                'status' => 'A Sair | Aguard Conclusão',
                'observacoes' => 'manutenção preventiva',
                'clientes_id' => $contrato->clientes_id,
                'usuarios_id' => $usuarioLogado,
                'local' => 'externo',
                'tipo' => 'contrato',
                'manutPreventiva' => 1,
                'contratos_id' => $idContratos,
                'faturado' => 0,
                'afaturar' => 0,
                'dataAbertura' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('os', $dataOs);
            $idOsGerada = $this->db->insert_id();
            $osCriadas++;

            if ($idOsGerada && $idServico) {
                $this->db->insert('servicos_os', [
                    'os_id' => $idOsGerada,
                    'servicos_id' => $idServico,
                    'quantidade' => 1,
                    'preco' => $valorMensal,
                    'subTotal' => $valorMensal * 1
                ]);
            }

            if (!empty($contrato->tecnico_id) && $idOsGerada) {
                $tObj = $this->tecnicos_model->getById($contrato->tecnico_id);
                $this->db->insert('tecnicos_os', [
                    'os_id' => $idOsGerada,
                    'tecnico_id' => $contrato->tecnico_id,
                    'tecnicoName' => $tObj ? $tObj->nome : ''
                ]);
            }

            // Avançar 1 mês
            $dataCalculada = date('Y-m-d', strtotime('+1 month', strtotime($dataCalculada)));
        }

        if ($osCriadas > 0) {
            $this->session->set_flashdata('success', $osCriadas . ' Ordens de Serviço preventivas criadas com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'A Data da Primeira Visita é maior que a Data Final do contrato, nenhuma OS foi gerada.');
        }

        redirect(site_url('contratos/editar/') . $idContratos);
    }
}
