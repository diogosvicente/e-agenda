<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\EventoModel;
use CodeIgniter\RESTful\ResourceController;

class FullCalendarController extends ResourceController
{
    protected $campusModel;
    protected $eventoModel;

    public function __construct()
    {
        $this->campusModel = new CampusModel();
        $this->eventoModel = new EventoModel();
        helper('sso');
    }

    public function index()
    {
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;
        $eventList = $this->eventoModel->getEventos();
        $TUDO = $this->eventoModel->findAll();

        // echo "<pre>"; dd(print_r($eventList));

        return view('calendar/index', [
            'idSistema'     => $this->idSistema,
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $this->userInfo,
            'eventList'     => $eventList
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

        $eventos = $this->eventoModel->getEventosWithEspacosAndPredios();
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
                'title' => $evento->evento_nome
            ];
        }

        return $this->response->setJSON([
            'resources' => $resources,
            'events' => $events
        ]);
    }

}
