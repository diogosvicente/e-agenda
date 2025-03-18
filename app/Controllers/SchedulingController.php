<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\PredioModel;
use App\Models\EspacosModel;
use CodeIgniter\Controller;

class SchedulingController extends BaseController
{
    protected $campusModel;
    protected $predioModel;
    protected $espacoModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        // Instancia os models no construtor
        $this->campusModel = new CampusModel();
        $this->predioModel = new PredioModel();
        $this->espacoModel = new EspacosModel();

        // Obtém variáveis do ambiente
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');

        // Obtém informações do usuário se houver um token JWT válido
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;
    }

    public function add()
    {
        // Obtém todos os campi com seus prédios e espaços
        $campus = $this->campusModel->getCampiWithPrediosAndEspacos();
        
        return view('scheduling/add', [
            'idSistema'     => $this->idSistema,
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $this->userInfo,
            'campus'        => $campus
        ]);
    }
}
