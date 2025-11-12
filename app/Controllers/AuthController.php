<?php

namespace App\Controllers;

use App\Models\User;
use Core\View;
use Core\Logger;

class AuthController
{
    public function showLogin()
    {
        View::render('auth/login');
    }

    public function login()
    {
        // Rate limiting - basit IP bazlı
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $attemptKey = 'login_attempts_' . $ip;
        
        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = ['count' => 0, 'time' => time()];
        }
        
        // 5 dakika içinde 5'ten fazla deneme varsa engelle
        if ($_SESSION[$attemptKey]['count'] >= 5 && (time() - $_SESSION[$attemptKey]['time']) < 300) {
            Logger::warning('Login rate limit exceeded', ['ip' => $ip]);
            flash('error', 'Çok fazla başarısız deneme. 5 dakika bekleyin.');
            redirect('admin.login.show');
            return;
        }
        
        // 5 dakika geçtiğinde sıfırla
        if ((time() - $_SESSION[$attemptKey]['time']) >= 300) {
            $_SESSION[$attemptKey] = ['count' => 0, 'time' => time()];
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Input validation
        if (empty($email) || empty($password)) {
            $_SESSION[$attemptKey]['count']++;
            flash('error', 'E-posta ve şifre gereklidir!');
            redirect('admin.login.show');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION[$attemptKey]['count']++;
            flash('error', 'Geçerli bir e-posta adresi girin!');
            redirect('admin.login.show');
            return;
        }

        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            // Başarılı giriş - attempt counter'ı sıfırla
            unset($_SESSION[$attemptKey]);
            
            // Session hijacking koruması
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user->id;
            $_SESSION['login_time'] = time();
            
            Logger::info('Successful login', ['user_id' => $user->id, 'email' => $email]);
            
            flash('success', 'Giriş başarılı, hoş geldin ' . htmlspecialchars($user->name) . '!');
            redirect('admin.users.index');
        } else {
            $_SESSION[$attemptKey]['count']++;
            $_SESSION[$attemptKey]['time'] = time();
            
            Logger::warning('Failed login attempt', [
                'email' => $email,
                'attempt_count' => $_SESSION[$attemptKey]['count']
            ]);
            
            flash('error', 'E-posta veya şifre hatalı!');
            redirect('admin.login.show');
        }
    }

    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? 'unknown';
        
        // Güvenli logout
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
        session_start(); // Flash mesaj için yeniden başlat
        
        Logger::info('User logout', ['user_id' => $userId]);
        
        flash('success', 'Çıkış yapıldı!');
        redirect('admin.login.show');
    }
}
