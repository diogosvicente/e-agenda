<?php

namespace App\Models;

use CodeIgniter\Model;

class PredioModel extends Model
{
    protected $table = 'predio';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_campus', 'nome', 'sigla'];
}
