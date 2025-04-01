<?php $this->layout('layouts/base', ['title' => 'Kullanıcı Listesi']) ?>

<?php $this->start('body') ?>

<h2>Kullanıcılar</h2>
<a href="<?= route('admin.users.create') ?>">Yeni Ekle</a>
<table border="1" cellpadding="10">
    <tr>
        <th>Ad</th>
        <th>Soyad</th>
        <th>Kullanıcı Adı</th>
        <th>Durum</th>
        <th>İşlem</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user->name ?></td>
        <td><?= $user->surname ?></td>
        <td><?= $user->username ?></td>
        <td><?= $user->status ?></td>
        <td>
            <a href="<?= route('admin.users.edit') ?>?id=<?= $user->id ?>">Düzenle</a> |
            <a href="<?= route('admin.users.delete') ?>?id=<?= $user->id ?>" onclick="return confirm('Silinsin mi?')">Sil</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php $this->end() ?>
