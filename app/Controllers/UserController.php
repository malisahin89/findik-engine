<?php

namespace App\Controllers;

use App\Models\User;
use Core\View;
use Core\Csrf;

class UserController
{
    public function index()
    {
        $users = User::all();
        View::render('users/index', ['users' => $users]);
    }

    public function create()
    {
        View::render('users/create');
    }

    public function store()
    {
        if (!Csrf::check($_POST['csrf_token'])) {
            die('CSRF koruması: Token doğrulanamadı.');
        }

        $errors = validate($_POST, [
            'name' => 'required',
            'surname' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            // 'profile_image' => 'required',
            'bio' => 'required',
            'status' => 'required',
        ]);

        if ($errors) {
            View::render('users/create', ['errors' => $errors]);
            return;
        }

        User::create($_POST);
        flash('success', 'Kullanıcı başarıyla eklendi!');
        redirect('admin.users.index');
    }

    public function edit()
    {
        $user = User::find($_GET['id']);
        View::render('users/edit', ['user' => $user]);
    }

    public function update()
    {
        if (!Csrf::check($_POST['csrf_token'])) {
            die('CSRF koruması: Token doğrulanamadı.');
        }
    
        $userId = $_POST['id'];
    
        $errors = validate($_POST, [
            'name'     => 'required',
            'surname'  => 'required',
            'username' => 'required|unique:users,username,' . $userId,
            'email'    => 'required|email|unique:users,email,' . $userId,
            'bio'      => 'required',
            'status'   => 'required',
        ]);
    
        if ($errors) {
            $user = User::find($userId);
            View::render('users/edit', [
                'errors' => $errors,
                'user'   => $user
            ]);
            return;
        }
    
        $data = $_POST;
    
        // Şifre varsa hashle, yoksa sil
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
    
        $user = User::find($userId);
        $user->update($data);
    
        flash('success', 'Kullanıcı başarıyla güncellendi!');
        redirect('admin.users.index');
    }
    

    public function delete()
    {
        User::destroy($_GET['id']);
        flash('success', 'Kullanıcı başarıyla silindi!');
        redirect('admin.users.index');
    }
}
