<?php

use Core\Route;

if (!function_exists('route')) {
    function route($name)
    {
        return Route::url($name);
    }
}

if (!function_exists('redirect')) {
    function redirect($urlOrName)
    {
        // POST verisi varsa sakla
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['_old'] = $_POST;
        }

        // Route helper fonksiyonu ile URL çek
        $url = Route::url($urlOrName);

        if ($url === '#') {
            $url = $urlOrName;
        }

        header("Location: $url");
        throw new \Core\RedirectException();
    }
}





if (!function_exists('asset')) {
    function asset($path)
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('flash')) {
    function flash($key, $message = null)
    {
        if (!isset($_SESSION))
            session_start();

        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return;
        }

        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $value;
        }

        return null;
    }
}

if (!function_exists('validate')) {
    function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = trim($data[$field] ?? '');
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $param = $ruleParts[1] ?? null;

                if ($ruleName === 'required' && $value === '') {
                    $errors[$field][] = "$field alanı zorunludur.";
                }

                if ($ruleName === 'min' && strlen($value) < (int) $param) {
                    $errors[$field][] = "$field en az $param karakter olmalı.";
                }

                if ($ruleName === 'max' && strlen($value) > (int) $param) {
                    $errors[$field][] = "$field en fazla $param karakter olabilir.";
                }

                if ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "$field geçerli bir e-posta olmalı.";
                }

                if ($ruleName === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = "$field sadece rakam içermeli.";
                }

                if ($ruleName === 'same' && $value !== ($data[$param] ?? '')) {
                    $errors[$field][] = "$field ile $param eşleşmiyor.";
                }
                
                if ($ruleName === 'in') {
                    $allowedValues = explode(',', $param);
                    if (!in_array($value, $allowedValues)) {
                        $errors[$field][] = "$field geçersiz bir değer içeriyor.";
                    }
                }
                
                if ($ruleName === 'alpha' && !preg_match('/^[a-zA-ZÇçĞğİıÖöŞşÜü\s]+$/', $value)) {
                    $errors[$field][] = "$field sadece harf içermelidir.";
                }
                
                if ($ruleName === 'alphanumeric' && !preg_match('/^[a-zA-Z0-9ÇçĞğİıÖöŞşÜü]+$/', $value)) {
                    $errors[$field][] = "$field sadece harf ve rakam içermelidir.";
                }

                if ($ruleName === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$field][] = "$field geçerli bir URL olmalı.";
                }
                
                if ($ruleName === 'unique') {
                    // unique:users,email,5 → tablo, kolon, hariç tutulacak ID
                    $parts = explode(',', $param);
                    $table = $parts[0] ?? null;
                    $column = $parts[1] ?? null;
                    $ignoreId = $parts[2] ?? null;

                    // Güvenlik: Sadece izin verilen tablo ve kolon adlarını kabul et
                    $allowedTables = ['users', 'posts'];
                    $allowedColumns = ['username', 'email', 'slug'];

                    if ($value !== '' && $table && $column &&
                        in_array($table, $allowedTables) &&
                        in_array($column, $allowedColumns)) {

                        $query = \Illuminate\Database\Capsule\Manager::table($table)
                            ->where($column, $value);

                        if ($ignoreId && is_numeric($ignoreId)) {
                            $query->where('id', '!=', (int)$ignoreId);
                        }

                        $count = $query->count();

                        if ($count > 0) {
                            $errors[$field][] = "$field zaten kullanılmış.";
                        }
                    }
                }
            }
        }

        return !empty($errors) ? $errors : null;
    }
}


if (!function_exists('auth')) {
    function auth()
    {
        return \App\Models\User::find($_SESSION['user_id'] ?? 0);
    }
}


if (!function_exists('old')) {
    function old($key, $default = '', $escape = true)
    {
        if (!isset($_SESSION)) session_start();
        $old = $_SESSION['_old'] ?? [];
        $value = $old[$key] ?? $default;

        // XSS koruması: HTML encoding (default olarak açık)
        return $escape ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
    }
}

if (!function_exists('csrf')) {
    function csrf()
    {
        return \Core\Csrf::generate();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return \Core\Csrf::generate();
    }
}

if (!function_exists('cache')) {
    function cache($key = null, $value = null, $ttl = 3600)
    {
        if ($key === null) {
            return new \Core\Cache();
        }

        if ($value === null) {
            return \Core\Cache::get($key);
        }

        return \Core\Cache::set($key, $value, $ttl);
    }
}

if (!function_exists('getRealIP')) {
    /**
     * Gerçek IP adresini güvenli bir şekilde alır
     * Proxy/load balancer arkasında bile doğru IP'yi döndürür
     */
    function getRealIP()
    {
        // Güvenilir proxy'ler listesi (deployment'a göre ayarlayın)
        $trustedProxies = ['127.0.0.1', '::1'];

        // Proxy arkasındaysak ve proxy güvenilirse X-Forwarded-For kullan
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
            in_array($_SERVER['REMOTE_ADDR'], $trustedProxies)) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $realIP = trim($ips[0]);

            // IP validasyonu
            if (filter_var($realIP, FILTER_VALIDATE_IP)) {
                return $realIP;
            }
        }

        // Cloudflare kullanılıyorsa
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $cfIP = $_SERVER['HTTP_CF_CONNECTING_IP'];
            if (filter_var($cfIP, FILTER_VALIDATE_IP)) {
                return $cfIP;
            }
        }

        // Normal durum - direkt IP
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

