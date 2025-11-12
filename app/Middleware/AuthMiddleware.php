<?php

namespace App\Middleware;

use App\Models\User;
use Core\Cache;

class AuthMiddleware
{
    const SESSION_TIMEOUT = 1800; // 30 dakika
    const IDLE_TIMEOUT = 900; // 15 dakika hareketsizlik

    public static function handle()
    {
        if (!isset($_SESSION)) session_start();

        // User ID kontrolü
        if (empty($_SESSION['user_id'])) {
            // Remember me cookie kontrolü
            if (isset($_COOKIE['remember_me']) && !empty($_COOKIE['remember_me'])) {
                $cookieParts = explode(':', $_COOKIE['remember_me'], 2);

                if (count($cookieParts) === 2) {
                    list($userId, $rememberToken) = $cookieParts;

                    // Token'ı hash'le ve veritabanıyla karşılaştır
                    $user = User::find($userId);

                    if ($user && $user->remember_token && hash_equals($user->remember_token, hash('sha256', $rememberToken))) {
                        // Otomatik login
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['login_time'] = time();
                        $_SESSION['last_activity'] = time();

                        \Core\Logger::info('Auto login via remember me', ['user_id' => $user->id]);

                        // Devam et, logout yapma
                        return self::continueAuthentication();
                    }
                }

                // Geçersiz cookie, temizle
                setcookie('remember_me', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
            }

            session_destroy();
            flash('error', 'Lütfen giriş yapın.');
            redirect('admin.login.show');
            return;
        }

        self::continueAuthentication();
    }

    private static function continueAuthentication()
    {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        // Session timeout kontrolü (30 dakika)
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > self::SESSION_TIMEOUT) {
            session_destroy();
            session_start();
            flash('error', 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.');
            redirect('admin.login.show');
            return;
        }

        // Idle timeout kontrolü (15 dakika hareketsizlik)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > self::IDLE_TIMEOUT) {
            session_destroy();
            session_start();
            flash('error', 'Hareketsizlik nedeniyle oturumunuz sonlandırıldı.');
            redirect('admin.login.show');
            return;
        }

        // User existence kontrolü (cache'den)
        $cacheKey = 'user_exists_' . $_SESSION['user_id'];
        $userExists = Cache::get($cacheKey);

        if ($userExists === null) {
            $userExists = User::find($_SESSION['user_id']) !== null;
            Cache::set($cacheKey, $userExists, 300); // 5 dakika cache
        }

        if (!$userExists) {
            session_destroy();
            session_start();
            flash('error', 'Kullanıcı hesabı bulunamadı.');
            redirect('admin.login.show');
            return;
        }

        // Activity güncellemesi
        $_SESSION['last_activity'] = time();

        // Login time'ı periyodik olarak güncelle (session timeout'u sıfırlamak için)
        // Her 5 dakikada bir güncellenir (gereksiz yazma işlemini önlemek için)
        if (!isset($_SESSION['login_time']) || (time() - $_SESSION['login_time']) > 300) {
            $_SESSION['login_time'] = time();
        }
    }
}
