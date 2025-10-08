<?php

namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\View;
use Core\Logger;
use Core\Cache;
use Core\FileUpload;

class UserController
{
    public function index()
    {
        // Laravel tarzında cache remember
        $users = Cache::remember('users_list', 300, function() {
            return User::all();
        });
        
        View::render('users/index', ['users' => $users]);
    }

    public function create()
    {
        View::render('users/create');
    }

    public function store()
    {
        // Güvenlik: Sadece izin verilen alanları al
        $allowedFields = ['name', 'surname', 'username', 'email', 'password', 'bio', 'status', 'profile_image'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));
        
        // Input sanitization
        $request = array_map('trim', $request);
        $request = array_map('htmlspecialchars', $request);
        
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
        // Password User model'deki mutator ile otomatik hash'leniyor
        
        // Resim yükleme
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $userData['profile_image'] = FileUpload::upload($_FILES['profile_image'], 'uploads/profiles');
            } catch (\Exception $e) {
                View::render('users/create', [
                    'errors' => ['profile_image' => [$e->getMessage()]],
                    'old' => $request
                ]);
                return;
            }
        } else {
            $userData['profile_image'] = 'default.png';
        }
        
        $newUser = User::create($userData);
        
        // Cache'i temizle
        Cache::forget('users_list');
        Cache::forget('home_users_list');
        
        Logger::info('User created', [
            'new_user_id' => $newUser->id,
            'created_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        flash('success', 'Kullanıcı başarıyla oluşturuldu!');
        redirect('admin.users.index');
    }

    public function edit()
    {
        $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            flash('error', 'Geçersiz kullanıcı ID!');
            redirect('admin.users.index');
            return;
        }
        
        $user = User::find($id);
        if (!$user) {
            flash('error', 'Kullanıcı bulunamadı!');
            redirect('admin.users.index');
            return;
        }
        
        View::render('users/edit', ['user' => $user]);
    }

    public function update()
    {
        $userId = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$userId) {
            flash('error', 'Geçersiz kullanıcı ID!');
            redirect('admin.users.index');
            return;
        }
        
        // Güvenlik: Sadece izin verilen alanları al
        $allowedFields = ['name', 'surname', 'username', 'email', 'password', 'bio', 'status'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));
        $request['id'] = $userId;
        
        // Input sanitization
        $request = array_map('trim', $request);
        $request = array_map('htmlspecialchars', $request);
        
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
        unset($data['id']); // ID'yi data'dan çıkar

        // Resim yükleme
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $data['profile_image'] = FileUpload::upload($_FILES['profile_image'], 'uploads/profiles');
            } catch (\Exception $e) {
                $user = User::find($userId);
                View::render('users/edit', [
                    'errors' => ['profile_image' => [$e->getMessage()]],
                    'user' => (object) array_merge((array) $user->toArray(), $request)
                ]);
                return;
            }
        }

        // Şifre boşsa sil (User model'de otomatik hash'leniyor)
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user = User::find($userId);
        if (!$user) {
            flash('error', 'Kullanıcı bulunamadı!');
            redirect('admin.users.index');
            return;
        }
        
        $user->update($data);
        
        // Cache'i temizle
        Cache::forget('users_list');
        Cache::forget('home_users_list');
        
        Logger::info('User updated', [
            'updated_user_id' => $userId,
            'updated_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        flash('success', 'Kullanıcı başarıyla güncellendi!');
        redirect('admin.users.index');
    }

    public function delete()
    {
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            flash('error', 'Geçersiz kullanıcı ID!');
            redirect('admin.users.index');
            return;
        }
        
        $user = User::find($id);
        if (!$user) {
            flash('error', 'Kullanıcı bulunamadı!');
            redirect('admin.users.index');
            return;
        }
        
        User::destroy($id);
        
        // Cache'i temizle
        Cache::forget('users_list');
        Cache::forget('home_users_list');
        
        Logger::warning('User deleted', [
            'deleted_user_id' => $id,
            'deleted_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);
        
        flash('success', 'Kullanıcı başarıyla silindi!');
        redirect('admin.users.index');
    }
}
