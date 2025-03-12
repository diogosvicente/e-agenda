<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GuestFilter implements FilterInterface
{
    private $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = getenv('JWT_SECRET');
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $cookie = $request->getCookie('jwt_token');

        if ($cookie) {
            try {
                // Decodifica o token para verificar se ainda é válido
                $decoded = JWT::decode($cookie, new Key($this->jwtSecret, 'HS256'));

                // Se o usuário já estiver autenticado, redireciona para a página inicial ou dashboard
                return redirect()->to('/');
            } catch (\Exception $e) {
                // Se o token for inválido ou expirado, permite o acesso ao login
                return;
            }
        }

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nenhuma ação necessária
    }
}
