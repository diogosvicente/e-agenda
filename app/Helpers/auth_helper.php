<?php

use CodeIgniter\HTTP\CURLRequest;

if (!function_exists('getUserInfo')) {
    function getUserInfo()
    {
        // Verifica se o cookie 'jwt_token' existe antes de prosseguir
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null; // Retorna null se não houver token (usuário deslogado)
        }

        $jwtToken = $_COOKIE['jwt_token'];
        $ssoBaseUrl = getenv('SSO_BASE_URL'); // Pegando a URL do SSO a partir do .env
        $endpoint = $ssoBaseUrl . "/api/userinfo";

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['error']) && $data['error'] === false) {
                return $data;
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter usuário: ' . $e->getMessage());
        }

        return null; // Retorna null se houver erro
    }
}
