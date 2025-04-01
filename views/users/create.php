<?php $this->layout('layouts/base', ['title' => 'Yeni Kullanıcı Ekle']) ?>
<?php $this->start('body') ?>

<h2>➕ Yeni Kullanıcı Ekle</h2>

<form method="POST" action="<?= route('admin.users.store') ?>" enctype="multipart/form-data">
    <?= $this->csrf() ?>

    <label>Ad:</label><br>
    <input type="text" name="name" value="<?= old('name') ?>" required><br><br>

    <label>Soyad:</label><br>
    <input type="text" name="surname" value="<?= old('surname') ?>" required><br><br>

    <label>Kullanıcı Adı:</label><br>
    <input type="text" name="username" value="<?= old('username') ?>" required><br><br>

    <label>E-Posta:</label><br>
    <input type="email" name="email" value="<?= old('email') ?>" required><br><br>

    <label>Şifre:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Profil Resmi:</label><br>
    <input type="file" name="profile_image"><br><br>

    <label>Biyografi:</label><br>
    <textarea name="bio" rows="4"><?= old('bio') ?></textarea><br><br>

    <label>Durum:</label><br>
    <select name="status">
        <option value="active">Aktif</option>
        <option value="passive">Pasif</option>
    </select><br><br>

    <button type="submit">Kaydet</button>
</form>

<?php $this->end() ?>
