<?php

namespace Core;

class FileUpload
{
    private static $allowedTypes = [
        'image/jpeg',
        'image/png', 
        'image/gif',
        'image/webp'
    ];
    
    private static $maxSize = 2097152; // 2MB
    
    public static function upload($file, $directory = 'uploads')
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('Geçersiz dosya yükleme.');
        }
        
        // Dosya boyutu kontrolü
        if ($file['size'] > self::$maxSize) {
            throw new \Exception('Dosya boyutu çok büyük. Maksimum 2MB.');
        }
        
        // MIME type kontrolü
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::$allowedTypes)) {
            throw new \Exception('Desteklenmeyen dosya türü.');
        }
        
        // Güvenli dosya adı oluştur
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        
        // Upload dizini oluştur
        $uploadPath = BASE_PATH . '/public/' . $directory;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $destination = $uploadPath . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Dosya yükleme başarısız.');
        }
        
        return $directory . '/' . $filename;
    }
}