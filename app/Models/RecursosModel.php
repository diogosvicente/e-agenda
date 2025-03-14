<?php

namespace App\Models;

use CodeIgniter\Model;

class RecursosModel extends Model
{
    protected $table = 'recursos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_espaco', 'nome', 'quantidade', 'tipo', 'status'];
}
