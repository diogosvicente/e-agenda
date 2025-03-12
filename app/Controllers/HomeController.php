<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
	{
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;
	}

    public function index()
    {
        return view('base/inicio', [
            'idSistema'     => $this->idSistema,
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $this->userInfo
        ]);
    }
}
