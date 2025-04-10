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

if (!function_exists('tradeNameByID')) {
    /**
     * Chama o endpoint tradeNameByID do SSO para converter um ID no valor de uma coluna.
     *
     * @param mixed  $id     O ID do registro.
     * @param string $table  A tabela destino (ex.: 'usuarios', 'unidades').
     * @param string $column A coluna que se deseja obter (ex.: 'nome', 'sigla').
     *
     * @return mixed Retorna o valor da coluna ou null em caso de erro.
     */
    function tradeNameByID($id, $table, $column)
    {
        // Verifica se o cookie 'jwt_token' existe
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null;
        }
        
        $jwtToken = $_COOKIE['jwt_token'];
        // A URL base do SSO deve estar definida no .env (ex.: SSO_BASE_URL=http://sso.exemplo.com)
        $ssoBaseUrl = getenv('SSO_BASE_URL');
        // Define o endpoint para o método tradeNameByID (note que a rota foi definida como POST)
        $endpoint = $ssoBaseUrl . "/api/tradeNameByID";
        
        // Cria uma instância do cliente CURL do CodeIgniter
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept'        => 'application/json'
                ],
                'form_params' => [
                    'id'     => $id,
                    'table'  => $table,
                    'column' => $column
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            // Verifica se a resposta indica sucesso e contém o valor
            if (isset($data['error']) && $data['error'] === false && isset($data['value'])) {
                return $data['value'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter valor com tradeNameByID: ' . $e->getMessage());
        }
        
        return null;
    }
}

if (!function_exists('getSystemId')) {
    /**
     * Obtém o ID do sistema atual a partir do endpoint /api/getSystemID do SSO.
     * Esse endpoint compara a URL base do sistema filho (obtida via base_url()) com a coluna url dos sistemas.
     *
     * @return int|null Retorna o ID do sistema ou null em caso de erro.
     */
    function getSystemId()
    {
        if (!isset($_COOKIE['jwt_token']) || empty($_COOKIE['jwt_token'])) {
            return null;
        }
        
        $jwtToken = $_COOKIE['jwt_token'];
        $ssoBaseUrl = getenv('SSO_BASE_URL');
        
        $childBaseUrl = base_url();
        $normalizedUrl = rtrim($childBaseUrl, '/') . '/';
        $endpoint = $ssoBaseUrl . "/api/getSystemIDbyUrl?base_url=" . urlencode($normalizedUrl);
        
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer " . $jwtToken,
                    'Accept'        => 'application/json'
                    ]
                ]);
            
            $data = json_decode($response->getBody(), true);
            if (isset($data['error']) && $data['error'] === false && isset($data['id_sistema'])) {
                return $data['id_sistema'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao obter ID do sistema: ' . $e->getMessage());
        }
        
        return null;
    }
}
