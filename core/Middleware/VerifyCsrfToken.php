<?php

namespace Core\Middleware;

use Core\Csrf;

class VerifyCsrfToken
{
    public function handle($request, $next)
    {
        // Sadece POST, PUT, PATCH, DELETE gibi değişiklik yapılan metodlarda kontrol yap
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH', 'DELETE'])) {

            // CSRF token kontrolü (TÜM POST isteklerinde zorunlu)
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!Csrf::check($token)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    http_response_code(419);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'CSRF token mismatch']);
                    throw new \Core\CsrfException('CSRF token mismatch');
                }

                throw new \Core\CsrfException('CSRF koruması: Token doğrulanamadı.');
            }
        }

        return $next($request);
    }
}
