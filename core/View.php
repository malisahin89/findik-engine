<?php

namespace Core;

use League\Plates\Engine;
use Core\Csrf;

class View
{
    public static function render($template, $data = [])
    {
        if (!isset($_SESSION)) session_start();

        $templates = new Engine(__DIR__ . '/../views');

        // Layout için tüm global verileri gönderiyoruz
        $templates->addData([
            'title'   => $data['title'] ?? '',
            'errors'  => $data['errors'] ?? [],
            'success' => flash('success'),
            'error'   => flash('error'),
            'user_id' => $_SESSION['user_id'] ?? null
        ]);

        // CSRF fonksiyonu
        $templates->registerFunction('csrf', function () {
            return '<input type="hidden" name="csrf_token" value="' . \Core\Csrf::generate() . '">';
        });
        
        // CSRF token fonksiyonu
        $templates->registerFunction('csrf_token', function () {
            return \Core\Csrf::generate();
        });
        
        // XSS koruması için escape fonksiyonu
        $templates->registerFunction('e', function ($string) {
            return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
        });
        
        // Güvenli URL fonksiyonu
        $templates->registerFunction('url', function ($name) {
            return htmlspecialchars(route($name), ENT_QUOTES, 'UTF-8');
        });
        
        // Old input fonksiyonu (XSS korumalı)
        $templates->registerFunction('old', function ($key, $default = '') {
            return htmlspecialchars(old($key, $default), ENT_QUOTES, 'UTF-8');
        });

        echo $templates->render($template, $data);
        unset($_SESSION['_old']);
    }
}
