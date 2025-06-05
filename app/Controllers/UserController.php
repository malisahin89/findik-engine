<?php

namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\View;

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
        $request = $_POST;
        
        // Eğer status 'passive' olarak gelirse, 'inactive' olarak değiştir
        if (isset($request['status']) && $request['status'] === 'passive') {
            $request['status'] = 'inactive';
        }
        
        $errors = validate($request, [
            'name' => 'required',
            'surname' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            // 'profile_image' => 'required',
            'bio' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($errors) {
            View::render('users/create', [
                'errors' => $errors,
                'old' => $request
            ]);
            return;
        }

        $userData = $request;
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Varsayılan resim yolu
        if (empty($userData['profile_image'])) {
            $userData['profile_image'] = 'default.png';
        }
        
        User::create($userData);

        flash('success', 'Kullanıcı başarıyla oluşturuldu!');
        redirect('admin.users.index');
    }

    public function edit()
    {
        $user = User::find($_GET['id']);
        View::render('users/edit', ['user' => $user]);
    }

    public function update()
    {
        $request = $_POST;
        $userId = $request['id'];
        
        // Eğer status 'passive' olarak gelirse, 'inactive' olarak değiştir
        if (isset($request['status']) && $request['status'] === 'passive') {
            $request['status'] = 'inactive';
        }

        $errors = validate($request, [
            'name' => 'required',
            'surname' => 'required',
            'username' => 'required|unique:users,username,' . $userId,
            'email' => 'required|email|unique:users,email,' . $userId,
            'bio' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($errors) {
            $user = User::find($userId);
            View::render('users/edit', [
                'errors' => $errors,
                'user' => (object) array_merge((array) $user->toArray(), $request)
            ]);
            return;
        }


        $data = $request;

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
