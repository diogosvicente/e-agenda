<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function __construct()
	{
		helper('cpf_helper');
	}

    public function index()
    {
        return view('base/inicio');
    }
}
