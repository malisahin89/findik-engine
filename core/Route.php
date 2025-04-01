<?php

namespace Core;

class Route
{
    protected static $routes = [];
    protected static $prefixStack = [];
    protected static $namedRoutes = [];

    // GET route tanımı
    public static function get($uri, $action)
    {
        $prefix = implode('', self::$prefixStack);
        $fullUri = rtrim($prefix . '/' . ltrim($uri, '/'), '/');
        self::$routes['GET'][$fullUri] = [
            'action' => $action,
            'middleware' => [],
            'name' => null,
        ];
        return new static($fullUri, 'GET');
    }

    // POST route tanımı
    public static function post($uri, $action)
    {
        $prefix = implode('', self::$prefixStack);
        $fullUri = rtrim($prefix . '/' . ltrim($uri, '/'), '/');
        self::$routes['POST'][$fullUri] = [
            'action' => $action,
            'middleware' => [],
            'name' => null,
        ];
        return new static($fullUri, 'POST');
    }

    // Prefix desteği
    public static function prefix($prefix)
    {
        self::$prefixStack[] = rtrim($prefix, '/');
        return new static(null, null);
    }

    public function group($callback)
    {
        $callback();
        array_pop(self::$prefixStack); // prefix stack'ten çıkart
        return $this;
    }

    // Route name tanımlama
    public function name($routeName)
    {
        if ($this->method && $this->uri) {
            self::$routes[$this->method][$this->uri]['name'] = $routeName;
            self::$namedRoutes[$routeName] = $this->uri;
        }
        return $this;
    }

    // Middleware tanımlama
    protected $uri;
    protected $method;

    public function __construct($uri, $method)
    {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function middleware($middlewareName)
    {
        if ($this->method && $this->uri) {
            self::$routes[$this->method][$this->uri]['middleware'][] = $middlewareName;
        }
        return $this;
    }

    // İsimle URL üretme
    public static function url($name)
    {
        return self::$namedRoutes[$name] ?? '#';
    }

    // Rotaları çalıştır
    public static function dispatch()
    {
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method][$uri])) {
            http_response_code(404);
            echo "404 - Not Found: $uri";
            return;
        }

        $route = self::$routes[$method][$uri];

        // Middleware'leri çalıştır
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareClass = "\\App\\Middleware\\" . ucfirst($middleware) . "Middleware";
                if (class_exists($middlewareClass)) {
                    $middlewareClass::handle();
                } else {
                    die("Middleware bulunamadı: $middlewareClass");
                }
            }
        }

        [$controller, $methodName] = explode('@', $route['action']);
        $controller = "App\\Controllers\\$controller";

        if (!class_exists($controller) || !method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Controller veya metot bulunamadı!";
            return;
        }

        call_user_func([new $controller, $methodName]);
    }
}
