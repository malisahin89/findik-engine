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
            return '<input type="hidden" name="csrf_token" value="' . Csrf::generate() . '">';
        });

        echo $templates->render($template, $data);
        unset($_SESSION['_old']);
    }
}
