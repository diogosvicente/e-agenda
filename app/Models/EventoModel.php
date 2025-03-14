<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{
    protected $table = 'evento';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_solicitante',
        'nome_solicitante',
        'id_responsavel',
        'telefone_responsavel',
        'email_responsavel',
        'nome',
        'quantidade_participantes',
        'assinado_solicitante',
        'assinado_componente_org',
        'observacoes'
    ];
}
