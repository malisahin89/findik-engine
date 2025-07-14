# FindikEngine – Laravel Tadında Mikro PHP Framework

<p align="center">
  <img src="findik-engine.png" alt="Fındık Engine Logo" width="200"/>
</p>

FindikEngine, modern PHP ile geliştirilmiş, MVC mimarisini kullanan, kullanıcı yönetimi ve oturum açma özelliklerine sahip, hızlı ve güvenli bir web uygulama altyapısıdır. Laravel mantığında geliştirilmiş sade, modern ve genişletilebilir bir PHP mikro framework'tür.

Eloquent ORM, Plates template engine, route/middleware/controller yapısı ile tam bir mini Laravel klonudur. Tamamen fantezisel olarak yapılmış ve anı olsun diye GitHub'a yüklenmiştir.

**⚠️ Üretim ortamında kullanılmaması önerilir. Sadece öğrenme ve geliştirme amaçlıdır.**

## 🚀 Kurulum

### 1. Gereksinimler

- PHP 8.0 veya üzeri
- Composer
- MySQL veritabanı
- Laragon veya başka bir PHP geliştirme ortamı

### 2. Kurulum Adımları

1. **Depoyu klonlayın:**

```bash
git clone https://github.com/malisahin89/findik-engine.git
cd findik-engine
```

2. **Bağımlılıkları yükleyin:**

```bash
composer install
```

3. **Veritabanı oluşturun ve tabloyu içe aktarın:**

   - `findikengine.sql` dosyasını MySQL veritabanınıza import edin.

4. **Veritabanı yapılandırmasını düzenleyin:**

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

5. **Geliştirme sunucusunu başlatın:**

```bash
php -S localhost:8000 -t public
```

veya Apache/Nginx ile `public` dizinini root olarak ayarlayın.

## 📁 Kapsamlı Klasör Yapısı

```
findik-engine/
│
├── app/                  # Uygulamanın ana iş mantığı
│   ├── Controllers/      # HTTP isteklerini yöneten controller'lar
│   ├── Middleware/       # Yetkilendirme ve doğrulama için ara katmanlar
│   └── Models/           # Veritabanı modelleri ve ORM tanımları
│
├── config/               # Yapılandırma dosyaları (örn. veritabanı ayarları)
│
├── core/                 # Framework'ün temel sınıfları ve yardımcılar
│   ├── Csrf.php          # CSRF koruma mekanizması
│   ├── helpers.php       # Genel yardımcı fonksiyonlar
│   ├── Middleware.php    # Middleware yönetimi
│   ├── Redirect.php      # Yönlendirme işlemleri
│   ├── Request.php       # HTTP istek yönetimi
│   ├── Route.php         # Rota tanımlama ve yönetimi
│   ├── View.php          # Plates ile görünüm yönetimi
│   └── Middleware/       # Çekirdek middleware'ler
│
├── public/               # Uygulamanın dışarıya açık kök dizini
│   ├── .htaccess         # Apache yapılandırması
│   ├── index.php         # Uygulamanın giriş noktası
│   └── css/              # Statik CSS dosyaları
│
├── routes/               # Rota tanımları
│   └── web.php           # Web uygulaması rotaları
│
├── views/                # HTML şablonları ve sayfa görünümleri
│   ├── home.php          # Ana sayfa görünümü
│   ├── auth/             # Giriş/çıkış ve kimlik doğrulama şablonları
│   ├── layouts/          # Ortak layout şablonları
│   └── users/            # Kullanıcı yönetimi şablonları
│
├── vendor/               # Composer ile yüklenen harici paketler
│
├── .gitignore            # Git için hariç tutulacak dosyalar
├── .gitattributes        # Git öznitelikleri
├── composer.json         # Composer bağımlılık tanımları
├── composer.lock         # Composer bağımlılık kilit dosyası
├── findikengine.sql      # Veritabanı şeması ve örnek veriler
└── README.md             # Proje açıklama dosyası
```

### Klasör Açıklamaları

- **app/**: Controller, Model ve Middleware dosyalarını içerir. Uygulamanın iş mantığı burada bulunur.
- **config/**: Yapılandırma dosyaları (örn. veritabanı ayarları).
- **core/**: Framework'ün temel fonksiyonları ve yardımcı sınıfları.
- **public/**: Dışarıya açık dizin. Tüm HTTP istekleri buradan başlar.
- **routes/**: Tüm uygulama rotaları burada tanımlanır.
- **views/**: HTML şablonları ve sayfa görünümleri.
- **vendor/**: Harici kütüphaneler (Composer ile yüklenir).
- **findikengine.sql**: Veritabanı şeması ve örnek veriler.

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

## 🔐 Güvenlik Özellikleri

- **CSRF Koruması**: Tüm POST işlemlerinde CSRF koruması vardır
- **Şifre Güvenliği**: Şifreler bcrypt ile hashlenir
- **Oturum Yönetimi**: Güvenli oturum sistemi
- **Middleware Koruması**: Yetkisiz erişimlerde AuthMiddleware ile yönlendirme yapılır
- **Input Doğrulama**: Güvenli veri işleme

## 📦 Kullanılan Paketler

- **illuminate/database** - Laravel'in Eloquent ORM paketidir. Kolay ve güçlü veritabanı işlemleri sağlar.
- **league/plates** - PHP için hızlı, güvenli ve esnek bir template engine'dir. Görünümleri (views) yönetmek için kullanılır.
- **vlucas/phpdotenv** - Ortam değişkenlerini yönetmek için kullanılır (isteğe bağlı).
- **tailwindcss** - Modern ve duyarlı arayüzler için CSS framework'ü. `public/css` altında derlenmiş olarak bulunur.
- **fontawesome** - İkonlar için kullanılır.

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

## 🌐 Kullanım Rotaları

- **Ana Sayfa:** `/`
- **Giriş:** `/admin/login`
- **Kullanıcı Yönetimi:** `/admin/users`
- **Kullanıcı Ekle:** `/admin/users/create`
- **Kullanıcı Düzenle:** `/admin/users/edit?id={id}`
- **Kullanıcı Sil:** `/admin/users/delete?id={id}`

## 🤝 Katkı Sağlama

1. Fork'layın
2. Yeni bir branch oluşturun (`git checkout -b feature/yeniozellik`)
3. Değişikliklerinizi commit'leyin (`git commit -am 'Yeni özellik ekledim'`)
4. Branch'i push'layın (`git push origin feature/yeniozellik`)
5. Pull request açın

## 📝 Lisans

Bu proje MIT lisansı altında açık kaynaklıdır ve sadece öğrenme amacıyla geliştirilmiştir. Üretim ortamında kullanılmaması önerilir.

Kod güvenliği tester varsa deneyen Auth ile ilgili destek verebilir. Boş zaman aktivitesidir, AI sayesinde kat kat iyisi yazılabilir, tamamen fanteziseldir. Acil bir proje gerekirse basit bir şablon olsun diye yazılmıştır.

## 👤 Geliştirici

**Muhammet Ali ŞAHİN**

- GitHub: [@MaliSahin89](https://github.com/malisahin89)
- Website: [malisahin.com](https://malisahin.com)

---

**Daha fazla bilgi için [Plates dökümantasyonu](https://platesphp.com/) adresini inceleyebilirsiniz.**
