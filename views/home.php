<?php $this->layout('layouts/base', ['title' => 'Kullanıcılar']) ?>

<?php $this->start('body') ?>
    <h2>Kullanıcılar</h2>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?= $user->name ?> <?= $user->surname ?> - <?= $user->username ?></li>
        <?php endforeach; ?>
    </ul>
<?php $this->end() ?>
