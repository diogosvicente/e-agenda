<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusDefinicaoModel extends Model
{
    protected $table = 'status_definicao';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'descricao', 'ordem'];
    protected $returnType = 'object';

    /**
     * Retorna todos os status, ordenados pela coluna "ordem".
     *
     * @return array
     */
    public function getAllOrdered()
    {
        return $this->orderBy('ordem', 'ASC')->findAll();
    }
}
