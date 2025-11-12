<?php

namespace Core\Middleware;

class SecurityHeaders
{
    public function handle($request, $next)
    {
        // XSS koruması
        header('X-XSS-Protection: 1; mode=block');

        // Content type sniffing koruması
        header('X-Content-Type-Options: nosniff');

        // Clickjacking koruması
        header('X-Frame-Options: DENY');

        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // CSP Nonce oluştur
        $nonce = base64_encode(random_bytes(16));
        $_SESSION['csp_nonce'] = $nonce;

        // Content Security Policy (Nonce ile - daha güvenli)
        // Development: unsafe-inline izinli
        // Production: Sadece nonce ile
        if (($_ENV['APP_ENV'] ?? 'local') === 'production') {
            // Production: Strict CSP (sadece nonce)
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' https://cdn.tailwindcss.com; style-src 'self' 'nonce-{$nonce}' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data:; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none';");
        } else {
            // Development: unsafe-inline izinli (geliştirme kolaylığı için)
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'nonce-{$nonce}' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data:;");
        }

        // HTTPS yönlendirme (production için)
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production' && !isset($_SERVER['HTTPS'])) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
            throw new \Core\RedirectException();
        }

        return $next($request);
    }
}