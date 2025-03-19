<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\PredioModel;
use App\Models\EspacosModel;
use App\Models\RecursosModel;
use CodeIgniter\Controller;

class SchedulingController extends BaseController
{
    protected $campusModel;
    protected $predioModel;
    protected $espacoModel;
    protected $recursoModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        $this->campusModel = new CampusModel();
        $this->predioModel = new PredioModel();
        $this->espacoModel = new EspacosModel();
        $this->recursoModel = new RecursosModel();

        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');

        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;
    }

    public function add()
    {
        // Obtém todos os campi com seus prédios e espaços
        $campus = $this->campusModel->getCampiWithPrediosAndEspacos();
        
        // Obtém todos os recursos organizados por espaço
        $recursos = [];
        $recursosGerais = [];

        $recursosList = $this->recursoModel->findAll();

        foreach ($recursosList as $recurso) {
            if (!empty($recurso->id_espaco)) {
                $recursos[$recurso->id_espaco][] = $recurso;
            } else {
                $recursosGerais[] = $recurso;
            }
        }

        return view('scheduling/add', [
            'idSistema'      => $this->idSistema,
            'ssoBaseUrl'     => $this->ssoBaseUrl,
            'userInfo'       => $this->userInfo,
            'campus'         => $campus,
            'recursos'       => $recursos,
            'recursosGerais' => $recursosGerais
        ]);
    }
}
