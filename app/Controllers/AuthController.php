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
        // Rate limiting - IP + Session kombinasyonu (daha güvenli)
        $ip = getRealIP();
        $sessionId = session_id();
        $attemptKey = 'login_attempts_' . md5($ip . '_' . $sessionId);
        
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

            // Remember me özelliği
            $rememberMe = isset($_POST['remember-me']) && $_POST['remember-me'] === 'on';
            if ($rememberMe) {
                // Güvenli random token oluştur (64 karakter)
                $rememberToken = bin2hex(random_bytes(32));

                // Token'ı veritabanına kaydet
                $user->remember_token = hash('sha256', $rememberToken);
                $user->save();

                // Cookie oluştur (30 gün)
                $cookieValue = $user->id . ':' . $rememberToken;
                setcookie(
                    'remember_me',
                    $cookieValue,
                    time() + (30 * 24 * 60 * 60), // 30 gün
                    '/',
                    '',
                    isset($_SERVER['HTTPS']), // Secure flag
                    true // HttpOnly flag
                );

                Logger::info('Remember me token created', ['user_id' => $user->id]);
            }

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

        // Remember me token'ı temizle
        if ($userId !== 'unknown') {
            $user = User::find($userId);
            if ($user && $user->remember_token) {
                $user->remember_token = null;
                $user->save();
            }
        }

        // Remember me cookie'yi temizle
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
        }

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

    public function showForgotPassword()
    {
        View::render('auth/forgot-password');
    }

    public function sendResetLink()
    {
        // Rate limiting - IP + Email kombinasyonu
        $ip = getRealIP();
        $email = trim($_POST['email'] ?? '');

        // IP bazlı rate limiting (tüm istekler için)
        $ipAttemptKey = 'reset_attempts_ip_' . md5($ip);

        if (!isset($_SESSION[$ipAttemptKey])) {
            $_SESSION[$ipAttemptKey] = ['count' => 0, 'time' => time()];
        }

        // IP başına 5 dakikada 5 istek
        if ($_SESSION[$ipAttemptKey]['count'] >= 5 && (time() - $_SESSION[$ipAttemptKey]['time']) < 300) {
            Logger::warning('Password reset rate limit exceeded (IP)', ['ip' => $ip]);
            flash('error', 'Çok fazla istek gönderdiniz. 5 dakika bekleyin.');
            redirect('admin.password.forgot');
            return;
        }

        // 5 dakika geçtiğinde sıfırla
        if ((time() - $_SESSION[$ipAttemptKey]['time']) >= 300) {
            $_SESSION[$ipAttemptKey] = ['count' => 0, 'time' => time()];
        }

        // Email validasyonu
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION[$ipAttemptKey]['count']++;
            flash('error', 'Geçerli bir e-posta adresi girin!');
            redirect('admin.password.forgot');
            return;
        }

        // Email bazlı rate limiting (aynı email için)
        $emailAttemptKey = 'reset_attempts_email_' . md5($email);

        if (!isset($_SESSION[$emailAttemptKey])) {
            $_SESSION[$emailAttemptKey] = ['count' => 0, 'time' => time()];
        }

        // Email başına 5 dakikada 3 istek
        if ($_SESSION[$emailAttemptKey]['count'] >= 3 && (time() - $_SESSION[$emailAttemptKey]['time']) < 300) {
            Logger::warning('Password reset rate limit exceeded (Email)', ['email' => $email, 'ip' => $ip]);
            flash('error', 'Bu e-posta için çok fazla istek gönderildi. 5 dakika bekleyin.');
            redirect('admin.password.forgot');
            return;
        }

        // 5 dakika geçtiğinde sıfırla
        if ((time() - $_SESSION[$emailAttemptKey]['time']) >= 300) {
            $_SESSION[$emailAttemptKey] = ['count' => 0, 'time' => time()];
        }

        // Counter'ları artır
        $_SESSION[$ipAttemptKey]['count']++;
        $_SESSION[$emailAttemptKey]['count']++;

        // Kullanıcıyı bul
        $user = User::where('email', $email)->first();

        // Güvenlik: Her zaman başarılı mesajı göster (email enumeration önleme)
        if (!$user) {
            flash('success', 'Eğer bu e-posta kayıtlıysa, şifre sıfırlama linki gönderildi.');
            redirect('admin.password.forgot');
            return;
        }

        // Güvenli token oluştur (64 karakter)
        $token = bin2hex(random_bytes(32));

        // Token'ı hash'le ve kaydet (1 saat geçerli)
        $user->reset_token = hash('sha256', $token);
        $user->reset_token_expires_at = date('Y-m-d H:i:s', time() + 3600);
        $user->save();

        // Reset URL oluştur
        $resetUrl = ($_ENV['APP_URL'] ?? 'http://findik-engine.test') . route('admin.password.reset') . '?token=' . $token . '&email=' . urlencode($email);

        // Email gönder
        $subject = 'Şifre Sıfırlama - FindikEngine';
        $message = "Merhaba,\n\n";
        $message .= "Şifrenizi sıfırlamak için aşağıdaki linke tıklayın:\n\n";
        $message .= $resetUrl . "\n\n";
        $message .= "Bu link 1 saat geçerlidir.\n\n";
        $message .= "Eğer bu isteği siz yapmadıysanız, bu e-postayı görmezden gelin.\n\n";
        $message .= "İyi günler,\nFindikEngine Ekibi";

        $headers = "From: noreply@findik-engine.test\r\n";
        $headers .= "Reply-To: noreply@findik-engine.test\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $mailSent = mail($email, $subject, $message, $headers);

        if ($mailSent) {
            Logger::info('Password reset email sent', ['email' => $email]);
        } else {
            Logger::error('Password reset email failed', ['email' => $email]);
        }

        flash('success', 'Eğer bu e-posta kayıtlıysa, şifre sıfırlama linki gönderildi.');
        redirect('admin.password.forgot');
    }

    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if (empty($token) || empty($email)) {
            flash('error', 'Geçersiz şifre sıfırlama linki!');
            redirect('admin.login.show');
            return;
        }

        View::render('auth/reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        // Validasyon
        if (empty($password) || empty($passwordConfirm)) {
            flash('error', 'Tüm alanları doldurun!');
            redirect('admin.password.reset?token=' . urlencode($token) . '&email=' . urlencode($email));
            return;
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Şifreler eşleşmiyor!');
            redirect('admin.password.reset?token=' . urlencode($token) . '&email=' . urlencode($email));
            return;
        }

        if (strlen($password) < 8) {
            flash('error', 'Şifre en az 8 karakter olmalıdır!');
            redirect('admin.password.reset?token=' . urlencode($token) . '&email=' . urlencode($email));
            return;
        }

        // Kullanıcıyı bul
        $user = User::where('email', $email)->first();

        if (!$user) {
            flash('error', 'Kullanıcı bulunamadı!');
            redirect('admin.login.show');
            return;
        }

        // Token doğrula
        if (!$user->reset_token || !hash_equals($user->reset_token, hash('sha256', $token))) {
            flash('error', 'Geçersiz veya süresi dolmuş token!');
            redirect('admin.login.show');
            return;
        }

        // Token süresi kontrol et
        if (strtotime($user->reset_token_expires_at) < time()) {
            flash('error', 'Token süresi dolmuş! Yeni şifre sıfırlama linki isteyin.');
            redirect('admin.password.forgot');
            return;
        }

        // Şifreyi güncelle
        $user->password = $password; // Mutator otomatik hash'leyecek
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();

        Logger::info('Password reset successful', ['email' => $email]);

        flash('success', 'Şifreniz başarıyla değiştirildi! Giriş yapabilirsiniz.');
        redirect('admin.login.show');
    }
}
