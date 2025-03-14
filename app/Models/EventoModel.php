<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{
    protected $table = 'evento';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_solicitante', 'nome_solicitante', 'id_responsavel',
        'telefone_responsavel', 'email_responsavel', 'nome',
        'quantidade_participantes', 'assinado_solicitante',
        'assinado_componente_org', 'observacoes'
    ];
    protected $returnType = 'object';

    public function getEventosWithEspacos()
    {
        return $this->select('
                evento.id AS evento_id,
                evento.nome AS evento_nome,
                evento_espaco_data_hora.id AS evento_espaco_id,
                evento_espaco_data_hora.id_espaco AS resource_id,
                evento_espaco_data_hora.data_hora_inicio AS start,
                evento_espaco_data_hora.data_hora_fim AS end
            ')
            ->join('evento_espaco_data_hora', 'evento_espaco_data_hora.id_evento = evento.id')
            ->findAll();
    }
}
