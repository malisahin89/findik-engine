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
        exit;
    }
}





if (!function_exists('asset')) {
    function asset($path)
    {
        return '/public/' . ltrim($path, '/');
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

                if ($ruleName === 'unique') {
                    // unique:users,email,5 → tablo, kolon, hariç tutulacak ID
                    $parts = explode(',', $param);
                    $table = $parts[0] ?? null;
                    $column = $parts[1] ?? null;
                    $ignoreId = $parts[2] ?? null;

                    if ($value !== '' && $table && $column) {
                        $query = \Illuminate\Database\Capsule\Manager::table($table)
                            ->where($column, $value);

                        if ($ignoreId) {
                            $query->where('id', '!=', $ignoreId);
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
    function old($key, $default = '')
    {
        if (!isset($_SESSION)) session_start();
        $old = $_SESSION['_old'] ?? [];

        return $old[$key] ?? $default;
    }
}

