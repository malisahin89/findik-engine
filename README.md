# FindikEngine – Laravel Tadında Mikro PHP Framework

<p align="center">
  <img src="findik-engine.png" alt="Fındık Engine Logo" width="200"/>
</p>

FindikEngine, Laravel mantığında geliştirilmiş sade, modern ve genişletilebilir bir PHP mikro framework'tür.
Eloquent ORM, Plates template engine, route/middleware/controller yapısı ile tam bir mini Laravel klonudur.
Tamamen fantezisel olarak yapılmış ve anı olsun diye GitHub'a yüklenmiştir.
Denemenizi tavsiye etmem

## 🚀 Kurulum

### 1. Gereksinimler

- PHP 8.0 veya üzeri
- Composer
- MySQL veritabanı
- Laragon veya başka bir PHP geliştirme ortamı

### 2. Kurulum Adımları

1. Projeyi klonlayın:
```bash
git clone https://github.com/malisahin89/findik-engine.git
```

2. Composer ile bağımlılıkları kurun:
```bash
composer install
```

3. Veritabanı yapılandırmasını düzenleyin:
```php
// config/database.php
return [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'findikengine',
    'username'  => 'root',
    'password'  => '',
];
```

4. Web sunucunuzu başlatın

## 📁 Proje Yapısı

```
findikengine/
├── app/                # Uygulama kodları
│   ├── Controllers/    # Route kontrolleri
│   ├── Models/        # Veritabanı modelleri
│   └── Middleware/    # Route ara yazılımları
├── config/            # Yapılandırma dosyaları
│   └── database.php   # Veritabanı ayarları
├── core/              # Framework çekirdeği
│   ├── Route.php      # Route yönetimi
│   ├── View.php      # View yönetimi
│   ├── Request.php    # HTTP istek yönetimi
│   └── Csrf.php      # CSRF koruması
├── public/            # Web root dizini
│   └── index.php     # Ana girdi noktası
├── routes/            # Route tanımlamaları
│   └── web.php       # Web rotaları
├── views/            # Template dosyaları
│   ├── layouts/      # Temel layoutlar
│   └── users/        # Kullanıcı görünümleri
├── composer.json     # Bağımlılıklar
└── README.md        # Proje dokümantasyonu
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

### ✅ Template Engine

- Plates template engine
- Blade benzeri template sözdizimi
- Layout destekleri
- Helper fonksiyonları

Örnek:
```php
// views/layouts/base.php
<?php $this->layout('layouts/base', ['title' => 'Sayfa Başlığı']) ?>

// views/users/index.php
<?php $this->start('content') ?>
    <h1>Kullanıcı Listesi</h1>
    <?= $this->insert('users/_list', ['users' => $users]) ?>
<?php $this->stop() ?>
```

### ✅ Helper Fonksiyonları

| Fonksiyon       | Açıklama                          |
|----------------|-----------------------------------|
| `route('name')`| Named route URL'si döner         |
| `redirect()`   | Yönlendirme yapar                |
| `old('field')` | Önceki input değerini döner      |
| `flash()`      | Flash mesaj sistemi              |
| `auth()`       | Giriş yapmış kullanıcıyı getirir |
| `asset('path')`| Statik dosya yolları             |
| `csrf()`       | CSRF token döner                 |

## 🔐 Güvenlik Özellikleri

- CSRF koruması
- Input doğrulama
- Oturum yönetimi
- Middleware koruması

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

## 📝 Lisans

Bu proje açık kaynaklıdır ve sadece öğrenme amacıyla geliştirilmiştir. Üretim ortamında kullanılmaması önerilir.
Kod güvenliği tester varsa deneyen Auth ile ilgili destek verebilir.
Boş zaman aktivitesidir, Aİ sayesinde kat kat iyisi yazılabilir, tamamen fanteziseldir.
Acil bir proje gerekirse basit bir şablon olsun diye yazılmıştır

## 👤 Geliştirici

Muhammet Ali ŞAHİN | @MaliSahin89 | malisahin.com
