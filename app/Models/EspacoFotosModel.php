<?php

namespace App\Models;

use CodeIgniter\Model;

class EspacoFotosModel extends Model
{
    protected $table = 'espaco_fotos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_espaco', 'nome', 'data'];
}
