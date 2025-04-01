<?php

namespace Core;

class Request
{
    public static function checkCsrf()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Csrf::check($_POST['csrf_token'])) {
                die("Güvenlik uyarısı: CSRF token geçersiz!");
            }
        }
    }
}
