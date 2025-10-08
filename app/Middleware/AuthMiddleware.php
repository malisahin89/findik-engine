<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function handle()
    {
        if (!isset($_SESSION)) session_start();

        if (empty($_SESSION['user_id'])) {
            redirect('admin.login.show');
            return;
        }
    }
}
