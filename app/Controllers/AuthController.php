<?php

namespace App\Controllers;

use App\Models\User;
use Core\View;

class AuthController
{
    public function showLogin()
    {
        View::render('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            flash('success', 'Giriş başarılı, hoş geldin ' . $user->name . '!');
            redirect('admin.users.index');
        } else {
            flash('error', 'E-posta veya şifre hatalı!');
            redirect('admin.login.show');
        }
    }

    public function logout()
    {
        session_destroy();
        flash('success', 'Çıkış yapıldı!');
        redirect('admin.login.show');
    }
}
