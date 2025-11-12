<?php

// Güvenlik: Temel dizini tanımla
define('BASE_PATH', realpath(__DIR__ . '/../'));

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/helpers.php';

use Core\Middleware;
use Core\Route;
use Core\Logger;

// Logger'ı başlat
Logger::init();

// Güvenli session ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Session timeout (30 dakika)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
    session_destroy();
    session_start();
}

try {
    Middleware::handle();

    require_once BASE_PATH . '/routes/web.php';

    Route::dispatch();

    // Flash message ve old input auto-cleanup
    if (isset($_SESSION['_old'])) {
        unset($_SESSION['_old']);
    }

} catch (\Core\RedirectException $e) {
    // Redirect exception - normal akış
    exit;
} catch (\Core\CsrfException $e) {
    http_response_code(419);
    Logger::warning('CSRF token mismatch', [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
    ]);
    echo 'CSRF Hatası: ' . htmlspecialchars($e->getMessage());
} catch (\Exception $e) {
    http_response_code(500);
    Logger::error('Application error: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    // Güvenlik: Production'da hata detaylarını gösterme
    if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
        echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        echo 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
    }
}
