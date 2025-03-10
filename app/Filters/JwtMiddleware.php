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
    private $idSistema;
    private $ssoBaseUrl;
    private $callbackUrl;
    private $ssoLoginUrl;

    public function __construct()
    {
        // Lê a chave JWT e o ID do sistema do .env
        $this->jwtSecret   = getenv('JWT_SECRET'); 
        $this->idSistema   = getenv('SISTEMA_ID'); 

        // Lê a URL base do SSO do .env
        // Ex.: http://localhost/e-prefeitura
        $this->ssoBaseUrl  = getenv('SSO_BASE_URL'); 

        // Em vez de pegar do .env, usamos base_url() para montar a callback local
        // Assim, se seu site estiver em http://localhost/e-agenda, base_url('callback') será http://localhost/e-agenda/callback
        $this->callbackUrl = base_url('callback'); 

        // Monta a URL final para redirecionar caso o token seja inválido/ausente
        // http://localhost/e-prefeitura/sso/login?redirect=http://localhost/e-agenda/callback&sistema=4
        $this->ssoLoginUrl = "{$this->ssoBaseUrl}/sso/login?redirect={$this->callbackUrl}&sistema={$this->idSistema}";
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Tenta obter "Authorization: Bearer <token>"
        $header = $request->getHeaderLine('Authorization');

        // Se não existir token ou não estiver no formato "Bearer <TOKEN>", redireciona para SSO
        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return redirect()->to($this->ssoLoginUrl);
        }

        $token = $matches[1];

        try {
            // Decodifica o token JWT
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            // Verifica se o ID do sistema está configurado no .env
            if (!$this->idSistema) {
                return redirect()->to($this->ssoLoginUrl);
            }

            // Verifica se o token corresponde ao sistema atual
            if ($decoded->id_sistema != $this->idSistema) {
                return redirect()->to($this->ssoLoginUrl);
            }

            // Token válido → continua
            return;
        } catch (\Exception $e) {
            // Token inválido ou expirado
            return redirect()->to($this->ssoLoginUrl);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer após a requisição
    }
}
