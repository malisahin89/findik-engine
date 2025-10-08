<?php

namespace Core;

class Logger
{
    private static $logPath;
    
    public static function init()
    {
        self::$logPath = BASE_PATH . '/storage/logs';
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
    }
    
    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }
    
    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }
    
    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }
    
    private static function log($level, $message, $context = [])
    {
        if (!self::$logPath) {
            self::init();
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] %s: %s | IP: %s | Context: %s | User-Agent: %s\n",
            $timestamp,
            $level,
            $message,
            $ip,
            json_encode($context),
            $userAgent
        );
        
        $filename = self::$logPath . '/' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
    }
}