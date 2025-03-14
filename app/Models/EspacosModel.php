<?php

namespace App\Models;

use CodeIgniter\Model;

class EspacosModel extends Model
{
    protected $table = 'espacos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_predio', 'nome', 'capacidade'];
}
