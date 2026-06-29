<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Os extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('os_model');
        $this->load->model('tecnicos_model');
        $this->data['menuOs'] = 'OS';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        $this->load->library('pagination');
        $this->load->model('steos_model');
        $this->load->model('contas_model');

        $where_array = [];

        $os = $this->input->get('os');
        $tecnico = $this->input->get('tecnico');
        $local = $this->input->get('local');
        $pesquisa = $this->input->get('pesquisa');
        $observacao = $this->input->get('observacao');
        $tipo = $this->input->get('tipo');
        $status = $this->input->get('status');
        $vendedor = $this->input->get('vendedor');
        $inputDe = $this->input->get('data');
        $inputAte = $this->input->get('data2');
        $afaturar = $this->input->get('afaturar');
        $manPrevnt = $this->input->get('manPrevnt');

        if ($os) { $where_array['os'] = $os; }
        if ($tecnico && $tecnico !== 'Nome do Tecnico' && $tecnico !== 'Todos') { $where_array['tecnico'] = $tecnico; }
        if ($local && $local !== 'Todos') { $where_array['local'] = $local; }
        if ($observacao) { $where_array['observacao'] = $observacao; }
        if ($pesquisa) { $where_array['pesquisa'] = $pesquisa; }
        if ($status && $status !== 'Todos') { $where_array['status'] = $status; }
        if ($tipo && $tipo !== 'Todos') { $where_array['tipo'] = $tipo; }
        if ($vendedor && $vendedor !== 'Todos') { $where_array['vendedor'] = $vendedor; }
        if ($inputDe) {
            $deArr = explode('/', $inputDe);
            if (count($deArr) == 3) {
                $where_array['de'] = $deArr[2] . '-' . $deArr[1] . '-' . $deArr[0];
            }
        }
        if ($inputAte) {
            $ateArr = explode('/', $inputAte);
            if (count($ateArr) == 3) {
                $where_array['ate'] = $ateArr[2] . '-' . $ateArr[1] . '-' . $ateArr[0];
            }
        }
        if ($afaturar == "1") { $where_array['afaturar'] = 1; }
        if ($manPrevnt == "1") { $where_array['manPrevnt'] = 1; }

        $this->data['configuration']['base_url'] = site_url('os/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->os_model->count('os', $where_array);
        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$inputDe}&data2={$inputAte}&os={$os}&tecnico={$tecnico}&local={$local}&observacao={$observacao}&tipo={$tipo}&vendedor={$vendedor}&afaturar={$afaturar}&manPrevnt={$manPrevnt}";
            $this->data['configuration']['first_url'] = base_url("index.php/os/gerenciar")."\?pesquisa={$pesquisa}&status={$status}&data={$inputDe}&data2={$inputAte}&os={$os}&tecnico={$tecnico}&local={$local}&observacao={$observacao}&tipo={$tipo}&vendedor={$vendedor}&afaturar={$afaturar}&manPrevnt={$manPrevnt}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->os_model->getOs(
            'os',
            'os.*,
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos',
            $where_array,
            $this->data['configuration']['per_page'],
            $this->uri->segment(3)
        );

        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];
        $this->data['emitente'] = $this->steos_model->getEmitente();
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['usuarios'] = $this->contas_model->getAll('usuarios');
        $this->data['view'] = 'os/os';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $this->load->model('usuarios_model');
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('termoGarantia');

            try {
                $dataInicial = explode('/', $dataInicial);
                $dataAnoHora = explode(' ', $dataInicial[2]);
                $dataAno  = $dataAnoHora[0];
                $dataHora = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

                $dataInicial = $dataAno . '-' . $dataInicial[1] . '-' . $dataInicial[0] . ' ' . $dataHora;

                if ($dataFinal) {
                    $dataFinal = explode('/', $dataFinal);
                    $dataAnoHora = explode(' ', $dataFinal[2]);
                    $dataAnoF  = $dataAnoHora[0];
                    $dataHoraF = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

                    $dataFinal = $dataAnoF . '-' . $dataFinal[1] . '-' . $dataFinal[0] . ' ' . $dataHoraF;
                } else {
                    $dataFinal = date('Y-m-d H:i:s');
                }

                $termoGarantiaId = (! $termoGarantiaId == null || ! $termoGarantiaId == '')
                    ? $this->input->post('garantias_id')
                    : null;
            } catch (Exception $e) {
                $dataInicial = date('Y-m-d H:i:s');
                $dataFinal = date('Y-m-d H:i:s');
            }

            $data = [
                'dataAbertura' => date('Y-m-d H:i:s'),
                'dataInicial' => $dataInicial,
                'clientes_id' => $this->input->post('clientes_id'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'contratos_id' => $this->input->post('contratos_id') ?: null,
                'dataFinal' => $dataFinal,
                'garantia' => set_value('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => set_value('descricaoProduto'),
                'defeito' => set_value('defeito_atendimento'),
                'defeito_encontrado' => set_value('defeito_encontrado_no_atendimento'),
                'status' => set_value('status'),
                'observacoes' => set_value('observacoes'),
                'laudoTecnico' => set_value('laudoTecnico'),
                'local' => set_value('local'),
                'tipo' => set_value('tipo'),
                'faturado' => 0,
            ];

            if (is_numeric($id = $this->os_model->add('os', $data, true))) {
                $this->load->model('steos_model');
                $this->load->model('usuarios_model');

                $idOs = $id;
                $os = $this->os_model->getById($idOs);
                $emitente = $this->steos_model->getEmitente();

                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // adiciona tecnicos
                if ($tecnicos_ids = $this->input->post('tecnico')) {
                    foreach ($tecnicos_ids as $tecnico_id) {
                        if (!empty($tecnico_id)) {
                            $this->load->model('tecnicos_model');
                            $nameTecnico = $this->tecnicos_model->getById($tecnico_id);
                        }
                        $dataTecnico = [
                            'os_id' => $idOs,
                            'tecnico_id' => $tecnico_id,
                            'tecnicoName' => isset($nameTecnico) ? $nameTecnico->nome : ''
                        ];
                        $this->tecnicos_os_model->add('tecnicos_os', $dataTecnico, true);
                    };
                }
                
                // adiciona equipamentos
                if ($equipamentos = $this->input->post('equipamentos')) {
                    foreach ($equipamentos as $equipamento) {
                        $dataEquipamento = [
                            'os_id' => $idOs,
                            'clientes_id' => $this->input->post('clientes_id'),
                            'equipamento' => $equipamento['equipamentos_value'],
                            'serie' => $equipamento['serie_value'],
                            'modelo' => $equipamento['modelo_value'],
                            'cor' => $equipamento['cor_value'],
                            'descricao' => $equipamento['descricao_value'],
                            'potecia' => $equipamento['potencia_value'],
                            'voltagem' => $equipamento['voltagem_value'],
                            'marca' => $equipamento['marcas_value'],
                            'local' => $equipamento['local_value'],
                            'defeito_declarado' => $equipamento['defeito_relatado'],
                            'defeito_encontrado' => $equipamento['defeito_encontrado'],
                        ];
                        $this->equipamentos_os_model->add('equipamentos_os', $dataEquipamento, true);
                    }
                }

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = [];
                    switch ($this->data['configuration']['os_notification']) {
                        case 'todos':
                            array_push($remetentes, $os->email);
                            array_push($remetentes, $tecnico->email);
                            array_push($remetentes, $emitente->email);
                            break;
                        case 'cliente':
                            array_push($remetentes, $os->email);
                            break;
                        case 'tecnico':
                            array_push($remetentes, $tecnico->email);
                            break;
                        case 'emitente':
                            array_push($remetentes, $emitente->email);
                            break;
                        default:
                            array_push($remetentes, $os->email);
                            break;
                    }
                    $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Criada');
                }

                $this->session->set_flashdata('success', 'OS adicionada com sucesso, você pode adicionar produtos ou serviços a essa OS nas abas de Produtos e Serviços!');
                log_info('Adicionou uma OS. ID: ' . $id);
                redirect(site_url('os/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
            }
        }
        
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['view'] = 'os/adicionarOs';

        return $this->layout();
    }

    public function duplicarOs()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');
        $this->data['custom_error'] = 'false';

        $this->load->model('usuarios_model');
        $dataInicial = $this->input->post('dataInicial');
        $dataFinal = $this->input->post('dataFinal');
        $termoGarantiaId = $this->input->post('termoGarantia');

        try {
            $dataInicial = explode('/', $dataInicial);
            $dataAnoHora = explode(' ', $dataInicial[2]);
            $dataAno  = $dataAnoHora[0];
            $dataHora = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

            $dataInicial = $dataAno . '-' . $dataInicial[1] . '-' . $dataInicial[0] . ' ' . $dataHora;

            if ($dataFinal) {
                $dataFinal = explode('/', $dataFinal);
                $dataAnoHora = explode(' ', $dataFinal[2]);
                $dataAnoF  = $dataAnoHora[0];
                $dataHoraF = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

                $dataFinal = $dataAnoF . '-' . $dataFinal[1] . '-' . $dataFinal[0] . ' ' . $dataHoraF;
            } else {
                $dataFinal = date('Y-m-d H:i:s');
            }

            $termoGarantiaId = (!$termoGarantiaId == null || !$termoGarantiaId == '')
                ? $this->input->post('garantias_id')
                : null;
        } catch (Exception $e) {
            $dataInicial = date('Y-m-d H:i:s');
            $dataFinal = date('Y-m-d H:i:s');
        }

        $data = [
            'dataAbertura' => date('Y-m-d H:i:s'),
            'dataInicial' => $dataInicial,
            'clientes_id' => $this->input->post('clientes_id'), 
            'usuarios_id' => $this->input->post('usuarios_id'), 
            'contratos_id' => $this->input->post('contratos_id') ?: null,
            'dataFinal' => $dataFinal,
            'garantia' => set_value('garantia'),
            'garantias_id' => $termoGarantiaId,
            'descricaoProduto' => set_value('descricaoProduto'),
            'defeito' => set_value('defeito_atendimento'),
            'defeito_encontrado' => set_value('defeito_encontrado_no_atendimento'),
            'status' => set_value('status'),
            'observacoes' => set_value('observacoes'),
            'laudoTecnico' => set_value('laudoTecnico'),
            'local' => set_value('local'),
            'tipo' => set_value('tipo'),
            'faturado' => 0,
        ];

        if (is_numeric($id = $this->os_model->add('os', $data, true))) {

            $this->load->model('steos_model');
            $idOs = $id;
            $os = $this->os_model->getById($idOs);
            $emitente = $this->steos_model->getEmitente();
            $tecnico = $this->usuarios_model->getById($os->usuarios_id);

            // adiciona tecnicos
            if ($tecnicos = $this->input->post('tecnicos')) {
                foreach ($tecnicos as $tecnicoPost) {
                    if (!empty($tecnicoPost['idTecnicos_os'])) {
                        $this->load->model('tecnicos_model');
                        $nameTecnico = $this->tecnicos_model->getById($tecnicoPost['tecnico_id']);
                        $dataTecnico = [
                            'os_id' => $idOs,
                            'tecnico_id' => $tecnicoPost['tecnico_id'],
                            'tecnicoName' => $nameTecnico->nome
                        ];
                        $this->tecnicos_os_model->add('tecnicos_os', $dataTecnico, true);
                    } else {
                        if (!isset($tecnicoPost['tecnico_id'])) {
                            $this->tecnicos_os_model->delete('tecnicos_os', 'idTecnicos_os', $tecnicoPost['idTecnicos_os']);
                        }
                    }
                };
            }

            // adiciona equipamentos
            if ($equipamentos = $this->input->post('equipamentos')) {
                foreach ($equipamentos as $equipamento) {
                    if (empty($equipamento['idEquipamentos_os'])) {
                        $dataEquipamento = [
                            'os_id' => $idOs,
                            'clientes_id' => $this->input->post('clientes_id'),
                            'equipamento' => $equipamento['equipamentos_value'],
                            'serie' => $equipamento['serie_value'],
                            'modelo' => $equipamento['modelo_value'],
                            'cor' => $equipamento['cor_value'],
                            'descricao' => $equipamento['descricao_value'],
                            'potecia' => $equipamento['potencia_value'],
                            'voltagem' => $equipamento['voltagem_value'],
                            'marca' => $equipamento['marcas_value'],
                            'local' => $equipamento['local_value'],
                            'defeito_declarado' => $equipamento['defeito_relatado'],
                            'defeito_encontrado' => $equipamento['defeito_encontrado'],
                        ];
                        $this->equipamentos_os_model->add('equipamentos_os', $dataEquipamento, true);
                    } else if (!isset($equipamento['equipamento_id'])) {
                        $this->equipamentos_os_model->delete('equipamentos_os', 'idEquipamentos_os', $equipamento['idEquipamentos_os']);
                    } else {
                        $dataEquipamento = [
                            'os_id' => $idOs,
                            'clientes_id' => $this->input->post('clientes_id'),
                            'equipamento' => $equipamento['equipamentos_value'],
                            'serie' => $equipamento['serie_value'],
                            'modelo' => $equipamento['modelo_value'],
                            'cor' => $equipamento['cor_value'],
                            'descricao' => $equipamento['descricao_value'],
                            'potecia' => $equipamento['potencia_value'],
                            'voltagem' => $equipamento['voltagem_value'],
                            'marca' => $equipamento['marcas_value'],
                            'local' => $equipamento['local_value'],
                            'defeito_declarado' => $equipamento['defeito_relatado'],
                            'defeito_encontrado' => $equipamento['defeito_encontrado'],
                        ];
                        $this->equipamentos_os_model->edit('equipamentos_os', $dataEquipamento, 'idEquipamentos_os', $equipamento['idEquipamentos_os']);
                    }
                }
            }

            // Verificar configuração de notificação
            if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                $remetentes = [];
                switch ($this->data['configuration']['os_notification']) {
                    case 'todos':
                        array_push($remetentes, $os->email);
                        array_push($remetentes, $tecnico->email);
                        array_push($remetentes, $emitente->email);
                        break;
                    case 'cliente':
                        array_push($remetentes, $os->email);
                        break;
                    case 'tecnico':
                        array_push($remetentes, $tecnico->email);
                        break;
                    case 'emitente':
                        array_push($remetentes, $emitente->email);
                        break;
                    default:
                        array_push($remetentes, $os->email);
                        break;
                }
                $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Editada');
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true, 'tecnico' => $tecnicos, 'os' => $idOs]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function editar()
    {
        $action = $this->input->post("action");
        if ($action) {
            $output = array();
            if ($action == "submit") {
                try {
                    $person_Name = $this->input->post('signatureName');
                    $person_Signature = $this->input->post('signature');
                    $idOs = $this->input->post('idOs');
                    $doc = $this->input->post('signatureDoc');
                    $dataAssinatura = [
                        'nameAssinatura' => $person_Name,
                        'assinatura' => $person_Signature,
                        'os_id'      => $idOs,
                        'data'      => date('Y-m-d'),
                        'doc'      => $doc
                    ];

                    $dataOs = $this->os_model->getById($this->input->post('idOs'));
                    if ((int)$dataOs->produtos_subTotal == 0 || (int)$dataOs->servicos_subTotal == 0) {
                        $data2 = [
                            'signature' => 1,
                            'status' => 'Finalizado'
                        ];
                        $this->os_model->edit('os', $data2, 'idOs', $this->input->post('idOs'));
                    } else {
                        $data2 = [
                            'signature' => 1,
                            'afaturar' => 1,
                            'status' => 'Finalizado'
                        ];
                        $this->os_model->edit('os', $data2, 'idOs', $this->input->post('idOs'));
                    }

                    $idRetornado = $this->os_model->add('assinatura', $dataAssinatura, true);
                    $instert = $this->os_model->getByIdAssinatura($idRetornado);
                    $output["data"] = $instert;
                    $output["msg"] = "Succesfully Save as JSON";
                } catch (PDOException $e) {
                    $output["msg"] = "There is some problem in connection: " . $e->getMessage();
                }
            }
            if ($action == "load") {
                $idOs = $this->input->post('id_Os');
                $assinatura = $this->os_model->getByIdAssinaturaExtr($idOs);
                if ($assinatura) {
                    $output["data"] = $assinatura;
                    $output["msg"] = "carregou";
                } else {
                    $output["data"] = false;
                }
            }
            echo json_encode($output);
            exit;
        }

        $dataImg = $this->input->post("image");
        if ($dataImg) {
            $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
        }

        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3)) || ! $this->os_model->getById($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'OS não encontrada ou parâmetro inválido.');
            redirect('os/gerenciar');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar O.S.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->load->model('usuarios_model');
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');
        $this->load->model('classificacao_financeira_model');
        $this->load->model('contas_model');
        
        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        $this->data['editavel'] = $this->os_model->isEditable($this->input->post('idOs'));
        if (! $this->data['editavel']) {
            $this->session->set_flashdata('error', 'Esta OS já e seu status não pode ser alterado e nem suas informações atualizadas. Por favor abrir uma nova OS.');

            redirect(site_url('os'));
        }

        if ($this->form_validation->run('os') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataInicial = $this->input->post('dataInicial');
            $dataFinal = $this->input->post('dataFinal');
            $termoGarantiaId = $this->input->post('garantias_id') ?: null;

            try {
                $dataInicial = explode('/', $dataInicial);
                $dataAnoHora = explode(' ', $dataInicial[2]);
                $dataAno  = $dataAnoHora[0];
                $dataHora = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

                $dataInicial = $dataAno . '-' . $dataInicial[1] . '-' . $dataInicial[0] . ' ' . $dataHora;

                $dataFinal = explode('/', $dataFinal);
                $dataAnoHora = explode(' ', $dataFinal[2]);
                $dataAnoF  = $dataAnoHora[0];
                $dataHoraF = isset($dataAnoHora[1]) ? $dataAnoHora[1] . ":00" : "00:00:00";

                $dataFinal = $dataAnoF . '-' . $dataFinal[1] . '-' . $dataFinal[0] . ' ' . $dataHoraF;
            } catch (Exception $e) {
                $dataInicial = date('Y-m-d H:i:s');
                $dataFinal = date('Y-m-d H:i:s');
            }

            $data = [
                'dataInicial' => $dataInicial,
                'dataFinal' => $dataFinal,
                'garantia' => $this->input->post('garantia'),
                'garantias_id' => $termoGarantiaId,
                'descricaoProduto' => $this->input->post('descricaoProduto'),
                'defeito' => set_value('defeito_atendimento'),
                'defeito_encontrado' => set_value('defeito_encontrado_no_atendimento'),
                'status' => $this->input->post('status'),
                'observacoes' => $this->input->post('observacoes'),
                'laudoTecnico' => $this->input->post('laudoTecnico'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'clientes_id' => $this->input->post('clientes_id'),
                'contratos_id' => $this->input->post('contratos_id') ?: null,
            ];
            $os = $this->os_model->getById($this->input->post('idOs'));

            //Verifica para poder fazer a devolução do produto para o estoque caso OS seja cancelada.

            if (strtolower($this->input->post('status')) == 'cancelado' && strtolower($os->status) != 'cancelado') {
                $this->devolucaoEstoque($this->input->post('idOs'));
            }

            if (strtolower($os->status) == 'cancelado' && strtolower($this->input->post('status')) != 'cancelado') {
                $this->debitarEstoque($this->input->post('idOs'));
            }

            if ($this->os_model->edit('os', $data, 'idOs', $this->input->post('idOs')) == true) {
                $this->load->model('steos_model');
                $this->load->model('usuarios_model');

                $idOs = $this->input->post('idOs');

                $os = $this->os_model->getById($idOs);
                $emitente = $this->steos_model->getEmitente();
                $tecnico = $this->usuarios_model->getById($os->usuarios_id);

                // adiciona tecnicos
                if ($tecnicos = $this->input->post('tecnicos')) {
                    foreach ($tecnicos as $tecnicoPost) {
                        if (empty($tecnicoPost['idTecnicos_os'])) {
                            $this->load->model('tecnicos_model');
                            $nameTecnico = $this->tecnicos_model->getById($tecnicoPost['tecnico_id']);
                            $dataTecnico = [
                                'os_id' => $idOs,
                                'tecnico_id' => $tecnicoPost['tecnico_id'],
                                'tecnicoName' => $nameTecnico->nome
                            ];
                            $this->tecnicos_os_model->add('tecnicos_os', $dataTecnico, true);
                        } else {
                            if (!isset($tecnicoPost['tecnico_id'])) {
                                $this->tecnicos_os_model->delete('tecnicos_os', 'idTecnicos_os', $tecnicoPost['idTecnicos_os']);
                            }
                        }
                    };
                }

                // adiciona equipamentos
                if ($equipamentos = $this->input->post('equipamentos')) {
                    foreach ($equipamentos as $equipamento) {
                        if (empty($equipamento['idEquipamentos_os'])) {
                            $dataEquipamento = [
                                'os_id' => $idOs,
                                'clientes_id' => $this->input->post('clientes_id'),
                                'equipamento' => $equipamento['equipamentos_value'],
                                'serie' => $equipamento['serie_value'],
                                'modelo' => $equipamento['modelo_value'],
                                'cor' => $equipamento['cor_value'],
                                'descricao' => $equipamento['descricao_value'],
                                'potecia' => $equipamento['potencia_value'],
                                'voltagem' => $equipamento['voltagem_value'],
                                'marca' => $equipamento['marcas_value'],
                                'local' => $equipamento['local_value'],
                                'defeito_declarado' => $equipamento['defeito_relatado'],
                                'defeito_encontrado' => $equipamento['defeito_encontrado'],
                            ];
                            $this->equipamentos_os_model->add('equipamentos_os', $dataEquipamento, true);
                        } else if (!isset($equipamento['equipamento_id'])) {
                            $this->equipamentos_os_model->delete('equipamentos_os', 'idEquipamentos_os', $equipamento['idEquipamentos_os']);
                        } else {
                            $dataEquipamento = [
                                'os_id' => $idOs,
                                'clientes_id' => $this->input->post('clientes_id'),
                                'equipamento' => $equipamento['equipamentos_value'],
                                'serie' => $equipamento['serie_value'],
                                'modelo' => $equipamento['modelo_value'],
                                'cor' => $equipamento['cor_value'],
                                'descricao' => $equipamento['descricao_value'],
                                'potecia' => $equipamento['potencia_value'],
                                'voltagem' => $equipamento['voltagem_value'],
                                'marca' => $equipamento['marcas_value'],
                                'local' => $equipamento['local_value'],
                                'defeito_declarado' => $equipamento['defeito_relatado'],
                                'defeito_encontrado' => $equipamento['defeito_encontrado'],
                            ];
                            $this->equipamentos_os_model->edit('equipamentos_os', $dataEquipamento, 'idEquipamentos_os', $equipamento['idEquipamentos_os']);
                        }
                    }
                }

                // Verificar configuração de notificação
                if ($this->data['configuration']['os_notification'] != 'nenhum' && $this->data['configuration']['email_automatico'] == 1) {
                    $remetentes = [];
                    switch ($this->data['configuration']['os_notification']) {
                        case 'todos':
                            array_push($remetentes, $os->email);
                            array_push($remetentes, $tecnico->email);
                            array_push($remetentes, $emitente->email);
                            break;
                        case 'cliente':
                            array_push($remetentes, $os->email);
                            break;
                        case 'tecnico':
                            array_push($remetentes, $tecnico->email);
                            break;
                        case 'emitente':
                            array_push($remetentes, $emitente->email);
                            break;
                        default:
                            array_push($remetentes, $os->email);
                            break;
                    }
                    $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço - Editada');
                }

                $this->session->set_flashdata('success', 'Os editada com sucesso!');
                log_info('Alterou uma OS. ID: ' . $this->input->post('idOs'));
                redirect(site_url('os/editar/') . $this->input->post('idOs'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $os = $this->os_model->getById($this->uri->segment(3));

        $this->data['usuarios'] = $this->contas_model->getAll('usuarios');
        $this->data['classificacao_financeira'] = $this->classificacao_financeira_model->getAll();
        $this->data['contas'] = $this->contas_model->getAll('contas');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));
        $this->data['equipamentos'] = $this->equipamentos_os_model->getAll($this->uri->segment(3));
        $this->data['tecnicos_os'] = $this->tecnicos_os_model->getById($this->uri->segment(3));

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
        }

        $this->load->model('steos_model');
        $this->load->model('financeiro_model');
        $this->data['emitente'] = $this->steos_model->getEmitente();
        
        $where = "vendas_id = '$os->idOs'";
        $this->data['lancamentos'] = $this->financeiro_model->get1('lancamentos', '*', $where);

        $this->data['tecnicos'] = $this->tecnicos_model->getAll();

        $this->data['view'] = 'os/editarOs';

        return $this->layout();
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->data['texto_de_notificacao'] = $this->data['configuration']['notifica_whats'];

        $this->load->model('steos_model');
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');
        $this->load->model('modelos_model');
        $this->data['modelos'] = $this->modelos_model->getAll();

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['anotacoes'] = $this->os_model->getAnotacoes($this->uri->segment(3));
        $this->data['editavel'] = $this->os_model->isEditable($this->uri->segment(3));
        $this->data['equipamentos'] = $this->equipamentos_os_model->getAll($this->uri->segment(3));
        $this->data['tecnicos_os'] = $this->tecnicos_os_model->getById($this->uri->segment(3));
        $this->data['qrCode'] = $this->os_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['modalGerarPagamento'] = $this->load->view(
            'cobrancas/modalGerarPagamento',
            [
                'id' => $this->uri->segment(3),
                'tipo' => 'os',
            ],
            true
        );
        $this->data['view'] = 'os/visualizarOs';
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        if ($return = $this->os_model->valorTotalOS($this->uri->segment(3))) {
            $this->data['totalServico'] = $return['totalServico'];
            $this->data['totalProdutos'] = $return['totalProdutos'];
        }

        return $this->layout();
    }

    public function recibo()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');

        $this->load->model('modelos_model');
        $this->data['modelos'] = $this->modelos_model->getAll();

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['assinatura'] = $this->os_model->getByIdAssinaturaExtr($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();
        $this->data['equipamentos'] = $this->equipamentos_os_model->getAll($this->uri->segment(3));
        $this->data['tecnicos_os'] = $this->tecnicos_os_model->getById($this->uri->segment(3));
        $this->data['qrCode'] = $this->os_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        $this->load->view('os/imprimirOsRecibo', $this->data);
    }

    public function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma1 += $cpf[$i] * (10 - $i);
        }
        $resto1 = $soma1 % 11;
        $dv1 = ($resto1 < 2) ? 0 : 11 - $resto1;
        if ($dv1 != $cpf[9]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma2 += $cpf[$i] * (11 - $i);
        }
        $resto2 = $soma2 % 11;
        $dv2 = ($resto2 < 2) ? 0 : 11 - $resto2;

        return $dv2 == $cpf[10];
    }

    public function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0, $pos = 5; $i < 12; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma1 += $cnpj[$i] * $pos;
        }
        $dv1 = ($soma1 % 11 < 2) ? 0 : 11 - ($soma1 % 11);
        if ($dv1 != $cnpj[12]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0, $pos = 6; $i < 13; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma2 += $cnpj[$i] * $pos;
        }
        $dv2 = ($soma2 % 11 < 2) ? 0 : 11 - ($soma2 % 11);

        return $dv2 == $cnpj[13];
    }

    public function formatarChave($chave)
    {
        if ($this->validarCPF($chave)) {
            return substr($chave, 0, 3) . '.' . substr($chave, 3, 3) . '.' . substr($chave, 6, 3) . '-' . substr($chave, 9);
        } elseif ($this->validarCNPJ($chave)) {
            return substr($chave, 0, 2) . '.' . substr($chave, 2, 3) . '.' . substr($chave, 5, 3) . '/' . substr($chave, 8, 4) . '-' . substr($chave, 12);
        } elseif (strlen($chave) === 11) {
            return '(' . substr($chave, 0, 2) . ') ' . substr($chave, 2, 5) . '-' . substr($chave, 7);
        }

        return $chave;
    }

    public function imprimir()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');
        $this->load->model('modelos_model');
        $this->data['modelos'] = $this->modelos_model->getAll();

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['anexos'] = $this->os_model->getAnexos($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();
        $this->data['equipamentos'] = $this->equipamentos_os_model->getAll($this->uri->segment(3));
        $this->data['tecnicos_os'] = $this->tecnicos_os_model->getById($this->uri->segment(3));

        if ($this->data['configuration']['pix_key']) {
            $this->data['qrCode'] = $this->os_model->getQrCode(
                $this->uri->segment(3),
                $this->data['configuration']['pix_key'],
                $this->data['emitente']
            );
            $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        }
        
        $this->data['imprimirAnexo'] = isset($_ENV['IMPRIMIR_ANEXOS']) ? (filter_var($_ENV['IMPRIMIR_ANEXOS'] ?? false, FILTER_VALIDATE_BOOLEAN)) : false;

        $this->load->view('os/imprimirOs', $this->data);
    }

    public function imprimirTermica()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('steos_model');
        $this->load->model('tecnicos_os_model');
        $this->load->model('equipamentos_os_model');

        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        $this->data['assinatura'] = $this->os_model->getByIdAssinaturaExtr($this->uri->segment(3));
        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();
        $this->data['equipamentos'] = $this->equipamentos_os_model->getAll($this->uri->segment(3));
        $this->data['tecnicos_os'] = $this->tecnicos_os_model->getById($this->uri->segment(3));
        $this->data['qrCode'] = $this->os_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        $this->load->view('os/imprimirOsTermica', $this->data);
    }

    public function enviar_email()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('steos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para enviar O.S. por e-mail.');
            redirect(base_url());
        }

        $this->load->model('steos_model');
        $this->load->model('usuarios_model');
        $this->data['result'] = $this->os_model->getById($this->uri->segment(3));
        if (! isset($this->data['result']->email)) {
            $this->session->set_flashdata('error', 'O cliente não tem e-mail cadastrado.');
            redirect(site_url('os'));
        }

        $this->data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $this->data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $this->data['emitente'] = $this->steos_model->getEmitente();

        if (! isset($this->data['emitente']->email)) {
            $this->session->set_flashdata('error', 'Efetue o cadastro dos dados de emitente');
            redirect(site_url('os'));
        }

        $idOs = $this->uri->segment(3);

        $emitente = $this->data['emitente'];
        $tecnico = $this->usuarios_model->getById($this->data['result']->usuarios_id);

        // Verificar configuração de notificação
        $ValidarEmail = false;
        if ($this->data['configuration']['os_notification'] != 'nenhum') {
            $remetentes = [];
            switch ($this->data['configuration']['os_notification']) {
                case 'todos':
                    array_push($remetentes, $this->data['result']->email);
                    array_push($remetentes, $tecnico->email);
                    array_push($remetentes, $emitente->email);
                    $ValidarEmail = true;
                    break;
                case 'cliente':
                    array_push($remetentes, $this->data['result']->email);
                    $ValidarEmail = true;
                    break;
                case 'tecnico':
                    array_push($remetentes, $tecnico->email);
                    break;
                case 'emitente':
                    array_push($remetentes, $emitente->email);
                    break;
                default:
                    array_push($remetentes, $this->data['result']->email);
                    $ValidarEmail = true;
                    break;
            }

            if ($ValidarEmail) {
                if (empty($this->data['result']->email) || ! filter_var($this->data['result']->email, FILTER_VALIDATE_EMAIL)) {
                    $this->session->set_flashdata('error', 'Por favor preencha o email do cliente');
                    redirect(site_url('os/visualizar/') . $this->uri->segment(3));
                }
            }

            $enviouEmail = $this->enviarOsPorEmail($idOs, $remetentes, 'Ordem de Serviço');

            if ($enviouEmail) {
                $this->session->set_flashdata('success', 'O email está sendo processado e será enviado em breve.');
                log_info('Enviou e-mail para o cliente: ' . $this->data['result']->nomeCliente . '. E-mail: ' . $this->data['result']->email);
                redirect(site_url('os'));
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao enviar e-mail.');
                redirect(site_url('os'));
            }
        }

        $this->session->set_flashdata('success', 'O sistema está com uma configuração ativada para não notificar. Entre em contato com o administrador.');
        redirect(site_url('os'));
    }

    private function devolucaoEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '+');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' voltou ao estoque. Quantidade: ' . $p->quantidade . '. Motivo: Cancelamento/Exclusão');
                }
            }
        }
    }

    private function debitarEstoque($id)
    {
        if ($produtos = $this->os_model->getProdutos($id)) {
            $this->load->model('produtos_model');
            if ($this->data['configuration']['control_estoque']) {
                foreach ($produtos as $p) {
                    $this->produtos_model->updateEstoque($p->produtos_id, $p->quantidade, '-');
                    log_info('ESTOQUE: Produto id ' . $p->produtos_id . ' baixa do estoque. Quantidade: ' . $p->quantidade . '. Motivo: Mudou status que já estava Cancelado para outro');
                }
            }
        }
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir O.S.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        $os = $this->os_model->getByIdCobrancas($id);
        if ($os == null) {
            $os = $this->os_model->getById($id);
            if ($os == null) {
                $this->session->set_flashdata('error', 'Erro ao tentar excluir OS.');
                redirect(base_url() . 'index.php/os/gerenciar/');
            }
        }

        if (isset($os->idCobranca) != null) {
            if ($os->status == 'canceled') {
                $this->os_model->delete('cobrancas', 'os_id', $id);
            } else {
                $this->session->set_flashdata('error', 'Existe uma cobrança associada a esta OS, deve cancelar e/ou excluir a cobrança primeiro!');
                redirect(site_url('os/gerenciar/'));
            }
        }

        $osStockRefund = $this->os_model->getById($id);
        //Verifica para poder fazer a devolução do produto para o estoque caso OS seja excluida.
        if (strtolower($osStockRefund->status) != 'cancelado') {
            $this->devolucaoEstoque($id);
        }

        $this->os_model->delete('servicos_os', 'os_id', $id);
        $this->os_model->delete('produtos_os', 'os_id', $id);
        $this->os_model->delete('anexos', 'os_id', $id);
        $this->os_model->delete('os', 'idOs', $id);
        if ((int) $os->faturado === 1) {
            $this->os_model->delete('lancamentos', 'descricao', "Fatura de OS - #${id}");
        }

        log_info('Removeu uma OS. ID: ' . $id);
        $this->session->set_flashdata('success', 'OS excluída com sucesso!');
        redirect(site_url('os/gerenciar/'));
    }

    public function autoCompleteProduto()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteProduto($q);
        }
    }

    public function autoCompleteProdutoSaida()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteProdutoSaida($q);
        }
    }

    public function autoCompleteCliente()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteCliente($q);
        }
    }

    public function autoCompleteUsuario()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteUsuario($q);
        }
    }

    public function autoCompleteTermoGarantia()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteTermoGarantia($q);
        }
    }

    public function autoCompleteServico()
    {
        if ($this->input->get('term')) {
            $q = strtolower($this->input->get('term'));
            $this->os_model->autoCompleteServico($q);
        }
    }

    public function adicionarProduto()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_produto_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $preco = $this->input->post('preco');
        $quantidade = $this->input->post('quantidade');
        $subtotal = $preco * $quantidade;
        $produto = $this->input->post('idProduto');
        $data = [
            'quantidade' => $quantidade,
            'subTotal' => $subtotal,
            'produtos_id' => $produto,
            'preco' => $preco,
            'os_id' => $this->input->post('idOsProduto'),
        ];

        $id = $this->input->post('idOsProduto');
        $os = $this->os_model->getById($id);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar inserir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->add('produtos_os', $data) == true) {
            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '-');
            }

            $this->os_model->edit('os', ['desconto' => 0.00, 'valor_desconto' => 0.00, 'tipo_desconto' => null], 'idOs', $id);

            log_info('Adicionou produto a uma OS. ID (OS): ' . $this->input->post('idOsProduto'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirProduto()
    {
        $id = $this->input->post('idProduto');
        $idOs = $this->input->post('idOs');

        $os = $this->os_model->getById($idOs);
        if ($os == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir produto na OS.');
            redirect(base_url() . 'index.php/os/gerenciar/');
        }

        if ($this->os_model->delete('produtos_os', 'idProdutos_os', $id) == true) {
            $quantidade = $this->input->post('quantidade');
            $produto = $this->input->post('produto');

            $this->load->model('produtos_model');

            if ($this->data['configuration']['control_estoque']) {
                $this->produtos_model->updateEstoque($produto, $quantidade, '+');
            }

            $this->os_model->edit('os', ['desconto' => 0.00, 'valor_desconto' => 0.00, 'tipo_desconto' => null], 'idOs', $idOs);

            log_info('Removeu produto de uma OS. ID (OS): ' . $idOs);

            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function adicionarServico()
    {
        $this->load->library('form_validation');

        if ($this->form_validation->run('adicionar_servico_os') === false) {
            $errors = validation_errors();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($errors));
        }

        $data = [
            'servicos_id' => $this->input->post('idServico'),
            'quantidade' => $this->input->post('quantidade'),
            'preco' => $this->input->post('preco'),
            'os_id' => $this->input->post('idOsServico'),
            'subTotal' => $this->input->post('preco') * $this->input->post('quantidade'),
        ];

        if ($this->os_model->add('servicos_os', $data) == true) {
            log_info('Adicionou serviço a uma OS. ID (OS): ' . $this->input->post('idOsServico'));

            $this->os_model->edit('os', ['desconto' => 0.00, 'valor_desconto' => 0.00, 'tipo_desconto' => null], 'idOs', $this->input->post('idOsServico'));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false]));
        }
    }

    public function excluirServico()
    {
        $ID = $this->input->post('idServico');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('servicos_os', 'idServicos_os', $ID) == true) {
            log_info('Removeu serviço de uma OS. ID (OS): ' . $idOs);
            $this->os_model->edit('os', ['desconto' => 0.00, 'valor_desconto' => 0.00, 'tipo_desconto' => null], 'idOs', $idOs);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function anexar()
    {
        $this->load->library('upload');
        $this->load->library('image_lib');

        $directory = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico');

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
            'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG|pdf|PDF|cdr|CDR|docx|DOCX|txt|heic|HEIC|webp|WEBP', // formatos permitidos para anexos de os
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
                        $this->load->model('Os_model');
                        $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), 'thumb_' . $new_file_name, $directory);
                        if (! $result) {
                            $error['db'][] = 'Erro ao inserir no banco de dados.';
                        }
                    }
                } else {
                    $success[] = $upload_data;

                    $this->load->model('Os_model');

                    $result = $this->Os_model->anexar($this->input->post('idOsServico'), $new_file_name, base_url('assets' . DIRECTORY_SEPARATOR . 'anexos' . DIRECTORY_SEPARATOR . date('m-Y') . DIRECTORY_SEPARATOR . 'OS-' . $this->input->post('idOsServico')), '', $directory);
                    if (! $result) {
                        $error['db'][] = 'Erro ao inserir no banco de dados.';
                    }
                }
            }
        }

        if (count($error) > 0) {
            echo json_encode(['result' => false, 'mensagem' => 'Ocorreu um erro ao processar os arquivos.', 'errors' => $error]);
        } else {
            log_info('Adicionou anexo(s) a uma OS. ID (OS): ' . $this->input->post('idOsServico'));
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
            $idOs = $this->input->post('idOs');

            unlink($file->path . DIRECTORY_SEPARATOR . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file->thumb);
            }

            if ($this->os_model->delete('anexos', 'idAnexos', $id) == true) {
                log_info('Removeu anexo de uma OS. ID (OS): ' . $idOs);
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

    public function adicionarDesconto()
    {
        if ($this->input->post('desconto') == '') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['messages' => 'Campo desconto vazio']));
        } else {
            $idOs = $this->input->post('idOs');
            $data = [
                'tipo_desconto' => $this->input->post('tipoDesconto'),
                'desconto' => $this->input->post('desconto'),
                'valor_desconto' => $this->input->post('resultado'),
            ];
            $editavel = $this->os_model->isEditable($idOs);
            if (! $editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages', 'Desconto não pode ser adiciona. Os não ja Faturada/Cancelada']));
            }
            if ($this->os_model->edit('os', $data, 'idOs', $idOs) == true) {
                log_info('Adicionou um desconto na OS. ID: ' . $idOs);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(201)
                    ->set_output(json_encode(['result' => true, 'messages' => 'Desconto adicionado com sucesso!']));
            } else {
                log_info('Ocorreu um erro ao tentar adiciona desconto a OS: ' . $idOs);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages', 'Ocorreu um erro ao tentar adiciona desconto a OS.']));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode(['result' => false, 'messages', 'Ocorreu um erro ao tentar adiciona desconto a OS.']));
    }

    public function faturar()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('receita') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $vencimento = $this->input->post('vencimento');
            $recebimento = $this->input->post('recebimento');

            try {
                $vencimento = DateTime::createFromFormat('d/m/Y', $vencimento)->format('Y-m-d');
                if ($recebimento != null) {
                    $recebimento = DateTime::createFromFormat('d/m/Y', $recebimento)->format('Y-m-d');
                }
            } catch (Exception $e) {
                $vencimento = date('Y-m-d');
            }

            $os_id = $this->input->post('os_id');
            $valorTotalData = $this->os_model->valorTotalOS($os_id);

            $valorTotalServico = $valorTotalData['totalServico'];
            $valorTotalProduto = $valorTotalData['totalProdutos'];
            $valorDesconto = $valorTotalData['valor_desconto'];

            $valorTotal = $valorTotalServico + $valorTotalProduto;
            $valorTotalComDesconto = $valorTotal - $valorDesconto;

            $data = [
                'descricao' => set_value('descricao'),
                'valor' => $valorTotal,
                'tipo_desconto' => 'real',
                'desconto' => ($valorDesconto > 0) ? $valorTotalComDesconto : 0,
                'valor_desconto' => ($valorDesconto > 0) ? $valorDesconto : $valorTotal,
                'clientes_id' => $this->input->post('clientes_id'),
                'data_vencimento' => $vencimento,
                'data_pagamento' => $recebimento,
                'baixado' => $this->input->post('recebido') ?: 0,
                'cliente_fornecedor' => set_value('cliente'),
                'forma_pgto' => $this->input->post('formaPgto'),
                'tipo' => $this->input->post('tipo'),
                'observacoes' => set_value('observacoes'),
                'usuarios_id' => $this->session->userdata('id_admin'),
            ];

            $editavel = $this->os_model->isEditable($os_id);
            if (!$editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false]));
            }

            $dadosOs = [
                'faturado' => 1,
                'valorTotal' => $valorTotal,
                'status' => 'Faturado'
            ];
            if ($valorDesconto > 0) {
                $dadosOs['desconto'] = $valorTotalComDesconto;
                $dadosOs['valor_desconto'] = $valorDesconto;
            } else {
                $dadosOs['desconto'] = 0;
                $dadosOs['valor_desconto'] = $valorTotal;
            }

            if ($this->os_model->faturarOs($os_id, $data, $dadosOs)) {
                log_info('Faturou uma OS. ID: ' . $os_id);
                $this->session->set_flashdata('success', 'OS faturada com sucesso!');
                $json = ['result' => true];
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
                $json = ['result' => false];
            }

            echo json_encode($json);
            exit();
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar OS.');
        $json = ['result' => false];
        echo json_encode($json);
    }

    private function enviarOsPorEmail($idOs, $remetentes, $assunto)
    {
        $dados = [];

        $this->load->model('steos_model');
        $dados['result'] = $this->os_model->getById($idOs);
        if (! isset($dados['result']->email)) {
            return false;
        }

        $dados['produtos'] = $this->os_model->getProdutos($idOs);
        $dados['servicos'] = $this->os_model->getServicos($idOs);
        $dados['emitente'] = $this->steos_model->getEmitente();
        $emitente = $dados['emitente'];
        if (! isset($emitente->email)) {
            return false;
        }

        $html = $this->load->view('os/emails/os', $dados, true);

        $this->load->model('email_model');

        $remetentes = array_unique($remetentes);
        foreach ($remetentes as $remetente) {
            if ($remetente) {
                $headers = ['From' => $emitente->email, 'Subject' => $assunto, 'Return-Path' => ''];
                $email = [
                    'to' => $remetente,
                    'message' => $html,
                    'status' => 'pending',
                    'date' => date('Y-m-d H:i:s'),
                    'headers' => json_encode($headers),
                ];
                $this->email_model->add('email_queue', $email);
            } else {
                log_info('Email não adicionado a Lista de envio de e-mails. Verifique se o remetente esta cadastrado. OS ID: ' . $idOs);
            }
        }

        return true;
    }

    public function adicionarAnotacao()
    {
        $this->load->library('form_validation');
        if ($this->form_validation->run('anotacoes_os') == false) {
            echo json_encode(validation_errors());
        } else {
            $data = [
                'anotacao' => '[' . $this->session->userdata('nome_admin') . '] ' . $this->input->post('anotacao'),
                'data_hora' => date('Y-m-d H:i:s'),
                'os_id' => $this->input->post('os_id'),
            ];

            if ($this->os_model->add('anotacoes_os', $data) == true) {
                log_info('Adicionou anotação a uma OS. ID (OS): ' . $this->input->post('os_id'));
                echo json_encode(['result' => true]);
            } else {
                echo json_encode(['result' => false]);
            }
        }
    }

    public function excluirAnotacao()
    {
        $id = $this->input->post('idAnotacao');
        $idOs = $this->input->post('idOs');

        if ($this->os_model->delete('anotacoes_os', 'idAnotacoes', $id) == true) {
            log_info('Removeu anotação de uma OS. ID (OS): ' . $idOs);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }

    public function adicionarChecklist($idOs = null)
    {
        if (!$idOs) {
            redirect(site_url('os'));
        }

        // Verifica se já existe checklist
        $this->db->where('os_id', $idOs);
        $checklistExistente = $this->db->get('os_checklists')->row();
        if ($checklistExistente) {
            redirect(site_url('os/editarChecklist/' . $idOs));
        }

        $this->data['result'] = $this->os_model->getById($idOs);
        if (!$this->data['result'] || !$this->data['result']->contratos_id) {
            $this->session->set_flashdata('error', 'OS não encontrada ou não possui contrato vinculado.');
            redirect(site_url('os'));
        }

        $maxObj = $this->db->query("SELECT IFNULL(MAX(idChecklist),0)+1 as proximo FROM os_checklists")->row();
        $this->data['proximoId'] = $maxObj ? $maxObj->proximo : 1;

        $this->load->model('tecnicos_os_model');
        $tecnicosOS = $this->tecnicos_os_model->getById($idOs);
        $nomeTecnicoResp = '';
        if (!empty($tecnicosOS)) {
            $nomeTecnicoResp = $tecnicosOS[0]->tecnicoName ?? ($tecnicosOS[0]->nome ?? '');
        }
        $this->data['tecnicoResp'] = !empty($nomeTecnicoResp) ? $nomeTecnicoResp : ($this->data['result']->nome ?? '');

        $idContrato = $this->data['result']->contratos_id;
        $this->load->model('contratos_model');
        $this->data['contrato'] = $this->contratos_model->getById($idContrato);

        $sistemas_contrato = $this->contratos_model->getSistemasByContrato($idContrato);
        $matriz = [];
        
        if($sistemas_contrato){
            foreach($sistemas_contrato as $sc){
                $nomeSistema = trim(strtoupper($sc->nome));
                if(!isset($matriz[$nomeSistema])){
                    $matriz[$nomeSistema] = [
                        'locais' => [],
                        'checks' => []
                    ];
                    $checksBase = $this->db->where('sistemas_id', $sc->sistemas_id)->get('sistemas_checks')->result();
                    foreach($checksBase as $cb){
                        $matriz[$nomeSistema]['checks'][] = $cb->descricao;
                    }
                }
                $matriz[$nomeSistema]['locais'][] = $sc->local ? strtoupper($sc->local) : 'LOCAL NÃO DEFINIDO';
            }
        }
        
        $this->data['matriz'] = $matriz;
        $this->load->view('os/adicionarChecklist', $this->data);
    }

    public function salvarChecklist()
    {
        $idOs = $this->input->post('idOs');
        $idContrato = $this->input->post('idContrato');
        
        $dataChecklist = [
            'os_id' => $idOs,
            'contratos_id' => $idContrato,
            'data_criacao' => date('Y-m-d H:i:s'),
            'usuarios_id' => $this->session->userdata('id_admin') ?: ($this->session->userdata('id') ?: 1),
            'status' => 'Fechado',
            'observacoes' => $this->input->post('observacoes_gerais'),
            'assinatura_cliente' => $this->input->post('assinatura_cliente'),
            'assinatura_tecnico' => $this->input->post('assinatura_tecnico'),
            'nome_tecnico' => $this->input->post('nome_tecnico'),
            'data_checklist' => date('Y-m-d'),
            'obs_gerais' => $this->input->post('observacoes_gerais')
        ];

        $this->db->insert('os_checklists', $dataChecklist);
        $checklist_id = $this->db->insert_id();

        $rawChecks = $this->input->post('checks_data', false);
        if (!$rawChecks && isset($_POST['checks_data'])) {
            $rawChecks = $_POST['checks_data'];
        }
        $checksData = json_decode($rawChecks, true);
        $countInseridos = 0;

        $debugLog = "Data: " . date('Y-m-d H:i:s') . "\n";
        $debugLog .= "Raw Checks: " . var_export($rawChecks, true) . "\n";
        $debugLog .= "Decoded: " . var_export($checksData, true) . "\n";

        if ($checksData && is_array($checksData)) {
            foreach ($checksData as $item) {
                $inserted = $this->db->insert('os_checklists_itens', [
                    'checklist_id' => $checklist_id,
                    'sistema' => isset($item['sistema']) ? $item['sistema'] : '',
                    'local' => isset($item['local']) ? $item['local'] : '',
                    'check_desc' => isset($item['check_desc']) ? $item['check_desc'] : '',
                    'status' => isset($item['status']) ? $item['status'] : 'O',
                    'obs_local' => isset($item['obs_local']) ? $item['obs_local'] : '',
                    'os_local' => isset($item['os_local']) ? $item['os_local'] : '',
                    'descricao' => isset($item['check_desc']) ? $item['check_desc'] : ''
                ]);
                if (!$inserted) {
                    $debugLog .= "DB Error on insert: " . print_r($this->db->error(), true) . "\n";
                } else {
                    $countInseridos++;
                }
            }
        } else {
            $debugLog .= "checksData not array or empty!\n";
        }
        $debugLog .= "Count Inseridos: $countInseridos\n";
        file_put_contents(FCPATH . 'scratch/checklist_debug.log', $debugLog);

        $this->session->set_flashdata('success', 'Checklist salvo com sucesso!');
        echo json_encode(['result' => true, 'idChecklist' => $checklist_id, 'itens' => $countInseridos]);
    }

    public function visualizarChecklist($idOs = null)
    {
        if (!$idOs) {
            redirect(site_url('os'));
        }

        $this->data['result'] = $this->os_model->getById($idOs);

        $this->load->model('tecnicos_os_model');
        $tecnicosOS = $this->tecnicos_os_model->getById($idOs);
        $nomeTecnicoResp = '';
        if (!empty($tecnicosOS)) {
            $nomeTecnicoResp = $tecnicosOS[0]->tecnicoName ?? ($tecnicosOS[0]->nome ?? '');
        }
        $this->data['tecnicoResp'] = !empty($nomeTecnicoResp) ? $nomeTecnicoResp : ($this->data['result']->nome ?? '');

        $this->db->where('os_id', $idOs);
        $checklist = $this->db->get('os_checklists')->row();
        
        if (!$checklist) {
            $this->session->set_flashdata('error', 'Checklist não encontrado para esta OS.');
            redirect(site_url('os/editar/' . $idOs));
        }
        
        $this->data['checklist'] = $checklist;
        
        $this->db->where('checklist_id', $checklist->idChecklist);
        $itens = $this->db->get('os_checklists_itens')->result();
        
        $matriz = [];
        foreach($itens as $item) {
            $sis = $item->sistema;
            $loc = $item->local;
            
            if(!isset($matriz[$sis])) {
                $matriz[$sis] = ['locais' => [], 'checks' => []];
            }
            if(!in_array($item->check_desc, $matriz[$sis]['checks'])) {
                $matriz[$sis]['checks'][] = $item->check_desc;
            }
            if(!isset($matriz[$sis]['locais'][$loc])) {
                $matriz[$sis]['locais'][$loc] = [
                    'obs_local' => $item->obs_local,
                    'os_local' => $item->os_local,
                    'checks' => []
                ];
            }
            $matriz[$sis]['locais'][$loc]['checks'][$item->check_desc] = $item->status;
        }
        
        $this->data['matriz'] = $matriz;
        $this->load->view('os/visualizarChecklist', $this->data);
    }
    
    public function editarChecklist($idOs = null)
    {
        // Por simplicidade, caso queira editar o mesmo checklist (sobreescrevendo)
        // Redirecionando para visualizar por enquanto, ou podemos implementar edição real
        $this->visualizarChecklist($idOs);
    }

    public function imprimirChecklist($idOs = null)
    {
        $this->data['autoPrint'] = true;
        $this->visualizarChecklist($idOs);
    }
}
