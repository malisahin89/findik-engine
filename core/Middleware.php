<?php

namespace Core;

class Middleware
{
    public static function handle()
    {
        Request::checkCsrf();
    }
}
