<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;

class Database
{
    private static $instance = null;
    private static $connections = [];
    private static $config = [];
    private static $maxConnections = 10;
    private static $activeConnections = 0;
    
    public static function init($config)
    {
        self::$config = $config;
        
        if (self::$instance === null) {
            self::$instance = new Capsule;
            
            // Ana bağlantı
            self::$instance->addConnection([
                'driver' => 'mysql',
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => $config['charset'],
                'collation' => $config['collation'],
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['collation']}"
                ]
            ]);
            
            self::$instance->setAsGlobal();
            self::$instance->bootEloquent();
        }
        
        return self::$instance;
    }
    
    public static function getConnection($name = 'default')
    {
        if (isset(self::$connections[$name])) {
            return self::$connections[$name];
        }
        
        if (self::$activeConnections >= self::$maxConnections) {
            // Connection pool dolu, mevcut bağlantıyı kullan
            return self::$instance->getConnection();
        }
        
        // Yeni bağlantı oluştur
        $connection = new PDO(
            "mysql:host=" . self::$config['host'] . ";port=" . self::$config['port'] . ";dbname=" . self::$config['database'] . ";charset=" . self::$config['charset'],
            self::$config['username'],
            self::$config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true, // Persistent connection
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::$config['charset']
            ]
        );
        
        self::$connections[$name] = $connection;
        self::$activeConnections++;
        
        return $connection;
    }
    
    public static function closeConnection($name)
    {
        if (isset(self::$connections[$name])) {
            unset(self::$connections[$name]);
            self::$activeConnections--;
        }
    }
    
    public static function closeAllConnections()
    {
        self::$connections = [];
        self::$activeConnections = 0;
    }
    
    public static function getActiveConnectionCount()
    {
        return self::$activeConnections;
    }
    
    public static function query($sql, $params = [])
    {
        $connection = self::getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function select($sql, $params = [])
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function selectOne($sql, $params = [])
    {
        return self::query($sql, $params)->fetch();
    }
    
    public static function insert($sql, $params = [])
    {
        self::query($sql, $params);
        return self::getConnection()->lastInsertId();
    }
    
    public static function update($sql, $params = [])
    {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function delete($sql, $params = [])
    {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function transaction($callback)
    {
        $connection = self::getConnection();
        
        try {
            $connection->beginTransaction();
            $result = $callback();
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}