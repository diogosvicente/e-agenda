<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function __construct()
	{
        helper(['email_helper', 'cpf_helper']);
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
	}

    public function index()
    {
        return view('login/login', [
            'idSistema' => $this->idSistema,
            'ssoBaseUrl' => $this->ssoBaseUrl
        ]);
    }
    
    public function logout()
    {
        return redirect()->to(base_url());
    }
}
