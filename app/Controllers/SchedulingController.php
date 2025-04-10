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
use App\Models\EventoVerificacaoModel;
use App\Models\StatusDefinicaoModel;
use App\Models\TokenModel;
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
    protected $eventoStatusModel;
    protected $eventoVerificacaoModel;
    protected $statusDefinicaoModel;
    protected $tokenModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        helper(['url', 'email', 'evento_format', 'evento_verificacao']);
        $this->campusModel                  = new CampusModel();
        $this->predioModel                  = new PredioModel();
        $this->espacoModel                  = new EspacosModel();
        $this->recursoModel                 = new RecursosModel();
        $this->eventoModel                  = new EventoModel();
        $this->eventoEspacoDataHoraModel    = new EventoEspacoDataHoraModel();
        $this->eventoRecursosModel          = new EventoRecursosModel();
        $this->eventoStatusModel            = new EventoStatusModel();
        $this->eventoVerificacaoModel       = new EventoVerificacaoModel();
        $this->statusDefinicaoModel         = new StatusDefinicaoModel();
        $this->tokenModel                   = new TokenModel();

        $this->idSistema  = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');

        // Obtém os dados do usuário via helper (definido, por exemplo, em auth_helper.php)
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo(getSystemId()) : null;

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

        // echo "<pre>"; dd(print_r($post));

        // Dados gerais do evento
        $eventoData = [
            'id_solicitante'           => $post['id_solicitante'],
            'id_unidade_solicitante'   => $post['id_unidade_solicitante'],
            'email_solicitante'        => $post['email_solicitante'],
            'nome'                     => $post['titulo_evento'],
            'quantidade_participantes' => $post['quantidade_participantes'] ?? 0,
            'observacoes'              => $post['observacoes'] ?? '',
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

        // Processa os dados dos espaços e horários
        // O formulário envia os espaços na estrutura:
        // cada espaço pode ter dados enviados tanto diretamente (chaves "data_inicio", etc.)
        // quanto dentro de um subarray "datas". Mesclamos ambos.
        $espacos = $post['espacos'] ?? [];
        $espacoDataArray = array_reduce($espacos, function($carry, $espaco) use ($eventoId) {
            // Se houver dados no nível superior, cria um registro padrão
            $defaultData = [];
            if (isset($espaco['data_inicio'], $espaco['hora_inicio'], $espaco['hora_fim'])) {
                $defaultData[] = [
                    'data_inicio' => $espaco['data_inicio'],
                    'hora_inicio' => $espaco['hora_inicio'],
                    'hora_fim'    => $espaco['hora_fim']
                ];
            }
            // Se existir o subarray "datas", utiliza-o
            $nestedData = isset($espaco['datas']) && is_array($espaco['datas']) ? $espaco['datas'] : [];
            // Mescla os dois conjuntos de dados
            $datas = array_merge($defaultData, $nestedData);
            if (empty($datas)) {
                return $carry;
            }
            // Processa cada entrada de data/hora
            $rows = array_map(function($data) use ($eventoId, $espaco) {
                // Se o id vier com prefixo "P-", trata como prédio
                if (isset($espaco['id']) && strpos($espaco['id'], 'P-') === 0) {
                    $id_predio = substr($espaco['id'], 2); // remove "P-"
                    $id_espaco = null;
                } else {
                    $id_predio = null;
                    $id_espaco = isset($espaco['id']) ? $espaco['id'] : null;
                }
                return [
                    'id_evento'        => $eventoId,
                    'id_predio'        => $id_predio,
                    'id_espaco'        => $id_espaco,
                    'data_hora_inicio' => date("Y-m-d H:i:s", strtotime($data['data_inicio'] . ' ' . $data['hora_inicio'])),
                    'data_hora_fim'    => date("Y-m-d H:i:s", strtotime($data['data_inicio'] . ' ' . $data['hora_fim']))
                ];
            }, $datas);
            return array_merge($carry, $rows);
        }, []);

        if (empty($espacoDataArray)) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados de espaços e horários inconsistentes.'
            ]);
        }

        // Verifica conflitos para cada registro de data/hora antes de inserir
        foreach ($espacoDataArray as $registro) {
            if ($this->eventoEspacoDataHoraModel->isConflict($registro['id_espaco'], $registro['id_predio'], $registro['data_hora_inicio'], $registro['data_hora_fim'])) {
                $db->transRollback();
                
                // Se o id_espaco não for nulo, utiliza o nome do espaço; caso contrário, utiliza o nome do prédio
                if (!is_null($registro['id_espaco'])) {
                    $nomeItem = getNameById($registro['id_espaco'], 'espacos', 'nome');
                } else {
                    $nomeItem = getNameById($registro['id_predio'], 'predio', 'nome');
                }
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Já existe um evento agendado para " . $nomeItem .
                                 " no período de " .
                                 date("d/m/Y H:i", strtotime($registro['data_hora_inicio'])) . " a " .
                                 date("d/m/Y H:i", strtotime($registro['data_hora_fim']))
                ]);
            }
        }

        // Insere os registros de espaços e horários em lote
        $result = $this->eventoEspacoDataHoraModel->insertBatch($espacoDataArray);
        if (!$result) {
            log_message('error', 'Erro ao inserir em evento_espaco_data_hora: ' .
                json_encode($this->eventoEspacoDataHoraModel->errors()));
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar os espaços do evento.'
            ]);
        }

        // Processa os recursos
        $recursos = $post['recursos'] ?? [];
        $resourcesToInsert = [];
        if (!empty($recursos) && is_array($recursos)) {
            foreach ($recursos as $idRecurso) {
                $resourcesToInsert[] = [
                    'id_evento'  => $eventoId,
                    'id_recurso' => $idRecurso,
                    'quantidade' => isset($post['quantidade_recurso'][$idRecurso]) ? $post['quantidade_recurso'][$idRecurso] : 1
                ];
            }
            $result = $this->eventoRecursosModel->insertBatch($resourcesToInsert);
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
            'id_evento'     => $eventoId,
            'id_status'     => 1,
            'id_usuario'    => $post['id_solicitante']
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

        // Mescla os dados do evento com os dados dos espaços e recursos
        $eventoInfo = $eventoData;
        $eventoInfo['horarios'] = $espacoDataArray;
        $eventoInfo['recursos'] = $resourcesToInsert;

        $tokenAcompanhamento = gerarRegistroEventoVerificacao($eventoId);

        $tokenAprovacao = $this->tokenModel->gerarToken($post['aprovador_nome_id'], 'aprovacao', $eventoId);

        $emailEnviado = enviar_email_aprovador($tokenAprovacao, $eventoInfo);

        if (!$emailEnviado) {
            log_message('error', 'Erro ao enviar e-mail para o aprovador.');
        }

        return $this->response->setJSON([
            'success'   => true,
            'id_evento' => $eventoId,
            'token'     => $tokenAcompanhamento,
            'message'   => 'Evento cadastrado com sucesso!<br>Um e-mail foi enviado para o aprovador confirmar a solicitação!'
        ]);
    }

    public function approve($token)
    {
        // Obtém o token válido
        $row = $this->tokenModel->obterTokenValido($token);

        if (!$row) {
            return view('errors/invalid_token', [
                'mensagem'   => 'O link fornecido é inválido, expirou ou já foi utilizado.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Verifica se o token expirou (mesmo que, para aprovação, o token não deva expirar,
        // essa verificação é uma segurança extra)
        $expira_em   = $row->expira_em;
        $currentDate = date('Y-m-d H:i:s');
        if ($currentDate > $expira_em) {
            return view('errors/invalid_token', [
                'mensagem'   => 'O token expirou.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Verifica se o usuário logado tem o mesmo id do usuário associado ao token
        if (!isset($this->userInfo['id_usuario']) || $this->userInfo['id_usuario'] != $row->id_usuario) {
            return view('errors/invalid_token', [
                'mensagem'   => 'Você não tem permissão para confirmar essa solicitação.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Extrai o id do evento a partir do token (formato "id.token")
        $partes = explode('.', $token);
        $eventoId = $partes[0];

        // Obtém os dados do evento e relacionados via models
        $evento         = $this->eventoModel->find($eventoId);
        $datasHorarios  = $this->eventoEspacoDataHoraModel->where('id_evento', $eventoId)->findAll();
        $recursos       = $this->eventoRecursosModel->where('id_evento', $eventoId)->findAll();
        $status         = $this->eventoStatusModel->where('id_evento', $eventoId)->findAll();

        // Carrega o helper para formatar o texto do evento
        helper('evento_format');
        $textoEvento = formatar_evento_aprovacao($evento, $datasHorarios, $recursos, $status);

        $greetings = array(
            "aprovador"     => tradeNameByID($evento->id_aprovador, 'usuarios', 'nome'),
            "data_cadastro" => $evento->created_at
        );

        // Token válido e usuário autorizado; exibe a tela de confirmação da aprovação,
        // passando o texto formatado do evento
        return view('scheduling/approve_confirm', [
            'usuario'      => $this->userInfo,
            'token'        => $token,
            'ssoBaseUrl'   => $this->ssoBaseUrl,
            'idSistema'    => $this->idSistema,
            'textoEvento'  => $textoEvento,
            'greetings'    => $greetings
        ]);
    }

    public function confirm_approval()
    {
        // Obtém a senha enviada via POST
        $senhaInput = $this->request->getPost('senha');
        $token      = $this->request->getPost('token');

        // Verifica se os dados do usuário foram obtidos via SSO (helper getUserInfo)
        if (!$this->userInfo || !isset($this->userInfo['senha'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não foi possível recuperar as informações do usuário.'
            ]);
        }

        // Validação da senha usando password_verify()
        if (!password_verify($senhaInput, $this->userInfo['senha'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Senha incorreta.'
            ]);
        }

        // Valida o token
        $row = $this->tokenModel->obterTokenValido($token);
        if (!$row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token inválido ou expirado.'
            ]);
        }

        // Verifica se o usuário logado é o mesmo associado ao token
        if (!isset($this->userInfo['id_usuario']) || $this->userInfo['id_usuario'] != $row->id_usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para confirmar essa solicitação.'
            ]);
        }

        // Extrai o id do evento a partir do token (formato "id.token")
        $parts    = explode('.', $token);
        $eventoId = $parts[0];

        // Registra o novo status usando o método do model de status
        $insertResult = $this->eventoStatusModel->inserirStatusAprovacao($eventoId, $this->userInfo['id_usuario']);

        if ($insertResult !== false) {
            // Remove o token da tabela tokens após aprovação
            $this->tokenModel->delete($row->id);

            // Gera o link para a página de acompanhamento do pedido de agendamento
            $acompanhamentoUrl = base_url('agendamento/acompanhamento/' . $this->eventoVerificacaoModel->obterTokenPorEvento($eventoId));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Solicitação aprovada com sucesso.<hr><a href="' . $acompanhamentoUrl . '" class="btn btn-primary">Clique aqui</a> para acompanhar o pedido de agendamento.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao registrar a aprovação.'
            ]);
        }
    }

    public function followUp($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            // Token inválido
            return view(
                'errors/invalid_token',
                [
                    'mensagem' => 'Token inválido.',
                    'ssoBaseUrl'    => $this->ssoBaseUrl,
                    'idSistema'     => $this->idSistema,
                ]
            );
        }

        $eventoId = $parts[0];

        $registro = $this->eventoVerificacaoModel->obterTokenValido($token);
        if (!$registro) {
            return view(
                'errors/invalid_token',
                [
                    'mensagem' => 'Token inválido ou expirado.',
                    'ssoBaseUrl'    => $this->ssoBaseUrl,
                    'idSistema'     => $this->idSistema,
                ]
            );
        }

        $evento = $this->eventoModel->find($eventoId);
        $statusPossiveis = $this->statusDefinicaoModel->getAllOrdered();
        $historicoStatus = $this->eventoStatusModel->getStatusByEvento($eventoId);
        $ultimoStatus = $this->eventoStatusModel->getUltimoStatusByEvento($eventoId);

        return view('scheduling/followUp', [
            'evento'            => $evento,
            'token'             => $token,
            'statusPossiveis'   => $statusPossiveis,
            'historicoStatus'   => $historicoStatus,
            'ultimoStatus'      => $ultimoStatus,
            'ssoBaseUrl'        => $this->ssoBaseUrl,
            'idSistema'         => $this->idSistema,
        ]);
    }

    public function updateStatus()
    {
        // Verifica se a requisição é AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Obtém os dados enviados pelo POST
        $idEvento = $this->request->getPost('id_evento');
        $idStatus = $this->request->getPost('id_status');

        // Recupera o ID do usuário logado a partir da sessão
        $idUsuario = $this->userInfo['id_usuario'];

        // Validação simples dos parâmetros
        if (empty($idEvento) || empty($idStatus) || empty($idUsuario)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parâmetros inválidos'
            ]);
        }

        // Prepara os dados para inserção, incluindo observações caso necessário
        $data = [
            'id_evento'   => $idEvento,
            'id_status'   => $idStatus,
            'id_usuario'  => $idUsuario,
            'observacoes' => NULL,
            'observacoes' => $this->request->getPost('observacoes') ?? ''
        ];

        $insertedId = $this->eventoStatusModel->insert($data);

        // Retorna a resposta em formato JSON de acordo com o sucesso ou erro da operação
        if ($insertedId) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'inserted_id' => $insertedId
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar o status'
            ], 500);
        }
    }

}
