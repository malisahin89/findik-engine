<?php $this->layout('layouts/base', ['title' => 'Kullanıcı Düzenle']) ?>
<?php $this->start('body') ?>

<h2>✏️ Kullanıcı Düzenle</h2>

<form method="POST" action="<?= route('admin.users.update') ?>" enctype="multipart/form-data">
    <?= $this->csrf() ?>
    <input type="hidden" name="id" value="<?= $user->id ?>">

    <label>Ad:</label><br>
    <input type="text" name="name" value="<?= $user->name ?>" required><br><br>

    <label>Soyad:</label><br>
    <input type="text" name="surname" value="<?= $user->surname ?>" required><br><br>

    <label>Kullanıcı Adı:</label><br>
    <input type="text" name="username" value="<?= $user->username ?>" required><br><br>

    <label>E-Posta:</label><br>
    <input type="email" name="email" value="<?= $user->email ?>" required><br><br>

    <label>Yeni Şifre (boş bırakılırsa değişmez):</label><br>
    <input type="password" name="password"><br><br>

    <label>Profil Resmi:</label><br>
    <input type="file" name="profile_image"><br><br>

    <label>Biyografi:</label><br>
    <textarea name="bio" rows="4"><?= $user->bio ?></textarea><br><br>

    <label>Durum:</label><br>
    <select name="status">
        <option value="active" <?= $user->status === 'active' ? 'selected' : '' ?>>Aktif</option>
        <option value="inactive" <?= $user->status === 'inactive' ? 'selected' : '' ?>>Pasif</option>
    </select><br><br>

    <button type="submit">Güncelle</button>
</form>

<?php $this->end() ?>
