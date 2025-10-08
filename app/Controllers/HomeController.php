<?php

namespace App\Controllers;

use Core\View;
use App\Models\User;
use Core\Cache;

class HomeController
{
    public function index()
    {
        // Laravel tarzında cache remember
        $users = Cache::remember('home_users_list', 300, function() {
            return User::where('status', 'active')->limit(10)->get();
        });
        
        View::render('home', ['users' => $users]);
    }
}
