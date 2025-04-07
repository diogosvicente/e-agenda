<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoVerificacaoModel extends Model
{
    protected $table         = 'evento_verificacao';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_evento', 
        'token', 
        'codigo_verificador', 
        'codigo_crc', 
        'created_at', 
        'updated_at', 
    ];
    protected $returnType    = 'object';

    /**
     * Obtém um token se ele for válido (não expirado).
     * Se o token estiver expirado ou não existir, retorna null.
     *
     * @param string $token
     * @return object|null
     */
    public function obterTokenValido(string $token)
    {
        $row = $this->where('token', $token)->first();
        if (!$row) {
            return null;
        }
        
        return $row;
    }

    public function obterTokenPorEvento(int $id_evento)
    {
        $row = $this->where('id_evento', $id_evento)->first();
        if (!$row) {
            return null;
        }
        return $row->token;
    }
}
