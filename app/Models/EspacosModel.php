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
        $espacos = $this->where('id_predio', $id_predio)->findAll();

        // Ordena os espaços numericamente pelo nome
        usort($espacos, function ($a, $b) {
            return intval(preg_replace('/\D/', '', $a->nome)) <=> intval(preg_replace('/\D/', '', $b->nome));
        });

        return $espacos;
    }
}
