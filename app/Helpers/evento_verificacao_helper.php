<?php

if (!function_exists('gerarRegistroEventoVerificacao')) {
    /**
     * Gera um registro na tabela evento_verificacao para o evento e retorna o token gerado.
     *
     * O token é gerado no formato "idEvento.tokenAleatorio".
     *
     * @param int $idEvento
     * @return string O token gerado.
     */
    function gerarRegistroEventoVerificacao($idEvento)
    {
        // Gera uma parte aleatória para o token
        $tokenPart = bin2hex(random_bytes(16));
        // Monta o token no formato "idEvento.tokenAleatorio"
        $token = $idEvento . '.' . $tokenPart;
        
        // Gera códigos de verificação e CRC (ajuste os tamanhos conforme sua necessidade)
        $codigoVerificador = strtoupper(bin2hex(random_bytes(4))); // Ex.: 8 caracteres
        $codigoCRC         = strtoupper(bin2hex(random_bytes(4))); // Ex.: 8 caracteres
        
        // Define datas
        $agora  = new DateTime();
        // Token sem expiração: data distante no futuro
        $expira = new DateTime('9999-12-31 23:59:59');
        
        $data = [
            'id_evento'          => $idEvento,
            'token'              => $token,
            'codigo_verificador' => $codigoVerificador,
            'codigo_crc'         => $codigoCRC,
            'created_at'         => $agora->format('Y-m-d H:i:s'),
            'updated_at'         => $agora->format('Y-m-d H:i:s'),
        ];
        
        // Usa o model de evento_verificacao para inserir o registro
        $eventoVerificacaoModel = model('App\Models\EventoVerificacaoModel');
        $eventoVerificacaoModel->insert($data);
        
        return $token;
    }
}
