<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class SSOController extends ResourceController
{
    public function callback()
    {
        $code = $this->request->getGet('code');

        $idSistema = getenv('SISTEMA_ID');
        $ssoBaseUrl = getenv('SSO_BASE_URL');

        if (!$idSistema) {
            return $this->fail('ID do sistema não configurado no .env.');
        }

        if (!$code) {
            return $this->fail('Código de autorização ausente.');
        }

        $url = "{$ssoBaseUrl}/sso/token?code={$code}&sistema={$idSistema}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        echo "<pre>"; dd(print_r($data));

        if (!isset($data['access_token'])) {
            return $this->fail('Erro ao obter token JWT.');
        }

        // Carrega a view `store_token` para armazenar o token no LocalStorage via JavaScript
        return view('sso/store_token', ['access_token' => $data['access_token']]);
    }
}
