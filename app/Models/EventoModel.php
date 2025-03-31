<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{
    protected $table = 'eventos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_solicitante',
        'id_unidade_solicitante',
        'id_responsavel',
        'nome_responsavel',
        'id_unidade_responsavel',
        'nome_unidade_responsavel',
        'email_responsavel',
        'telefone1_responsavel',
        'telefone2_responsavel',
        'id_aprovador',
        'id_unidade_aprovador',
        'email_aprovador',
        'telefone1_aprovador',
        'telefone2_aprovador',
        'nome',
        'quantidade_participantes',
        'assinado_solicitante',
        'assinado_componente_org',
        'observacoes'
    ];
    protected $returnType = 'object';

    public function getEventosWithEspacos()
    {
        return $this->select('
                eventos.id AS evento_id,
                eventos.nome AS evento_nome,
                evento_espaco_data_hora.id AS evento_espaco_id,
                evento_espaco_data_hora.id_espaco AS resource_id,
                evento_espaco_data_hora.data_hora_inicio AS start,
                evento_espaco_data_hora.data_hora_fim AS end
            ')
            ->join('evento_espaco_data_hora', 'evento_espaco_data_hora.id_evento = eventos.id')
            ->findAll();
    }
}
