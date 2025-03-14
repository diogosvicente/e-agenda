<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoEspacoDataHoraModel extends Model
{
    protected $table = 'evento_espaco_data_hora';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'id_espaco', 'data_hora_inicio', 'data_hora_fim'];
}
