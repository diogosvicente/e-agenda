<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoEspacoDataHoraModel extends Model
{
    protected $table = 'evento_espaco_data_hora';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'id_predio', 'id_espaco', 'data_hora_inicio', 'data_hora_fim'];
    protected $returnType = 'object';

    /**
     * Verifica se há conflito de horário para o espaço ou prédio informado.
     *
     * @param mixed $id_espaco Pode ser um valor ou null
     * @param mixed $id_predio Pode ser um valor ou null
     * @param string $data_hora_inicio
     * @param string $data_hora_fim
     * @return bool
     */
    public function isConflict($id_espaco, $id_predio, $data_hora_inicio, $data_hora_fim)
    {
        $builder = $this->builder();

        if (!is_null($id_espaco)) {
            $builder->where('id_espaco', $id_espaco);
        } elseif (!is_null($id_predio)) {
            $builder->where('id_predio', $id_predio);
        }

        $builder->where('data_hora_inicio <', $data_hora_fim);
        $builder->where('data_hora_fim >', $data_hora_inicio);
        
        // Se existir pelo menos um registro, significa que há conflito
        return $builder->countAllResults() > 0;
    }
}
