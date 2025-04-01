<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/helpers.php';

use Core\Middleware;
use Core\Route;

session_start();

Middleware::handle();

require_once __DIR__ . '/../routes/web.php';

Route::dispatch();
