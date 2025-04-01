<?php $this->layout('layouts/base', ['title' => 'Giriş Yap']) ?>

<?php $this->start('body') ?>

<div style="max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

    <h2 style="margin-bottom: 20px;">🔐 Giriş Yap</h2>

    <form method="POST" action="<?= route('admin.login.do') ?>">

        <?= $this->csrf() ?>

        <label for="email">E-Posta</label><br>
        <input type="email" name="email" required style="width: 100%; padding: 10px; margin-bottom: 15px;"><br>

        <label for="password">Şifre</label><br>
        <input type="password" name="password" required style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

        <button type="submit" style="width: 100%; background-color: #343a40; color: white; padding: 10px; border: none; border-radius: 4px;">
            Giriş Yap
        </button>
    </form>

</div>

<?php $this->end() ?>
