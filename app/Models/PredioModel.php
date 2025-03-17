<?php

namespace App\Models;

use CodeIgniter\Model;

class PredioModel extends Model
{
    protected $table = 'predio';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_campus', 'nome', 'sigla'];
    protected $returnType = 'object';

    public function getPrediosWithEspacos($id_campus)
    {
        $espacosModel = new EspacosModel();
        $predios = $this->where('id_campus', $id_campus)->orderBy('id', 'ASC')->findAll();

        foreach ($predios as $predio) {
            $predio->espacos = $espacosModel->getEspacosByPredio($predio->id);
        }

        return $predios;
    }
}
