<?php

namespace App\Models;

use CodeIgniter\Model;

class CampusModel extends Model
{
    protected $table = 'campus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'sigla'];
    protected $returnType = 'object';

    public function getCampiWithPrediosAndEspacos()
    {
        $predioModel = new PredioModel();
        $campi = $this->findAll();

        foreach ($campi as $campus) {
            $campus->predios = $predioModel->getPrediosWithEspacos($campus->id);
        }

        return $campi;
    }
}
