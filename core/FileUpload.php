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
    private static $quality = 70; // WebP kalitesi (0-100)

    public static function upload($file, $directory = 'uploads', $convertToWebP = true, $customFilename = null)
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

        // Upload dizini oluştur
        $uploadPath = BASE_PATH . '/public/' . $directory;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Güvenli dosya adı oluştur
        $filename = $customFilename ? self::sanitizeFilename($customFilename) : uniqid();
        $tempDestination = $uploadPath . '/' . $filename . '_temp';

        if (!move_uploaded_file($file['tmp_name'], $tempDestination)) {
            throw new \Exception('Dosya yükleme başarısız.');
        }

        // WebP'ye dönüştür
        if ($convertToWebP && function_exists('imagewebp')) {
            try {
                $webpFilename = $filename . '.webp';
                $webpPath = $uploadPath . '/' . $webpFilename;

                // Resmi yükle
                $image = self::createImageFromFile($tempDestination, $mimeType);

                if ($image === false) {
                    // Dönüştürme başarısız, orijinal dosyayı kullan
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $finalFilename = $filename . '.' . $extension;
                    $finalPath = $uploadPath . '/' . $finalFilename;
                    rename($tempDestination, $finalPath);
                    return $directory . '/' . $finalFilename;
                }

                // WebP formatında kaydet
                imagewebp($image, $webpPath, self::$quality);
                imagedestroy($image);

                // Geçici dosyayı sil
                unlink($tempDestination);

                return $directory . '/' . $webpFilename;

            } catch (\Exception $e) {
                // Hata durumunda geçici dosyayı temizle
                if (file_exists($tempDestination)) {
                    unlink($tempDestination);
                }
                throw new \Exception('WebP dönüştürme başarısız: ' . $e->getMessage());
            }
        } else {
            // WebP desteği yok, orijinal formatı kullan
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $finalFilename = $filename . '.' . $extension;
            $finalPath = $uploadPath . '/' . $finalFilename;
            rename($tempDestination, $finalPath);
            return $directory . '/' . $finalFilename;
        }
    }

    /**
     * MIME type'a göre GD image resource oluşturur
     */
    private static function createImageFromFile($filePath, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                $image = imagecreatefrompng($filePath);
                // PNG şeffaflığını koru
                imagealphablending($image, false);
                imagesavealpha($image, true);
                return $image;
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }

    /**
     * Dosya adını güvenli hale getirir
     * @param string $filename Dosya adı
     * @return string Güvenli dosya adı
     */
    private static function sanitizeFilename($filename)
    {
        // Türkçe karakterleri dönüştür
        $turkish = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
        $english = ['s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'];
        $filename = str_replace($turkish, $english, $filename);

        // Sadece alfanumerik karakterler, tire ve alt çizgi kalsın
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $filename);

        // Boşsa uniqid kullan
        if (empty($filename)) {
            $filename = uniqid();
        }

        return $filename;
    }

    /**
     * Dosyayı yeniden adlandırır
     * @param string $oldPath Eski dosya yolu (örn: 'uploads/profiles/ahmet-pp.webp')
     * @param string $newFilename Yeni dosya adı (örn: 'mehmet-pp')
     * @return string|false Yeni dosya yolu veya false
     */
    public static function rename($oldPath, $newFilename)
    {
        if (empty($oldPath) || $oldPath === 'default.png') {
            return false;
        }

        $fullOldPath = BASE_PATH . '/public/' . $oldPath;

        if (!file_exists($fullOldPath) || !is_file($fullOldPath)) {
            return false;
        }

        // Yeni dosya yolunu oluştur
        $directory = dirname($oldPath);
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        $sanitizedFilename = self::sanitizeFilename($newFilename);
        $newPath = $directory . '/' . $sanitizedFilename . '.' . $extension;
        $fullNewPath = BASE_PATH . '/public/' . $newPath;

        // Dosyayı yeniden adlandır
        if (rename($fullOldPath, $fullNewPath)) {
            return $newPath;
        }

        return false;
    }

    /**
     * Dosyayı siler
     * @param string $filePath Silinecek dosyanın relative path'i (örn: 'uploads/profiles/abc123.jpg')
     * @return bool Silme başarılı ise true
     */
    public static function delete($filePath)
    {
        if (empty($filePath) || $filePath === 'default.png') {
            return false;
        }

        $fullPath = BASE_PATH . '/public/' . $filePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}