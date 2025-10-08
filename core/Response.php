<?php

namespace Core;

class Response
{
    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        throw new \Core\RedirectException();
    }
    
    public static function redirect($url, $status = 302)
    {
        http_response_code($status);
        header("Location: $url");
        throw new \Core\RedirectException();
    }
    
    public static function notFound($message = '404 - Sayfa Bulunamadı')
    {
        http_response_code(404);
        View::render('errors/404', ['message' => $message]);
        throw new \Core\RedirectException();
    }
    
    public static function forbidden($message = '403 - Erişim Yasak')
    {
        http_response_code(403);
        View::render('errors/403', ['message' => $message]);
        throw new \Core\RedirectException();
    }
    
    public static function serverError($message = '500 - Sunucu Hatası')
    {
        http_response_code(500);
        View::render('errors/500', ['message' => $message]);
        throw new \Core\RedirectException();
    }
}