<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\PredioModel;
use App\Models\EspacosModel;
use App\Models\RecursosModel;
use App\Models\EventoModel;
use App\Models\EventoEspacoDataHoraModel;
use App\Models\EventoRecursosModel;
use App\Models\EventoStatusModel;
use CodeIgniter\Controller;

class SchedulingController extends BaseController
{
    protected $campusModel;
    protected $predioModel;
    protected $espacoModel;
    protected $recursoModel;
    protected $eventoModel;
    protected $eventoEspacoDataHoraModel;
    protected $eventoRecursosModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        $this->campusModel                  = new CampusModel();
        $this->predioModel                  = new PredioModel();
        $this->espacoModel                  = new EspacosModel();
        $this->recursoModel                 = new RecursosModel();
        $this->eventoModel                  = new EventoModel();
        $this->eventoEspacoDataHoraModel    = new EventoEspacoDataHoraModel();
        $this->eventoRecursosModel          = new EventoRecursosModel();
        $this->eventoStatusModel            = new EventoStatusModel();

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

        // Processa os arrays de espaços e horários
        $espacos     = $post['espaco']       ?? [];
        $datas       = $post['data_inicio']  ?? [];
        $horasInicio = $post['hora_inicio']  ?? [];
        $horasFim    = $post['hora_fim']     ?? [];

        // Monta os dados gerais do evento a partir dos inputs do POST
        $eventoData = [
            'id_solicitante'           => $post['id_solicitante'],
            'id_unidade_solicitante'   => $post['id_unidade_solicitante'],
            'nome'                     => $post['titulo_evento'],
            'quantidade_participantes' => $post['quantidade_participantes'] ?? 0,
            'observacoes'              => $post['observacoes'] ?? '',
            'assinado_solicitante'     => 0,
            'assinado_componente_org'  => 0,
            'email_responsavel'        => $post['responsavel_email'] ?? '',
            'telefone1_responsavel'    => $post['responsavel_telefone1'] ?? '',
            'telefone2_responsavel'    => $post['responsavel_telefone2'] ?? '',
            'email_aprovador'          => $post['aprovador_email'] ?? '',
            'telefone1_aprovador'      => $post['aprovador_telefone1'] ?? '',
            'telefone2_aprovador'      => $post['aprovador_telefone2'] ?? '',
            'id_responsavel'           => $post['responsavel_nome_id'] ?? 0,
            'id_unidade_responsavel'   => $post['responsavel_unidade_id'] ?? 0,
            'nome_responsavel'         => $post['responsavel_nome_externo'] ?? '',
            'nome_unidade_responsavel' => $post['responsavel_unidade_externo'] ?? '',
            'id_aprovador'             => $post['aprovador_nome_id'] ?? 0,
            'id_unidade_aprovador'     => $post['aprovador_unidade_id'] ?? 0
        ];

        // Inicia a transação
        $db = \Config\Database::connect();
        $db->transStart();

