<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoRecursosModel extends Model
{
    protected $table = 'evento_recursos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'id_recurso', 'quantidade'];
    protected $returnType = 'object';
}
            