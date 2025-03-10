<?php

namespace App\Controllers;

class SobreController extends BaseController
{

    protected $idSistema;
    protected $ssoBaseUrl;

    public function __construct()
	{
		helper('cpf_helper');
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
	}

    public function index()
    {

        return view('base/sobre', [
            'idSistema' => $this->idSistema,
            'ssoBaseUrl' => $this->ssoBaseUrl
        ]);
    }
}