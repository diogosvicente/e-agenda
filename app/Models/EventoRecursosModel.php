<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoRecursosModel extends Model
{
    protected $table = 'evento_recursos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_espaco_recurso'];
}
