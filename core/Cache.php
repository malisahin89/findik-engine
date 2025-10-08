<?php

namespace Core;

class Cache
{
    private static $cachePath;
    
    public static function init()
    {
        self::$cachePath = BASE_PATH . '/storage/cache';
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0755, true);
        }
    }
    
    public static function get($key, $default = null)
    {
        if (!self::$cachePath) {
            self::init();
        }
        
        $filename = self::$cachePath . '/' . md5($key) . '.cache';
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // TTL kontrolü
        if ($data['expires'] < time()) {
            unlink($filename);
            return $default;
        }
        
        return $data['value'];
    }
    
    public static function set($key, $value, $ttl = 3600)
    {
        if (!self::$cachePath) {
            self::init();
        }
        
        $filename = self::$cachePath . '/' . md5($key) . '.cache';
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($filename, serialize($data), LOCK_EX);
    }
    
    public static function remember($key, $ttl, $callback)
    {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
    
    public static function forget($key)
    {
        if (!self::$cachePath) {
            self::init();
        }
        
        $filename = self::$cachePath . '/' . md5($key) . '.cache';
        
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    public static function flush()
    {
        if (!self::$cachePath) {
            self::init();
        }
        
        $files = glob(self::$cachePath . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    public static function pull($key, $default = null)
    {
        $value = self::get($key, $default);
        self::forget($key);
        return $value;
    }
    
    public static function has($key)
    {
        return self::get($key) !== null;
    }
}