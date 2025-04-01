<?php

namespace Core;

class Redirect
{
    public static function to($url)
    {
        header("Location: $url");
        exit;
    }

    public static function route($name)
    {
        $url = \Core\Route::url($name);
        self::to($url);
    }
}
