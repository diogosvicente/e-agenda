<?php

namespace App\Controllers;

class CalendarController extends BaseController
{
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function index()
    {
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;

        return view('calendar/index', [
            'idSistema'     => $this->idSistema,
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $this->userInfo
        ]);
    }
}
