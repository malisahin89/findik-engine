<?php

namespace App\Middleware;

use App\Models\User;

class AuthMiddleware
{
    public static function handle()
    {
        if (!isset($_SESSION)) session_start();

        // User existence kontrolü
        if (empty($_SESSION['user_id']) || !User::find($_SESSION['user_id'])) {
            session_destroy();
            redirect('admin.login.show');
            return;
        }

        // Session activity güncelle
        $_SESSION['last_activity'] = time();
    }
}
