<?php


namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function dashboard()
    {
        var_dump(session()->get()); exit;
        return view('admin_dashboard');
    }
}