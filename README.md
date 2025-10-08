# FindikEngine – Laravel Tadında Mikro PHP Framework

<p align="center">
  <img src="findik-engine.png" alt="Fındık Engine Logo" width="200"/>
</p>

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Framework](https://img.shields.io/badge/Framework-Custom-orange.svg)]()

FindikEngine, modern PHP ile geliştirilmiş, MVC mimarisini kullanan, kullanıcı yönetimi ve oturum açma özelliklerine sahip, hızlı ve güvenli bir web uygulama altyapısıdır. Laravel mantığında geliştirilmiş sade, modern ve genişletilebilir bir PHP mikro framework'tür.

**Özellikler:**
- 🚀 Laravel benzeri syntax ve yapı
- 🔒 Gelişmiş güvenlik sistemi (CSRF, XSS, SQL Injection koruması)
- 📦 Cache sistemi (Laravel tarzında)
- 📝 Logging ve monitoring
- 🖼️ Dosya yükleme sistemi
- 🎨 Plates template engine
- 🗄️ Eloquent ORM
- 🛡️ Middleware sistemi
- 📱 Responsive tasarım (Tailwind CSS)

**⚠️ Bu proje eğitim ve öğrenme amaçlıdır. Üretim ortamında kullanılması önerilmez.**

## 🚀 Kurulum

### 1. Gereksinimler

- PHP 8.0 veya üzeri
- Composer
- MySQL veritabanı
- Web sunucusu (Apache/Nginx) veya PHP built-in server

### 2. Hızlı Kurulum

```bash
# Projeyi klonlayın
git clone https://github.com/malisahin89/findik-engine.git
cd findik-engine

# Bağımlılıkları yükleyin
composer install

# Environment dosyasını oluşturun
cp .env.example .env

# Veritabanını import edin
mysql -u root -p < findikengine.sql

# Geliştirme sunucusunu başlatın
php -S localhost:8000 -t public
```

### 3. Environment Yapılandırması

`.env` dosyasını düzenleyin:

```env
# Uygulama Ayarları
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Veritabanı Ayarları
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=findikengine
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# Güvenlik Ayarları
SESSION_LIFETIME=30
MAX_LOGIN_ATTEMPTS=5
LOGIN_LOCKOUT_TIME=300
```

## 📁 Proje Yapısı

```
findik-engine/
├── app/                    # Uygulama katmanı
│   ├── Controllers/        # HTTP controller'ları
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── UserController.php
│   ├── Middleware/         # Uygulama middleware'leri
│   │   └── AuthMiddleware.php
│   └── Models/             # Eloquent modelleri
│       └── User.php
├── config/                 # Yapılandırma dosyaları
│   └── database.php
├── core/                   # Framework çekirdeği
│   ├── Middleware/         # Çekirdek middleware'ler
│   │   ├── SecurityHeaders.php
│   │   └── VerifyCsrfToken.php
│   ├── Cache.php           # Cache sistemi
│   ├── Csrf.php            # CSRF koruması
│   ├── FileUpload.php      # Dosya yükleme
│   ├── Logger.php          # Logging sistemi
│   ├── Response.php        # HTTP response yönetimi
│   ├── Route.php           # Routing sistemi
│   ├── View.php            # Template engine
│   └── helpers.php         # Yardımcı fonksiyonlar
├── public/                 # Web root dizini
│   ├── uploads/            # Yüklenen dosyalar
│   │   └── profiles/       # Profil resimleri
│   ├── css/                # Statik CSS dosyaları
│   ├── .htaccess           # Apache yapılandırması
│   └── index.php           # Giriş noktası
├── routes/                 # Route tanımları
│   └── web.php
├── storage/                # Uygulama depolama
│   ├── cache/              # Cache dosyaları
│   └── logs/               # Log dosyaları
├── views/                  # Template dosyaları
│   ├── auth/               # Kimlik doğrulama
│   ├── errors/             # Hata sayfaları (404, 403, 500)
│   ├── layouts/            # Layout şablonları
│   ├── users/              # Kullanıcı yönetimi
│   └── home.php            # Ana sayfa
├── .env.example            # Environment örnek dosyası
├── composer.json           # Composer bağımlılıkları
└── findikengine.sql        # Veritabanı şeması
```

## ✨ Temel Özellikler

### ✅ Route Sistemi

- Laravel benzeri route tanımlamaları
- Route grupları ve prefix desteği
- Named route ve redirect fonksiyonları

Örnek:

```php
// Basit route
Route::get('/', 'HomeController@index');

// Route grupları
Route::prefix('/admin')->group(function () {
    Route::get('/login', 'AuthController@showLogin');
    Route::get('/logout', 'AuthController@logout')->middleware('auth');
});
```

### ✅ Auth Sistemi

- Oturum tabanlı giriş sistemi
- Auth middleware koruması
- Flash mesaj sistemi
- CSRF koruması

### ✅ Plates Template Engine

**Plates**, PHP için geliştirilmiş, sade ve güvenli bir template engine'dir. Bu projede, `core/View.php` dosyası üzerinden Plates entegre edilmiştir.

- **views/** klasöründe tüm şablon dosyaları bulunur
- Ortak layout'lar `views/layouts/` altında tutulur
- Controller'lar, Plates ile ilgili sayfa şablonunu render eder ve değişkenleri kolayca aktarır

**Avantajları:**

- PHP kodunu HTML'den ayırır, kodun okunabilirliğini artırır
- Layout ve partial desteği ile tekrar kullanılabilir şablonlar oluşturulabilir
- Güvenli değişken aktarımı sağlar

Örnek:

```php
// Controller'da bir görünüm render etmek:
echo $this->view->render('users/list', ['users' => $users]);

// views/layouts/base.php
<?php $this->layout('layouts/base', ['title' => 'Sayfa Başlığı']) ?>

// views/users/index.php
<?php $this->start('content') ?>
    <h1>Kullanıcı Listesi</h1>
    <?= $this->insert('users/_list', ['users' => $users]) ?>
<?php $this->stop() ?>
```

### ✅ Helper Fonksiyonları

| Fonksiyon       | Açıklama                         |
| --------------- | -------------------------------- |
| `route('name')` | Named route URL'si döner         |
| `redirect()`    | Yönlendirme yapar                |
| `old('field')`  | Önceki input değerini döner      |
| `flash()`       | Flash mesaj sistemi              |
| `auth()`        | Giriş yapmış kullanıcıyı getirir |
| `asset('path')` | Statik dosya yolları             |
| `csrf()`        | CSRF token döner                 |
| `cache()`       | Cache sistemi                    |

### ✅ Cache Sistemi

Laravel tarzında cache sistemi ile performansı artırın:

```php
// Remember pattern - Cache varsa getir, yoksa hesapla ve cache'le
$users = Cache::remember('users', 300, function() {
    return User::all();
});

// Cache'i sil
Cache::forget('users');

// Tüm cache'i temizle
Cache::flush();

// Cache var mı kontrol et
if (Cache::has('users')) {
    // Cache var
}

// Cache'i al ve sil
$value = Cache::pull('key');

// Helper kullanımı
cache('key', 'value', 300); // Set
$value = cache('key');       // Get
```

### ✅ Dosya Yükleme Sistemi

Güvenli dosya yükleme sistemi:

```php
// Controller'da
try {
    $filePath = FileUpload::upload($_FILES['image'], 'uploads/images');
    // Dosya başarıyla yüklendi
} catch (Exception $e) {
    // Hata yönetimi
    echo $e->getMessage();
}
```

### ✅ Logging Sistemi

Uygulama olaylarını kaydetme:

```php
// Farklı log seviyeleri
Logger::info('Kullanıcı giriş yaptı', ['user_id' => 123]);
Logger::warning('Başarısız giriş denemesi', ['ip' => '192.168.1.1']);
Logger::error('Veritabanı bağlantı hatası', ['error' => $e->getMessage()]);
```

### ✅ Response Sistemi

HTTP response yönetimi:

```php
// JSON response
Response::json(['status' => 'success', 'data' => $data]);

// Redirect
Response::redirect('/dashboard');

// Error pages
Response::notFound();
Response::forbidden();
Response::serverError();
```

## 🔐 Güvenlik Özellikleri

### Temel Güvenlik
- ✅ **CSRF Koruması**: Tüm POST işlemlerinde token doğrulaması
- ✅ **XSS Koruması**: Template'lerde otomatik escape
- ✅ **SQL Injection**: Eloquent ORM ile parametreli sorgular
- ✅ **Şifre Güvenliği**: bcrypt ile hash'leme
- ✅ **Session Güvenliği**: HTTPOnly, Secure, SameSite cookies

### Gelişmiş Güvenlik
- ✅ **Rate Limiting**: Brute force saldırı koruması
- ✅ **Input Validation**: Güvenli veri doğrulama
- ✅ **File Upload Security**: MIME type ve boyut kontrolü
- ✅ **HTTP Security Headers**: CSP, XSS-Protection, HSTS
- ✅ **Path Traversal**: Güvenli dosya yolu yönetimi
- ✅ **Mass Assignment**: Model seviyesinde koruma

### Monitoring & Logging
- ✅ **Security Logging**: Tüm güvenlik olayları loglanır
- ✅ **Failed Login Tracking**: Başarısız giriş denemeleri
- ✅ **User Activity**: Kullanıcı işlemleri audit
- ✅ **Error Logging**: Sistem hatalarının kaydı

## 📦 Kullanılan Teknolojiler

### Backend
- **PHP 8.0+** - Modern PHP özellikleri
- **Eloquent ORM** - Laravel'in güçlü ORM sistemi
- **Plates Template Engine** - Hızlı ve güvenli template sistemi
- **Composer** - Bağımlılık yönetimi

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Font Awesome** - İkon kütüphanesi
- **Vanilla JavaScript** - Hafif ve hızlı

### Güvenlik
- **CSRF Protection** - Cross-site request forgery koruması
- **XSS Protection** - Cross-site scripting koruması
- **SQL Injection Protection** - Parametreli sorgular
- **File Upload Security** - Güvenli dosya yükleme

### Performans
- **File-based Caching** - Hızlı cache sistemi
- **Optimized Queries** - Veritabanı optimizasyonu
- **Asset Optimization** - Statik dosya optimizasyonu

## 🛠 Geliştirme

### Controller Yapısı

```php
namespace App\Controllers;

class UserController {
    public function index() {
        $users = User::all();
        View::render('users/index', ['users' => $users]);
    }

    public function create() {
        View::render('users/create');
    }
}
```

### Model Yapısı

```php
namespace App\Models;

class User {
    protected $table = 'users';

    public static function all() {
        return (new static())->query()->get();
    }
}
```

## 🌐 API Rotaları

### Genel Rotalar
- `GET /` - Ana sayfa
- `GET /admin/login` - Giriş sayfası
- `POST /admin/login` - Giriş işlemi
- `POST /admin/logout` - Çıkış işlemi

### Kullanıcı Yönetimi (Auth Required)
- `GET /admin/users` - Kullanıcı listesi
- `GET /admin/users/create` - Yeni kullanıcı formu
- `POST /admin/users/store` - Kullanıcı oluştur
- `GET /admin/users/edit?id={id}` - Kullanıcı düzenle formu
- `POST /admin/users/update` - Kullanıcı güncelle
- `POST /admin/users/delete` - Kullanıcı sil

### Hata Sayfaları
- `404` - Sayfa bulunamadı
- `403` - Erişim yasak
- `500` - Sunucu hatası

## 🚀 Geliştirme

### Yeni Controller Oluşturma

```php
<?php
namespace App\Controllers;

use Core\View;

class ExampleController
{
    public function index()
    {
        View::render('example/index', ['data' => $data]);
    }
}
```

### Yeni Route Tanımlama

```php
// routes/web.php
Route::get('/example', 'ExampleController@index')->name('example.index');
Route::post('/example', 'ExampleController@store')->middleware('auth');
```

### Yeni Middleware Oluşturma

```php
<?php
namespace App\Middleware;

class ExampleMiddleware
{
    public function handle($request, $next)
    {
        // Middleware logic
        return $next($request);
    }
}
```

## 🤝 Katkı Sağlama

1. Fork'layın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit'leyin (`git commit -m 'Add amazing feature'`)
4. Branch'i push'layın (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📋 TODO

- [ ] API endpoints
- [ ] Email sistemi
- [ ] Multi-language desteği
- [ ] Database migrations
- [ ] Unit testler
- [ ] Docker desteği

## 📊 Performans

- ⚡ **Hızlı Başlatma**: Minimal framework overhead
- 🗄️ **Veritabanı**: Eloquent ORM ile optimize edilmiş sorgular
- 💾 **Cache**: File-based caching sistemi
- 📦 **Küçük Boyut**: Sadece gerekli bileşenler
- 🔧 **Kolay Özelleştirme**: Modüler yapı

## 🔧 Sistem Gereksinimleri

- PHP >= 8.0
- MySQL >= 5.7 veya MariaDB >= 10.2
- Composer
- Apache/Nginx (mod_rewrite aktif)
- PHP Extensions: PDO, mbstring, fileinfo

## 📝 Lisans

Bu proje MIT lisansı altında açık kaynaklıdır. Eğitim ve öğrenme amaçlıdır.

## 👤 Geliştirici

**Muhammet Ali ŞAHİN**

- GitHub: [@MaliSahin89](https://github.com/malisahin89)
- Website: [malisahin.com](https://malisahin.com)

## 🙏 Teşekkürler

- [Laravel](https://laravel.com) - İlham kaynağı
- [Plates](https://platesphp.com) - Template engine
- [Tailwind CSS](https://tailwindcss.com) - CSS framework
- [Eloquent ORM](https://laravel.com/docs/eloquent) - Database ORM

---

**⭐ Projeyi beğendiyseniz yıldız vermeyi unutmayın!**
