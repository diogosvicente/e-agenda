<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoEspacoDataHoraModel extends Model
{
    protected $table = 'evento_espaco_data_hora';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'id_espaco', 'data_hora_inicio', 'data_hora_fim'];

    public function isConflict($id_espaco, $data_hora_inicio, $data_hora_fim)
    {
        $builder = $this->builder();
        $builder->where('id_espaco', $id_espaco);
        // Verifica se o horário de início do novo evento é anterior ao término de algum evento existente
        // e se o horário de término do novo evento é posterior ao início de algum evento existente.
        $builder->where('data_hora_inicio <', $data_hora_fim);
        $builder->where('data_hora_fim >', $data_hora_inicio);
        
        // Se existir pelo menos um registro, significa que há conflito
        return $builder->countAllResults() > 0;
    }

}
