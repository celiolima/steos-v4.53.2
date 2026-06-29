    public function checklist($id)
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
