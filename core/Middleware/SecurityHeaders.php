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
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data:;");
        
        // HTTPS yönlendirme (production için)
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production' && !isset($_SERVER['HTTPS'])) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
            throw new \Core\RedirectException();
        }
        
        return $next($request);
    }
}