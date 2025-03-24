<?php

if (!function_exists('getUnits')) {
    /**
     * Obtém a lista de unidades a partir do endpoint /api/units do SSO.
     *
     * @return array|null Retorna um array de unidades ou null em caso de erro.
     */
    function getUnits()
    {
        // Verifica se o cookie 'jwt_token' existe antes de prosseguir
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null;
        }
        
        $jwtToken  = $_COOKIE['jwt_token'];
        $ssoBaseUrl = getenv('SSO_BASE_URL'); // URL do SSO definida no .env
        $endpoint  = $ssoBaseUrl . "/api/units";
        
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept'        => 'application/json'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            // Verifica se não houve erro e se a resposta contém as unidades
            if (isset($data['error']) && $data['error'] === false && isset($data['units'])) {
                return $data['units'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter unidades: ' . $e->getMessage());
        }
        
        return null;
    }
}

if (!function_exists('getUsers')) {
    /**
     * Obtém a lista de usuários com permissão no sistema a partir do endpoint /api/users do SSO.
     *
     * @return array|null Retorna um array de usuários ou null em caso de erro.
     */
    function getUsers()
    {
        // Verifica se o cookie 'jwt_token' existe antes de prosseguir
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null;
        }
        
        $jwtToken  = $_COOKIE['jwt_token'];
        $ssoBaseUrl = getenv('SSO_BASE_URL'); // URL do SSO definida no .env
        $endpoint  = $ssoBaseUrl . "/api/users";
        
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept'        => 'application/json'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            // Verifica se não houve erro e se a resposta contém os usuários
            if (isset($data['error']) && $data['error'] === false && isset($data['users'])) {
                return $data['users'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter usuários: ' . $e->getMessage());
        }
        
        return null;
    }
}
