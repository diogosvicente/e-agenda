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
        // Recupera os dados do formulário via POST
        $post = $this->request->getPost();

        echo "<pre>"; dd(print_r($post));

        // Processa os arrays de espaços e horários
        $espacos     = $post['espaco']       ?? [];
        $datas       = $post['data_inicio']  ?? [];
        $horasInicio = $post['hora_inicio']  ?? [];
        $horasFim    = $post['hora_fim']     ?? [];

        // Verifica conflitos para cada espaço/hora
        foreach ($espacos as $index => $id_espaco) {
            $data       = $datas[$index]       ?? '';
            $horaInicio = $horasInicio[$index] ?? '';
            $horaFim    = $horasFim[$index]    ?? '';
        
            // Combina data e hora para formar um datetime (para a consulta)
            $data_hora_inicio = date("Y-m-d H:i:s", strtotime("$data $horaInicio"));
            $data_hora_fim    = date("Y-m-d H:i:s", strtotime("$data $horaFim"));
        
            // Obtém o nome do espaço através do model; se não encontrar, usa o próprio id
            $espacoInfo = $this->espacoModel->find($id_espaco);
            $nomeEspaco = ($espacoInfo && isset($espacoInfo->nome)) ? $espacoInfo->nome : $id_espaco;
        
            // Formata as datas para o padrão dd/mm/aaaa HH:MM
            $data_hora_inicio_format = date("d/m/Y H:i", strtotime("$data $horaInicio"));
            $data_hora_fim_format    = date("d/m/Y H:i", strtotime("$data $horaFim"));
        
            // Consulta para verificar se já existe um evento para o mesmo espaço que se sobreponha ao novo
            $conflictCount = $this->eventoEspacoModel
                ->where('id_espaco', $id_espaco)
                ->where('data_hora_inicio <', $data_hora_fim)
                ->where('data_hora_fim >', $data_hora_inicio)
                ->countAllResults();
        
            if ($conflictCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Já existe um evento agendado para o espaço {$nomeEspaco} no período de {$data_hora_inicio_format} a {$data_hora_fim_format}."
                ]);
            }
        }

        // Monta os dados gerais do evento para a tabela "eventos"
        $eventoData = [
            // Dados do solicitante
            'id_solicitante'         => isset($this->userInfo['id']) ? $this->userInfo['id'] : 0,
            'id_unidade_solicitante' => isset($this->userInfo['id_unidade']) ? $this->userInfo['id_unidade'] : 0,
            
            // Dados do responsável:
            // Se interno, utiliza os dados do usuário logado e deixa os campos de responsável externo nulos;
            // se externo, utiliza os campos do formulário.
            'id_responsavel'           => (isset($post['eu_sou_o_responsavel']) && $post['eu_sou_o_responsavel'] === 'S') ? (isset($this->userInfo['id']) ? $this->userInfo['id'] : 0) : null,
            'id_unidade_responsavel'   => (isset($post['eu_sou_o_responsavel']) && $post['eu_sou_o_responsavel'] === 'S') ? (isset($this->userInfo['id_unidade']) ? $this->userInfo['id_unidade'] : 0) : null,
            'nome_responsavel'         => (isset($post['responsavel_externo']) && $post['responsavel_externo'] === 'S') ? ($post['responsavel_nome_externo'] ?? '') : null,
            'nome_unidade_responsavel' => (isset($post['responsavel_externo']) && $post['responsavel_externo'] === 'S') ? ($post['responsavel_unidade_externo'] ?? '') : null,
            
            'email_responsavel'     => $post['responsavel_email'] ?? '',
            'telefone1_responsavel' => $post['responsavel_telefone1'] ?? '',
            'telefone2_responsavel' => $post['responsavel_telefone2'] ?? '',
            
            // Dados do aprovador:
            // Se o checkbox "eu_sou_o_aprovador" estiver marcado, usa os dados do usuário logado;
            // caso contrário, utiliza os dados enviados pelo formulário.
            'id_aprovador'         => ($post['eu_sou_o_aprovador'] === 'S') ? (isset($this->userInfo['id']) ? $this->userInfo['id'] : 0) : $post['aprovador_nome'],
            'id_unidade_aprovador' => ($post['eu_sou_o_aprovador'] === 'S') ? (isset($this->userInfo['id_unidade']) ? $this->userInfo['id_unidade'] : 0) : $post['aprovador_unidade'],
            'email_aprovador'      => $post['aprovador_email'] ?? '',
            'telefone1_aprovador'  => $post['aprovador_telefone1'] ?? '',
            'telefone2_aprovador'  => $post['aprovador_telefone2'] ?? '',
            
            'nome'                     => $post['titulo_evento'] ?? '',
            'quantidade_participantes' => $post['quantidade_participantes'] ?? 0,
            'assinado_solicitante'     => 0,
            'assinado_componente_org'  => 0,
            'observacoes'              => $post['observacoes'] ?? ''
        ];

        echo "<pre>"; dd(print_r($eventoData));

        // Inicia uma transação para garantir a integridade dos dados
        $db = \Config\Database::connect();
        $db->transStart();

        // Insere os dados gerais do evento
        $eventoId = $this->eventoModel->insert($eventoData);
        if (!$eventoId) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar os dados do evento.'
            ]);
        }

        // Insere os dados de cada espaço agendado
        foreach ($espacos as $index => $id_espaco) {
            $data       = $datas[$index]       ?? '';
            $horaInicio = $horasInicio[$index] ?? '';
            $horaFim    = $horasFim[$index]    ?? '';

            $data_hora_inicio = date("Y-m-d H:i:s", strtotime("$data $horaInicio"));
            $data_hora_fim    = date("Y-m-d H:i:s", strtotime("$data $horaFim"));

            $espacoData = [
                'id_evento'        => $eventoId,
                'id_espaco'        => $id_espaco,
                'data_hora_inicio' => $data_hora_inicio,
                'data_hora_fim'    => $data_hora_fim,
            ];
            $this->eventoEspacoModel->insert($espacoData);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar os dados. Tente novamente.'
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'id_evento' => $eventoId,
            'message'   => 'Evento cadastrado com sucesso!'
        ]);
    }
}
