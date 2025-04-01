<?php

namespace App\Controllers;

use Core\View;
use App\Models\User;

class HomeController
{
    public function index()
    {
        $users = User::all();
        View::render('home', ['users' => $users]);
    }
}
