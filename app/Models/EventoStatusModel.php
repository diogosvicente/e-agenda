<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoStatusModel extends Model
{
    protected $table = 'evento_status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'status', 'data'];
}
