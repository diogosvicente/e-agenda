<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function __construct()
	{
		helper('cpf_helper');
	}

    public function index()
    {
        $idSistema = getenv('SISTEMA_ID');
        $ssoBaseUrl = getenv('SSO_BASE_URL');
        
        return view('base/inicio', [
            'idSistema' => $idSistema,
            'ssoBaseUrl' => $ssoBaseUrl
        ]);
    }
}
