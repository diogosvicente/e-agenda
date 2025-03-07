<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function __construct()
	{
        helper(['email_helper', 'cpf_helper']);
	}

    public function index()
    {
        return view('login/login');
    }
    
    public function logout()
    {
        return redirect()->to(base_url());
    }
}
