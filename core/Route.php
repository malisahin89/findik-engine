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

    // Middleware çalıştır
    protected static function runMiddleware($middlewareName, $request = null)
    {
        // Önce app/Middleware'de ara
        $middlewareClass = "\\App\\Middleware\\" . ucfirst($middlewareName) . "Middleware";
        
        // Bulunamazsa core/Middleware'de ara
        if (!class_exists($middlewareClass)) {
            $middlewareClass = "\\Core\\Middleware\\" . ucfirst($middlewareName);
        }
        
        if (class_exists($middlewareClass)) {
            $middleware = new $middlewareClass();
            return $middleware->handle($request, function($request) { return $request; });
        }
        
        throw new \Exception("Middleware bulunamadı: $middlewareName");
    }

    // Rotaları çalıştır
    public static function dispatch()
    {
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method][$uri])) {
            \Core\Response::notFound();
            return;
        }

        $route = self::$routes[$method][$uri];
        $request = $_REQUEST;

        // Global middleware'ler (tüm isteklerde çalışır)
        $globalMiddlewares = ['SecurityHeaders', 'VerifyCsrfToken'];
        
        // Global middleware'leri çalıştır
        foreach ($globalMiddlewares as $middleware) {
            self::runMiddleware($middleware, $request);
        }

        // Rota özel middleware'leri çalıştır
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                self::runMiddleware($middleware, $request);
            }
        }

        [$controller, $methodName] = explode('@', $route['action']);
        
        // Güvenlik: Controller ve method adlarını doğrula
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $controller) || 
            !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $methodName)) {
            \Core\Response::serverError('Geçersiz controller veya metot adı!');
            return;
        }
        
        $controller = "App\\Controllers\\$controller";

        if (!class_exists($controller) || !method_exists($controller, $methodName)) {
            \Core\Response::serverError('Controller veya metot bulunamadı!');
            return;
        }

        call_user_func([new $controller, $methodName]);
    }
}
