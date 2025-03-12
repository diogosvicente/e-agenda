<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class LoginController extends Controller
{

    protected $idSistema;
    protected $ssoBaseUrl;

    public function __construct()
	{
        helper(['email_helper', 'cpf_helper']);
        $this->idSistema = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');
	}

    public function index()
    {
        return view('sso/login', [
            'idSistema' => $this->idSistema,
            'ssoBaseUrl' => $this->ssoBaseUrl
        ]);
    }
    public function logout()
    {
        $response = service('response');

        // Remover o cookie JWT definindo um valor vazio e uma data de expiração no passado
        $response->setCookie(
            'jwt_token', '', time() - 3600, // Define um timestamp no passado para expirar imediatamente
            [
                'httponly' => true,           // Protege contra XSS
                'secure'   => getenv('CI_ENVIRONMENT') !== 'development', // Apenas HTTPS em produção
                'samesite' => 'Strict',
                'path'     => '/'
            ]
        );

        return $response->setJSON([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}
