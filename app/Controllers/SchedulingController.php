<?php

namespace App\Controllers;

class SchedulingController extends BaseController
{
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function add()
    {
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;

        return view('scheduling/add', [
            'idSistema'     => $this->idSistema,
            'ssoBaseUrl'    => $this->ssoBaseUrl,
            'userInfo'      => $this->userInfo
        ]);
    }
}
