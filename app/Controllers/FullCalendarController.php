<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\EventoModel;
use App\Models\StatusDefinicaoModel;
use CodeIgniter\RESTful\ResourceController;

class FullCalendarController extends ResourceController
{
    protected $campusModel;
    protected $eventoModel;
    protected $statusDefinicaoModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        helper('sso');
        $this->campusModel = new CampusModel();
        $this->eventoModel = new EventoModel();
        $this->statusDefinicaoModel = new StatusDefinicaoModel();
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo(getSystemId()) : null;
    }
    
    public function index()
    {
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
        $userInfo = $this->userInfo;
        $statusList = $this->statusDefinicaoModel->getAllOrdered();
        
        if ($userInfo['id_nivel'] == 3) {
            $eventList = $this->eventoModel->getEventosPorUsuario($userInfo['id_usuario']);
        } else {
            $eventList = $this->eventoModel->getEventos();
        }

        return view('calendar/index', [
            'idSistema'     => getSystemId(),
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $userInfo,
            'eventList'     => $eventList,
            'statusList'    => $statusList
        ]);
    }

    public function getCalendarData()
    {
        $campi = $this->campusModel->getCampiWithPrediosAndEspacos();
        $resources = [];

        foreach ($campi as $campus) {
            foreach ($campus->predios as $predio) {
                $predioResource = [
                    'id' => 'predio_' . $predio->id,
                    'building' => $campus->sigla,
                    'title' => $predio->nome
                ];

                $children = [];
                foreach ($predio->espacos as $espaco) {
                    $children[] = [
                        'id' => 'espaco_' . $espaco->id,
                        'title' => $espaco->nome
                    ];
                }

                // Adiciona `children` apenas se houver espaços no prédio
                if (!empty($children)) {
                    $predioResource['children'] = $children;
                }

                $resources[] = $predioResource;
            }
        }

        $eventos = $this->eventoModel->getEventosWithEspacosAndPredios($this->userInfo['id_nivel'] < 3 ? 1 : 0);
        $events = [];

        foreach ($eventos as $evento) {
            // Se o evento possui id de espaço, utiliza-o; caso contrário, utiliza o id do prédio
            $resourceId = !empty($evento->resource_id) 
                ? 'espaco_' . $evento->resource_id 
                : 'predio_' . $evento->predio_id;

            $events[] = [
                'id' => 'evento_' . $evento->evento_id,
                'resourceId' => $resourceId,
                'start' => $evento->start,
                'end' => $evento->end,
                'title' => $evento->evento_nome,
                'evento_status' => $evento->evento_status
            ];
        }

        return $this->response->setJSON([
            'resources' => $resources,
            'events' => $events
        ]);
    }

}
