<?php

namespace App\Models;

use CodeIgniter\Model;

class EspacosModel extends Model
{
    protected $table = 'espacos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_predio', 'nome', 'capacidade'];
    protected $returnType = 'object';

    public function getEspacosByPredio($id_predio)
    {
        return $this->where('id_predio', $id_predio)->findAll();
    }
}
