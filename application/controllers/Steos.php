<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Steos extends MY_Controller
{
    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('steos_model');
    }

    public function index()
    {
        $this->load->model('Steos_model');
        $this->load->model('tecnicos_model');


        $this->data['ordens'] = $this->steos_model->getOsAbertas();
        $this->data['ordens1'] = $this->steos_model->getOsAguardandoPecas();
        $this->data['ordens_andamento'] = $this->steos_model->getOsAndamento();
        $this->data['produtos'] = $this->steos_model->getProdutosMinimo();
        $this->data['os'] = $this->steos_model->getOsEstatisticas();
        $this->data['ordens_orcamentos'] = null;
        $this->data['ordens_abertas'] = null;
        $this->data['ordens_aprovadas'] = null;
        $this->data['ordens_finalizadas'] = null;
        $this->data['ordens_status'] = null;
        $this->data['vendasstatus'] = null;
        $this->data['lancamentos'] = null;
        $this->data['estatisticas_financeiro'] = $this->steos_model->getEstatisticasFinanceiro();
        $this->data['financeiro_mes_dia'] = $this->steos_model->getEstatisticasFinanceiroDia($this->input->get('year'));
        $this->data['financeiro_mes'] = $this->steos_model->getEstatisticasFinanceiroMes($this->input->get('year'));
        $this->data['financeiro_mesinadipl'] = $this->steos_model->getEstatisticasFinanceiroMesInadimplencia($this->input->get('year'));
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['menuPainel'] = 'Painel';
        $this->data['tab'] = "1";
        $this->data['view'] = 'steos/painel';
        return $this->layout();
    }

    public function minhaConta()
    {
        $this->load->library('pagination');
        $this->load->model('steos_model');
        $this->load->model('os_model');
        $this->load->model('tecnicos_model');
        $this->load->model('tecnicos_os_model');

        $where_array = [];
        $de = $this->input->get('finalizado_de') ?: date('d/m/Y');
        $ate = $this->input->get('finalizado_ate') ?: date('d/m/Y');
        $tipo = $this->input->get('tipo');
        $tecnico = $this->input->get('tecnico');
        $status = "Finalizado";

        if ($tecnico) {
            $where_array['tecnico'] = $tecnico;
        }
        if ($status) {
            $where_array['status'] = $status;
        }

        /* if ($tipo) {
            $where_array['tipo'] = $tipo;
        } */

        if ($de) {
            $de = explode('/', $de);
            $de = $de[2] . '-' . $de[1] . '-' . $de[0];

            $where_array['de'] = $de;
        }
        if ($ate) {
            $ate = explode('/', $ate);
            $ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

            $where_array['ate'] = $ate;
        }


        $this->data['configuration']['total_rows'] = $this->os_model->count('os');
        $this->pagination->initialize($this->data['configuration']);
        $data = $this->os_model->getOs(
            'os',
            'os.*,                   
            COALESCE((SELECT SUM(produtos_os.preco * produtos_os.quantidade ) FROM produtos_os WHERE produtos_os.os_id = os.idOs), 0) totalProdutos,
            COALESCE((SELECT SUM(servicos_os.preco * servicos_os.quantidade ) FROM servicos_os WHERE servicos_os.os_id = os.idOs), 0) totalServicos',
            $where_array,
            $this->data['configuration']['per_page'],
            $this->uri->segment(3),
            null,
            $this->session->userdata('tecnico')
        );

        if (!empty($data)) {

            $c = "";
            $data3 = [];
            foreach ($data as $d) {
                $c++;
                $data2 = $this->tecnicos_os_model->getById($d->idOs);
                $cont = "";
                foreach ($data2 as $d1) {
                    if ($d1->os_id == $d->idOs) {
                        $cont++;
                    }
                }

                $data3[$c]['idOs'] = $d->idOs;
                $data3[$c]['divizorComissaoServico'] = $cont;
                $data3[$c]['dataInicial'] = $d->dataInicial;
                $data3[$c]['dataFinal'] = $d->dataFinal;
                $data3[$c]['dataAbertura'] = $d->dataAbertura;
                $data3[$c]['garantia'] = $d->garantia;
                $data3[$c]['status'] = $d->status;
                $data3[$c]['observacoes'] = $d->observacoes;
                $data3[$c]['laudoTecnico'] = $d->laudoTecnico;
                $data3[$c]['valorTotal'] = $d->valorTotal;
                $data3[$c]['desconto'] = $d->desconto;
                $data3[$c]['valor_desconto'] = $d->valor_desconto;
                $data3[$c]['tipo_desconto'] = $d->tipo_desconto;
                $data3[$c]['clientes_id'] = $d->clientes_id;
                $data3[$c]['tecnicos_id'] = $d->tecnicos_id;
                $data3[$c]['usuarios_id'] = $d->usuarios_id;
                $data3[$c]['lancamento'] = $d->lancamento;
                $data3[$c]['afaturar'] = $d->afaturar;
                $data3[$c]['faturado'] = $d->faturado;
                $data3[$c]['garantias_id'] = $d->garantias_id;
                $data3[$c]['signature'] = $d->signature;
                $data3[$c]['equipamentos_id'] = $d->equipamentos_id;
                $data3[$c]['descricaoProduto'] = $d->descricaoProduto;
                $data3[$c]['defeito'] = $d->defeito;
                $data3[$c]['tipo'] = $d->tipo;
                $data3[$c]['local'] = $d->local;
                $data3[$c]['defeito_encontrado'] = $d->defeito_encontrado;
                $data3[$c]['totalProdutos'] = $d->totalProdutos;
                $data3[$c]['totalServicos'] = $d->totalServicos;
                $data3[$c]['idClientes'] = $d->idClientes;
                $data3[$c]['nomeCliente'] = $d->nomeCliente;
                $data3[$c]['celular_cliente'] = $d->celular_cliente;
                $data3[$c]['nome'] = $d->nome;
                $data3[$c]['idGarantias'] = $d->idGarantias;
                $data3[$c]['dataGarantia'] = $d->dataGarantia;
                $data3[$c]['refGarantia'] = $d->refGarantia;
                $data3[$c]['textoGarantia'] = $d->textoGarantia;
                $data3[$c]['tecnicoName'] = $d->tecnicoName;
            }

            $this->data['results'] = $data3;
        }


        $this->load->model('tecnicos_model');
        $this->load->model('contas_model');
        $this->data['tecnicos'] = $this->tecnicos_model->getAll();
        $this->data['usuarios'] = $this->contas_model->getAll('usuarios');
        $this->data['usuario'] = $this->steos_model->getById($this->session->userdata('id_admin'));
        $this->data['tab'] = "1";
        $this->data['view'] = 'steos/minhaConta';
        return $this->layout();
    }


    public function alterarSenha()
    {
        $current_user = $this->steos_model->getById($this->session->userdata('id_admin'));

        if (!$current_user) {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao pesquisar usuário!');
            redirect(site_url('steos/minhaConta'));
        }

        $oldSenha = $this->input->post('oldSenha');
        $senha = $this->input->post('novaSenha');

        if (!password_verify($oldSenha, $current_user->senha)) {
            $this->session->set_flashdata('error', 'A senha atual não corresponde com a senha informada.');
            redirect(site_url('steos/minhaConta'));
        }

        $result = $this->steos_model->alterarSenha($senha);

        if ($result) {
            $this->session->set_flashdata('success', 'Senha alterada com sucesso!');
            redirect(site_url('steos/minhaConta'));
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar a senha!');
        redirect(site_url('steos/minhaConta'));
    }

    public function pesquisar()
    {
        $termo = $this->input->get('termo');

        $data['results'] = $this->steos_model->pesquisar($termo);
        $this->data['produtos'] = $data['results']['produtos'];
        $this->data['servicos'] = $data['results']['servicos'];
        $this->data['os'] = $data['results']['os'];
        $this->data['clientes'] = $data['results']['clientes'];
        $this->data['view'] = 'steos/pesquisa';
        return $this->layout();
    }

    public function backup()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para efetuar backup.');
            redirect(base_url());
        }

        $this->load->dbutil();
        $prefs = [
            'format' => 'zip',
            'foreign_key_checks' => false,
            'filename' => 'backup' . date('d-m-Y') . '.sql',
        ];

        $backup = $this->dbutil->backup($prefs);

        $this->load->helper('file');
        write_file(base_url() . 'backup/backup.zip', $backup);

        log_info('Efetuou backup do banco de dados.');

        $this->load->helper('download');
        force_download('backup' . date('d-m-Y H:m:s') . '.zip', $backup);
    }

    public function emitente()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $this->data['menuConfiguracoes'] = 'Configuracoes';
        $this->data['dados'] = $this->steos_model->getEmitente();
        $this->data['view'] = 'steos/emitente';
        return $this->layout();
    }

    public function do_upload()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $this->load->library('upload');

        $image_upload_folder = FCPATH . 'assets/uploads';

        if (!file_exists($image_upload_folder)) {
            mkdir($image_upload_folder, DIR_WRITE_MODE, true);
        }

        $this->upload_config = [
            'upload_path' => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp|svg',
            'max_size' => 2048,
            'remove_space' => true,
            'encrypt_name' => true,
        ];

        $this->upload->initialize($this->upload_config);

        if (!$this->upload->do_upload()) {
            $upload_error = $this->upload->display_errors();
            print_r($upload_error);
            exit();
        } else {
            $file_info = [$this->upload->data()];
            return $file_info[0]['file_name'];
        }
    }

    public function do_upload_user()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $this->load->library('upload');

        $image_upload_folder = FCPATH . 'assets/userImage/';

        if (!file_exists($image_upload_folder)) {
            mkdir($image_upload_folder, DIR_WRITE_MODE, true);
        }

        $this->upload_config = [
            'upload_path' => $image_upload_folder,
            'allowed_types' => 'png|jpg|jpeg|bmp',
            'max_size' => 2048,
            'remove_space' => true,
            'encrypt_name' => true,
        ];

        $this->upload->initialize($this->upload_config);

        if (!$this->upload->do_upload()) {
            $upload_error = $this->upload->display_errors();
            print_r($upload_error);
            exit();
        } else {
            $file_info = [$this->upload->data()];
            return $file_info[0]['file_name'];
        }
    }

    public function cadastrarEmitente()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nome', 'Razão Social', 'required|trim');
        $this->form_validation->set_rules('cnpj', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('ie', 'IE', 'required|trim');
        $this->form_validation->set_rules('cep', 'CEP', 'required|trim');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required|trim');
        $this->form_validation->set_rules('numero', 'Número', 'required|trim');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required|trim');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required|trim');
        $this->form_validation->set_rules('uf', 'UF', 'required|trim');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(site_url('steos/emitente'));
        } else {
            $nome = $this->input->post('nome');
            $cnpj = $this->input->post('cnpj');
            $ie = $this->input->post('ie');
            $cep = $this->input->post('cep');
            $logradouro = $this->input->post('logradouro');
            $numero = $this->input->post('numero');
            $bairro = $this->input->post('bairro');
            $cidade = $this->input->post('cidade');
            $uf = $this->input->post('uf');
            $telefone = $this->input->post('telefone');
            $email = $this->input->post('email');
            $image = $this->do_upload();
            $logo = base_url() . 'assets/uploads/' . $image;

            $retorno = $this->steos_model->addEmitente($nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email, $logo);
            if ($retorno) {
                $this->session->set_flashdata('success', 'As informações foram inseridas com sucesso.');
                log_info('Adicionou informações de emitente.');
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar inserir as informações.');
            }
            redirect(site_url('steos/emitente'));
        }
    }

    public function editarEmitente()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nome', 'Razão Social', 'required|trim');
        $this->form_validation->set_rules('cnpj', 'CNPJ', 'required|trim');
        $this->form_validation->set_rules('ie', 'IE', 'required|trim');
        $this->form_validation->set_rules('cep', 'CEP', 'required|trim');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required|trim');
        $this->form_validation->set_rules('numero', 'Número', 'required|trim');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required|trim');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required|trim');
        $this->form_validation->set_rules('uf', 'UF', 'required|trim');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|trim');
        $this->form_validation->set_rules('email', 'E-mail', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', 'Campos obrigatórios não foram preenchidos.');
            redirect(site_url('steos/emitente'));
        } else {
            $nome = $this->input->post('nome');
            $cnpj = $this->input->post('cnpj');
            $ie = $this->input->post('ie');
            $cep = $this->input->post('cep');
            $logradouro = $this->input->post('logradouro');
            $numero = $this->input->post('numero');
            $bairro = $this->input->post('bairro');
            $cidade = $this->input->post('cidade');
            $uf = $this->input->post('uf');
            $telefone = $this->input->post('telefone');
            $email = $this->input->post('email');
            $id = $this->input->post('id');

            $retorno = $this->steos_model->editEmitente($id, $nome, $cnpj, $ie, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $telefone, $email);
            if ($retorno) {
                $this->session->set_flashdata('success', 'As informações foram alteradas com sucesso.');
                log_info('Alterou informações de emitente.');
            } else {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar as informações.');
            }
            redirect(site_url('steos/emitente'));
        }
    }

    public function editarLogo()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar emitente.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar a logomarca.');
            redirect(site_url('steos/emitente'));
        }
        $this->load->helper('file');
        delete_files(FCPATH . 'assets/uploads/');

        $image = $this->do_upload();
        $logo = base_url() . 'assets/uploads/' . $image;

        $retorno = $this->steos_model->editLogo($id, $logo);
        if ($retorno) {
            $this->session->set_flashdata('success', 'As informações foram alteradas com sucesso.');
            log_info('Alterou a logomarca do emitente.');
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar as informações.');
        }
        redirect(site_url('steos/emitente'));
    }

    public function uploadUserImage()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para mudar a foto.');
            redirect(base_url());
        }

        $id = $this->session->userdata('id_admin');
        if ($id == null || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar sua foto.');
            redirect(site_url('steos/minhaConta'));
        }

        $usuario = $this->steos_model->getById($id);

        if (is_file(FCPATH . 'assets/userImage/' . $usuario->url_image_user)) {
            unlink(FCPATH . 'assets/userImage/' . $usuario->url_image_user);
        }

        $image = $this->do_upload_user();
        $imageUserPath = $image;
        $retorno = $this->steos_model->editImageUser($id, $imageUserPath);

        if ($retorno) {
            $this->session->set_userdata('url_image_user', $imageUserPath);
            $this->session->set_flashdata('success', 'Foto alterada com sucesso.');
            log_info('Alterou a Imagem do Usuario.');
        } else {
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar alterar sua foto.');
        }
        redirect(site_url('steos/minhaConta'));
    }

    public function emails()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmail')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar fila de e-mails');
            redirect(base_url());
        }

        $this->data['menuConfiguracoes'] = 'Email';

        $this->load->library('pagination');
        $this->load->model('email_model');

        $this->data['configuration']['base_url'] = site_url('steos/emails/');
        $this->data['configuration']['total_rows'] = $this->email_model->count('email_queue');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->email_model->get('email_queue', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'emails/emails';
        return $this->layout();
    }

    public function excluirEmail()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cEmail')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir e-mail da fila.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir e-mail da fila.');
            redirect(site_url('steos/emails/'));
        }

        $this->load->model('email_model');
        $this->email_model->delete('email_queue', 'id', $id);

        log_info('Removeu um e-mail da fila de envio. ID: ' . $id);

        $this->session->set_flashdata('success', 'E-mail removido da fila de envio!');
        redirect(site_url('steos/emails/'));
    }

    public function configurar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar o sistema');
            redirect(base_url());
        }
        $this->data['menuConfiguracoes'] = 'Sistema';

        $this->load->library('form_validation');
        $this->load->model('steos_model');

        $this->data['custom_error'] = '';

        $this->form_validation->set_rules('app_name', 'Nome do Sistema', 'required|trim');
        $this->form_validation->set_rules('per_page', 'Registros por página', 'required|numeric|trim');
        $this->form_validation->set_rules('app_theme', 'Tema do Sistema', 'required|trim');
        $this->form_validation->set_rules('os_notification', 'Notificação de OS', 'required|trim');
        $this->form_validation->set_rules('email_automatico', 'Enviar Email Automático', 'required|trim');
        $this->form_validation->set_rules('control_estoque', 'Controle de Estoque', 'required|trim');
        $this->form_validation->set_rules('notifica_whats', 'Notificação Whatsapp', 'required|trim');
        $this->form_validation->set_rules('control_baixa', 'Controle de Baixa', 'required|trim');
        $this->form_validation->set_rules('control_editos', 'Controle de Edição de OS', 'required|trim');
        $this->form_validation->set_rules('control_edit_vendas', 'Controle de Edição de Vendas', 'required|trim');
        $this->form_validation->set_rules('control_datatable', 'Controle de Visualização em DataTables', 'required|trim');
        $this->form_validation->set_rules('os_status_list[]', 'Controle de visualização de OS', 'required|trim', ['required' => 'Selecione ao menos uma das opções!']);
        $this->form_validation->set_rules('control_2vias', 'Controle Impressão 2 Vias', 'required|trim');
        $this->form_validation->set_rules('pix_key', 'Chave Pix', 'trim|valid_pix_key', [
            'valid_pix_key' => 'Chave Pix inválida!',
        ]);

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert">' . validation_errors() . '</div>' : false);
        } else {
            // Edição do .env
            $dataDotEnv = [
                'IMPRIMIR_ANEXOS' => $this->input->post('imprmirAnexos'),
                'PAYMENT_GATEWAYS_EFI_PRODUCTION' => $this->input->post('PAYMENT_GATEWAYS_EFI_PRODUCTION'),
                'PAYMENT_GATEWAYS_EFI_CREDENTIAIS_CLIENT_ID' => $this->input->post('PAYMENT_GATEWAYS_EFI_CREDENTIAIS_CLIENT_ID'),
                'PAYMENT_GATEWAYS_EFI_CREDENTIAIS_CLIENT_SECRET' => $this->input->post('PAYMENT_GATEWAYS_EFI_CREDENTIAIS_CLIENT_SECRET'),
                'PAYMENT_GATEWAYS_EFI_BOLETO_EXPIRATION' => $this->input->post('PAYMENT_GATEWAYS_EFI_BOLETO_EXPIRATION'),
                'PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_PUBLIC_KEY' => $this->input->post('PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_PUBLIC_KEY'),
                'PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_ACCESS_TOKEN' => $this->input->post('PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_ACCESS_TOKEN'),
                'PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_CLIENT_ID' => $this->input->post('PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_CLIENT_ID'),
                'PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_CLIENT_SECRET' => $this->input->post('PAYMENT_GATEWAYS_MERCADO_PAGO_CREDENTIALS_CLIENT_SECRET'),
                'PAYMENT_GATEWAYS_MERCADO_PAGO_BOLETO_EXPIRATION' => $this->input->post('PAYMENT_GATEWAYS_MERCADO_PAGO_BOLETO_EXPIRATION'),
                'PAYMENT_GATEWAYS_ASAAS_PRODUCTION' => $this->input->post('PAYMENT_GATEWAYS_ASAAS_PRODUCTION'),
                'PAYMENT_GATEWAYS_ASAAS_NOTIFY' => $this->input->post('PAYMENT_GATEWAYS_ASAAS_NOTIFY'),
                'PAYMENT_GATEWAYS_ASAAS_CREDENTIAIS_API_KEY' => $this->input->post('PAYMENT_GATEWAYS_ASAAS_CREDENTIAIS_API_KEY'),
                'PAYMENT_GATEWAYS_ASAAS_BOLETO_EXPIRATION' => $this->input->post('PAYMENT_GATEWAYS_ASAAS_BOLETO_EXPIRATION'),
                'API_ENABLED' => $this->input->post('apiEnabled'),
                'API_TOKEN_EXPIRE_TIME' => $this->input->post('apiExpireTime'),
                'API_JWT_KEY' => $this->input->post('resetJwtToken'),
                'EMAIL_PROTOCOL' => $this->input->post('EMAIL_PROTOCOL'),
                'EMAIL_SMTP_HOST' => $this->input->post('EMAIL_SMTP_HOST'),
                'EMAIL_SMTP_CRYPTO' => $this->input->post('EMAIL_SMTP_CRYPTO'),
                'EMAIL_SMTP_PORT' => $this->input->post('EMAIL_SMTP_PORT'),
                'EMAIL_SMTP_USER' => $this->input->post('EMAIL_SMTP_USER'),
                'EMAIL_SMTP_PASS' => $this->input->post('EMAIL_SMTP_PASS'),
            ];

            if (!$this->editDontEnv($dataDotEnv)) {
                $this->data['custom_error'] = '<div class="alert">Falha ao editar o .env</div>';
            }
            // FIM Edição do .env

            $data = [
                'app_name' => $this->input->post('app_name'),
                'per_page' => $this->input->post('per_page'),
                'app_theme' => $this->input->post('app_theme'),
                'os_notification' => $this->input->post('os_notification'),
                'email_automatico' => $this->input->post('email_automatico'),
                'control_estoque' => $this->input->post('control_estoque'),
                'notifica_whats' => $this->input->post('notifica_whats'),
                'control_baixa' => $this->input->post('control_baixa'),
                'control_editos' => $this->input->post('control_editos'),
                'control_edit_vendas' => $this->input->post('control_edit_vendas'),
                'control_datatable' => $this->input->post('control_datatable'),
                'pix_key' => $this->input->post('pix_key'),
                'os_status_list' => json_encode($this->input->post('os_status_list')),
                'control_2vias' => $this->input->post('control_2vias'),
            ];
            if ($this->steos_model->saveConfiguracao($data) == true) {
                $this->session->set_flashdata('success', 'Configurações do sistema atualizadas com sucesso!');
                redirect(site_url('steos/configurar'));
            } else {
                $this->data['custom_error'] = '<div class="alert">Ocorreu um errro.</div>';
            }
        }

        $this->data['view'] = 'steos/configurar';

        return $this->layout();
    }

    public function atualizarBanco()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar o sistema');
            redirect(base_url());
        }

        $this->load->library('migration');

        if ($this->migration->latest() === false) {
            $this->session->set_flashdata('error', $this->migration->error_string());
        } else {
            $this->session->set_flashdata('success', 'Banco de dados atualizado com sucesso!');
        }

        return redirect(site_url('steos/configurar'));
    }

    public function atualizarSteos()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cSistema')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para configurar o sistema');
            redirect(base_url());
        }

        $this->load->library('github_updater');

        if (!$this->github_updater->has_update()) {
            $this->session->set_flashdata('success', 'Seu steos já está atualizado!');

            return redirect(site_url('steos/configurar'));
        }

        $success = $this->github_updater->update();

        if ($success) {
            $this->session->set_flashdata('success', 'Steos atualizado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar steos!');
        }

        return redirect(site_url('steos/configurar'));
    }

    public function calendario()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar O.S.');
            redirect(base_url());
        }
        $this->load->model('os_model');
        $status = $this->input->get('status') ?: null;
        $start = $this->input->get('start') ?: null;
        $end = $this->input->get('end') ?: null;
        $tecnicos = $this->input->get('tecnicos') ?: null;

        $allOs = $this->steos_model->calendario(
            $start,
            $end,
            $status,
            $tecnicos
        );

        $events = array_map(function ($os) {
            switch ($os->status) {
                case 'A Sair | Aguard Conclusão':
                    $cor = '#00cd00';
                    break;
                case 'Manutenção Preventiva':
                    $cor = '#AEB404';
                    break;
                case 'Em Andamento':
                    $cor = '#436eee';
                    break;
                case 'Orçamento':
                    $cor = '#CDB380';
                    break;
                case 'Negociação':
                    $cor = '#ffd700 ';
                    break;
                case 'Cancelado':
                    $cor = '#CD0000';
                    break;
                case 'Finalizado':
                    $cor = '#256';
                    break;
                case 'Faturado':
                    $cor = '#B266FF';
                    break;
                case 'Aguardando Peças':
                    $cor = '#FF7F00';
                    break;
                case 'Aprovado':
                    $cor = '#808080';
                    break;
                default:
                    $cor = '#E0E4CC';
                    break;
            }
            return [
                'title' => "OS: {$os->idOs}, Cliente: {$os->nomeCliente}",
                'start' => date('Y-m-d\TH:i:s', strtotime($os->dataInicial)),
                'end' => date('Y-m-d\TH:i:s', strtotime($os->dataFinal)),
                'allDay' => false,
                'color' => $cor,
                'extendedProps' => [
                    'id' => $os->idOs,
                    'cliente' => '<b>Cliente:</b> ' . $os->nomeCliente,
                    'endereco' => '<b>Edereço:</b> ' . $os->rua . ',' . $os->numero . '-' . $os->bairro . '<a target="_blank" title="abra o endereço no Navegador" class="button btn btn-mini btn-inverse" href=" https://www.google.com/maps/search/' . $os->rua . ',' . $os->numero . '-' . $os->bairro . '"><span class="button__icon"><i class="bx bx-map-alt"></i></span> <span class="button__text">Maps</span>',
                    'dataInicial' => '<b>Data Inicial:</b> ' . date('d/m/Y H:i:s', strtotime($os->dataInicial)),
                    'dataFinal' => '<b>Data Final:</b> ' . date('d/m/Y H:i:s', strtotime($os->dataFinal)),
                    'garantia' => '<b>Garantia:</b> ' . $os->garantia . ' dias',
                    'status' => '<b>Status da OS:</b> ' . $os->status,
                    'description' => '<b>Descrição/Produto:</b> ' . strip_tags(html_entity_decode($os->descricaoProduto)),
                    'defeito' => '<b>Defeito:</b> ' . strip_tags(html_entity_decode($os->defeito)),
                    'observacoes' => '<b>Observações:</b> ' . strip_tags(html_entity_decode($os->observacoes)),
                    'total' => '<b>Valor Total:</b> R$ ' . number_format($os->totalProdutos + $os->totalServicos, 2, ',', '.'),
                    'desconto' => '<b>Desconto: </b>R$ ' . number_format($this->desconto(floatval($os->valorTotal), floatval($os->desconto), strval($os->tipo_desconto)), 2, ',', '.'),
                    'valorFaturado' => '<b>Valor Faturado:</b> ' . ($os->faturado ? 'R$ ' . number_format($os->valorTotal - $this->desconto(floatval($os->valorTotal), floatval($os->desconto), strval($os->tipo_desconto)), 2, ',', '.') : "PENDENTE"),
                    'editar' => $this->os_model->isEditable($os->idOs),
                ]
            ];
        }, $allOs);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($events));
        //->set_output(json_encode($data));
    }

    public function atualizarDataOs()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            echo json_encode(['result' => false, 'message' => 'Você não tem permissão para editar O.S.']);
            return;
        }

        $idOs = $this->input->post('idOs');
        $novaData = $this->input->post('novaData');
        $dataInicialPost = $this->input->post('dataInicial');
        $dataFinalPost = $this->input->post('dataFinal');

        $this->load->model('os_model');
        if (!$this->os_model->isEditable($idOs)) {
            echo json_encode(['result' => false, 'message' => 'Esta O.S. não pode ser editada (Faturada ou Cancelada).']);
            return;
        }

        $dadosAtualizar = [];
        if ($dataInicialPost) {
            $dadosAtualizar['dataInicial'] = date('Y-m-d H:i:s', strtotime($dataInicialPost));
        }
        if ($dataFinalPost) {
            $dadosAtualizar['dataFinal'] = date('Y-m-d H:i:s', strtotime($dataFinalPost));
        } elseif ($novaData) {
            $dadosAtualizar['dataFinal'] = date('Y-m-d H:i:s', strtotime($novaData));
        }

        if (!empty($dadosAtualizar) && $this->os_model->edit('os', $dadosAtualizar, 'idOs', $idOs)) {
            log_info('Alterou a data da OS ID: ' . $idOs . ' via Calendário.');
            echo json_encode(['result' => true, 'message' => 'Horário da O.S. atualizado com sucesso!']);
        } else {
            echo json_encode(['result' => false, 'message' => 'Erro ao atualizar data no banco de dados.']);
        }
    }

    private function desconto(
        float $valorTotal,
        float $desconto,
        string $tipoDesconto
    ) {
        return $tipoDesconto === 'porcento'
            ? $valorTotal * ($desconto / 100)
            : $desconto;
    }

    private function editDontEnv(array $data)
    {
        $env_file_path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . '.env';
        $env_file = file_get_contents($env_file_path);

        foreach ($data as $constante => $valor) {
            if ($constante == 'API_JWT_KEY' && $valor == 'sim') {
                $base64 = base64_encode(openssl_random_pseudo_bytes(32));
                $valor = '"' . $base64 . '"';
                $env_file = str_replace("$constante=" . '"' . $_ENV[$constante] . '"', "$constante={$valor}", $env_file);
            } else {
                if (isset($_ENV[$constante])) {
                    $env_file = str_replace("$constante={$_ENV[$constante]}", "$constante={$valor}", $env_file);
                } else {
                    file_put_contents($env_file_path, $env_file . "\n{$constante}={$valor}\n");
                    $env_file = file_get_contents($env_file_path);
                }
            }
        }
        return file_put_contents($env_file_path, $env_file) ? true : false;
    }
}
