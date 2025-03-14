<?php

namespace App\Models;

use CodeIgniter\Model;

class CampusModel extends Model
{
    protected $table = 'campus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'sigla'];
}
