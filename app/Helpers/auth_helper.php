<?php

use CodeIgniter\HTTP\CURLRequest;

if (!function_exists('getUserInfo')) {
    /**
     * Obtém as informações do usuário a partir do endpoint /api/getUserInfo do SSO,
     * enviando o id do sistema como parâmetro na query string.
     *
     * @param int $sistemaId O ID do sistema atual.
     * @return array|null Retorna um array com as informações do usuário ou null em caso de erro.
     */
    function getUserInfo($sistemaId)
    {
        // Verifica se o cookie 'jwt_token' existe antes de prosseguir
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null; // Retorna null se não houver token (usuário deslogado)
        }

        $jwtToken = $_COOKIE['jwt_token'];
        $ssoBaseUrl = getenv('SSO_BASE_URL'); // URL do SSO definida no .env
        // Inclui o id do sistema na URL do endpoint
        $endpoint = $ssoBaseUrl . "/api//userinfo?sistema=" . urlencode($sistemaId);

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept'        => 'application/json'
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
