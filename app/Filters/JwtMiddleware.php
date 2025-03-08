<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware implements FilterInterface
{
    private $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = getenv('JWT_SECRET'); // Obtém a chave do .env
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Recupera o token JWT do cabeçalho
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')->setJSON(['error' => 'Token ausente'])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            // Verifica se o usuário tem permissão para este sistema
            $idSistema = 2; // Defina o ID do sistema filho
            if ($decoded->id_sistema != $idSistema) {
                return service('response')->setJSON(['error' => 'Acesso negado. Sistema não autorizado.'])->setStatusCode(403);
            }

            return; // Token válido, prossegue
        } catch (\Exception $e) {
            return service('response')->setJSON(['error' => 'Token inválido'])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer
    }
}