        // Insere os dados gerais do evento
        $eventoId = $this->eventoModel->insert($eventoData);
        if (!$eventoId) {
            $error = $this->eventoModel->errors();
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar evento: ' . json_encode($error)
            ]);
        }

        // Tratamento dos espaços e horários
        // Se houver somente um espaço selecionado e múltiplos horários, aplica todos a esse espaço.
        if (count($espacos) === 1 && count($datas) > 1) {
            $idEspaco = $espacos[0];
            for ($i = 0; $i < count($datas); $i++) {
                if (!isset($datas[$i], $horasInicio[$i], $horasFim[$i])) {
                    continue;
                }
                $data_hora_inicio = date("Y-m-d H:i:s", strtotime($datas[$i] . ' ' . $horasInicio[$i]));
                $data_hora_fim    = date("Y-m-d H:i:s", strtotime($datas[$i] . ' ' . $horasFim[$i]));

                // Verifica conflito para esse espaço no horário
                $conflictCount = $this->eventoEspacoDataHoraModel
                    ->where('id_espaco', $idEspaco)
                    ->where('data_hora_inicio <', $data_hora_fim)
                    ->where('data_hora_fim >', $data_hora_inicio)
                    ->countAllResults();
                if ($conflictCount > 0) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Já existe um evento agendado para o espaço {$idEspaco} no período de " .
                                    date("d/m/Y H:i", strtotime($datas[$i] . ' ' . $horasInicio[$i])) .
                                    " a " .
                                    date("d/m/Y H:i", strtotime($datas[$i] . ' ' . $horasFim[$i]))
                    ]);
                }

                $espacoData = [
                    'id_evento'        => $eventoId,
                    'id_espaco'        => $idEspaco,
                    'data_hora_inicio' => $data_hora_inicio,
                    'data_hora_fim'    => $data_hora_fim
                ];
                $result = $this->eventoEspacoDataHoraModel->insert($espacoData);
                if (!$result) {
                    log_message('error', 'Erro ao inserir em evento_espaco_data_hora: ' .
                        json_encode($this->eventoEspacoDataHoraModel->errors()));
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Erro ao salvar os espaços do evento.'
                    ]);
                }
            }
        }
        // Se os arrays tiverem a mesma quantidade, associa cada índice individualmente
        else if (count($espacos) === count($datas)) {
            foreach ($espacos as $index => $idEspaco) {
                if (!isset($datas[$index], $horasInicio[$index], $horasFim[$index])) {
                    continue;
                }
                $data_hora_inicio = date("Y-m-d H:i:s", strtotime($datas[$index] . ' ' . $horasInicio[$index]));
                $data_hora_fim    = date("Y-m-d H:i:s", strtotime($datas[$index] . ' ' . $horasFim[$index]));

                // Verifica conflito para esse espaço no horário
                $conflictCount = $this->eventoEspacoDataHoraModel
                    ->where('id_espaco', $idEspaco)
                    ->where('data_hora_inicio <', $data_hora_fim)
                    ->where('data_hora_fim >', $data_hora_inicio)
                    ->countAllResults();
                if ($conflictCount > 0) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Já existe um evento agendado para o espaço {$idEspaco} no período de " .
                                    date("d/m/Y H:i", strtotime($datas[$index] . ' ' . $horasInicio[$index])) .
                                    " a " .
                                    date("d/m/Y H:i", strtotime($datas[$index] . ' ' . $horasFim[$index]))
                    ]);
                }

                $espacoData = [
                    'id_evento'        => $eventoId,
                    'id_espaco'        => $idEspaco,
                    'data_hora_inicio' => $data_hora_inicio,
                    'data_hora_fim'    => $data_hora_fim
                ];
                $result = $this->eventoEspacoDataHoraModel->insert($espacoData);
                if (!$result) {
                    log_message('error', 'Erro ao inserir em evento_espaco_data_hora: ' .
                        json_encode($this->eventoEspacoDataHoraModel->errors()));
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Erro ao salvar os espaços do evento.'
                    ]);
                }
            }
        }
        // Se os arrays não se alinharem, retorna erro
        else {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados de espaços e horários inconsistentes.'
            ]);
        }

        // Recursos
        $recursos = $post['recursos'] ?? [];
        $quantidades = $post['quantidade_recurso'] ?? [];
        foreach ($recursos as $idRecurso) {
            $resourceData = [
                'id_evento'  => $eventoId,
                'id_recurso' => $idRecurso,
                'quantidade' => $quantidades[$idRecurso] ?? 1
            ];

            $result = $this->eventoRecursosModel->insert($resourceData);
            if (!$result) {
                log_message('error', 'Erro ao inserir em evento_recursos: ' .
                    json_encode($this->eventoRecursosModel->errors()));
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao salvar os recursos do evento.'
                ]);
            }
        }

        // Insere o status do evento como "assinatura pendente"
        $statusData = [
            'id_evento' => $eventoId,
            'status'    => 'assinatura pendente'
        ];
        $result = $this->eventoStatusModel->insert($statusData);
        if (!$result) {
            log_message('error', 'Erro ao inserir em evento_status: ' .
                json_encode($this->eventoStatusModel->errors()));
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar o status do evento.'
            ]);
        }

        // Finaliza a transação
        $db->transComplete();
        if ($db->transStatus() === false) {
            $dbError = $db->error();
            log_message('error', 'Erro ao finalizar transação: ' . json_encode($dbError));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar os dados. Tente novamente. Erro: ' . json_encode($dbError)
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'id_evento' => $eventoId,
            'message'   => 'Evento cadastrado com sucesso!'
        ]);
    }

}
