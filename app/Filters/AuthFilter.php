<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    private $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = getenv('JWT_SECRET');
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $cookie = $request->getCookie('jwt_token'); // Obtém o JWT do cookie

        if (!$cookie) {
            return redirect()->to('/login')->with('error', 'Você precisa estar autenticado.');
        }

        try {
            // Decodifica o token JWT
            $decoded = JWT::decode($cookie, new Key($this->jwtSecret, 'HS256'));

            // Adiciona os dados do usuário no request para serem usados em controllers/views
            $request->setGlobal('user', (array) $decoded);
        } catch (\Exception $e) {
            return redirect()->to('/login')->with('error', 'Sessão expirada. Faça login novamente.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nenhuma ação necessária após a requisição
    }
}
