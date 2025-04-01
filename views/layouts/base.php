<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= $this->e($title ?? 'Yönetim Paneli') ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<header>
    <div>FindikEngine</div>
    <div>
        <?php if (auth()): ?>
            Hoş geldin, <?= auth()->name ?> |
            <a href="<?= route('admin.logout') ?>" style="color: #fff;">Çıkış</a>
        <?php endif; ?>
    </div>
</header>

<div class="sidebar">
    <a href="<?= route('admin.users.index') ?>">👥 Kullanıcılar</a>
    <a href="#">📦 Ürünler</a>
    <a href="#">⚙️ Ayarlar</a>
</div>

<div class="main-content">
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->section('body') ?>
</div>

</body>
</html>
