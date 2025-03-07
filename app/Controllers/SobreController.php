<?php

namespace App\Controllers;

class SobreController extends BaseController
{
    public function __construct()
	{
		helper('cpf_helper');
	}

    public function index()
    {
        return view('base/sobre');
    }
}