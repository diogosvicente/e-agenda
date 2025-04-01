<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\PredioModel;
use App\Models\EspacosModel;
use App\Models\RecursosModel;
use App\Models\EventoModel;
use App\Models\EventoEspacoDataHoraModel;
use CodeIgniter\Controller;

class SchedulingController extends BaseController
{
    protected $campusModel;
    protected $predioModel;
    protected $espacoModel;
    protected $recursoModel;
    protected $eventoModel;
    protected $eventoEspacoModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        $this->campusModel  = new CampusModel();
        $this->predioModel  = new PredioModel();
        $this->espacoModel  = new EspacosModel();
        $this->recursoModel = new RecursosModel();
        $this->eventoModel  = new EventoModel();
        $this->eventoEspacoModel = new EventoEspacoDataHoraModel();

        $this->idSistema  = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');

        // Obtém os dados do usuário via helper (definido, por exemplo, em auth_helper.php)
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;
    }

    public function add()
    {
        // Obtém todos os campi com seus prédios e espaços
        $campus = $this->campusModel->getCampiWithPrediosAndEspacos();

        // Obtém todos os recursos organizados por espaço
        $recursos       = [];
        $recursosGerais = [];
        $recursosList   = $this->recursoModel->findAll();

        foreach ($recursosList as $recurso) {
            if (!empty($recurso->id_espaco)) {
                $recursos[$recurso->id_espaco][] = $recurso;
            } else {
                $recursosGerais[] = $recurso;
            }
        }

        // Carrega os helpers que obtêm as unidades e os usuários do SSO
        helper('sso_helper'); // Certifique-se de que o helper que contém as funções getUnits() e getUsers() está carregado

        $units = getUnits();
        $users = getUsers();

        // Passa todas as variáveis necessárias para a view
        return view('scheduling/add', [
            'idSistema'      => $this->idSistema,
            'ssoBaseUrl'     => $this->ssoBaseUrl,
            'userInfo'       => $this->userInfo,
            'campus'         => $campus,
            'recursos'       => $recursos,
            'recursosGerais' => $recursosGerais,
            'registro'       => null,
            'units'          => $units,
            'users'          => $users,
        ]);
    }

    public function save()
    {
        $post = $this->request->getPost();

        $espacos     = $post['espaco'] ?? [];
        $datas       = $post['data_inicio'] ?? [];
        $horasInicio = $post['hora_inicio'] ?? [];
        $horasFim    = $post['hora_fim'] ?? [];

        $eventoData = [
            'id_solicitante'            => $post['id_solicitante'],
            'id_unidade_solicitante'    => $post['id_unidade_solicitante'],
            'nome'                      => $post['titulo_evento'],
            'quantidade_participantes'  => $post['quantidade_participantes'] ?? 0,
            'observacoes'               => $post['observacoes'] ?? '',
            'assinado_solicitante'      => 0,
            'assinado_componente_org'   => 0,
            'email_responsavel'         => $post['responsavel_email'] ?? '',
            'telefone1_responsavel'     => $post['responsavel_telefone1'] ?? '',
            'telefone2_responsavel'     => $post['responsavel_telefone2'] ?? '',
            'email_aprovador'           => $post['aprovador_email'] ?? '',
            'telefone1_aprovador'       => $post['aprovador_telefone1'] ?? '',
            'telefone2_aprovador'       => $post['aprovador_telefone2'] ?? '',
            'id_responsavel'            => $post['responsavel_nome_id'] ?? 0,
            'id_unidade_responsavel'    => $post['responsavel_unidade_id'] ?? 0,
            'nome_responsavel'          => $post['responsavel_nome_externo'] ?? '',
            'nome_unidade_responsavel'  => $post['responsavel_unidade_externo'] ?? '',
            'id_aprovador'              => $post['aprovador_nome_id'] ?? '',
            'id_unidade_aprovador'      => $post['aprovador_unidade_id'] ?? ''
        ];

        // echo "<pre>"; dd(print_r($eventoData));


        $eventoId = $this->eventoModel->insert($eventoData);
        if (!$eventoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar evento.'
            ]);
        }

        // Espaços + horários
        foreach ($espacos as $index => $idEspaco) {
            if (!isset($datas[$index], $horasInicio[$index], $horasFim[$index])) continue;
        
            $data_hora_inicio = date("Y-m-d H:i:s", strtotime($datas[$index] . ' ' . $horasInicio[$index]));
            $data_hora_fim    = date("Y-m-d H:i:s", strtotime($datas[$index] . ' ' . $horasFim[$index]));
        
            log_message('debug', "Insert espaco {$idEspaco} - $data_hora_inicio até $data_hora_fim");
        
            $result = $this->eventoEspacoModel->insert([
                'id_evento'        => $eventoId,
                'id_espaco'        => $idEspaco,
                'data_hora_inicio' => $data_hora_inicio,
                'data_hora_fim'    => $data_hora_fim
            ]);
        
            if (!$result) {
                log_message('error', 'Erro ao inserir em evento_espaco_data_hora: ' . json_encode($this->eventoEspacoModel->errors()));
            }
        }
        

        // Recursos
        $recursos = $post['recursos'] ?? [];
        $quantidades = $post['quantidade_recurso'] ?? [];

        foreach ($recursos as $idRecurso) {
            $this->eventoRecursosModel->insert([
                'id_evento'  => $eventoId,
                'id_recurso' => $idRecurso,
                'quantidade' => $quantidades[$idRecurso] ?? 1
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'id_evento' => $eventoId,
            'message' => 'Evento cadastrado com sucesso!'
        ]);
    }

}
