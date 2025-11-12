<?php $this->layout('layouts/admin/app', ['title' => 'Kullanıcı Yönetimi']) ?>

<?php $this->start('body') ?>
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Kullanıcı Yönetimi</h2>
                <a href="<?= route('admin.users.create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Yeni Kullanıcı
                </a>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ad Soyad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kullanıcı Adı
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                E-posta
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durum
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">İşlemler</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if ($user->profile_image && $user->profile_image !== 'default.png'): ?>
                                            <img class="h-10 w-10 rounded-full object-cover" src="/<?= htmlspecialchars($user->profile_image) ?>" alt="<?= htmlspecialchars($user->name) ?>">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($user->name) ?> <?= htmlspecialchars($user->surname) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user->username) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($user->email) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($user->status === 'active'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Pasif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= route('admin.users.edit') ?>?id=<?= $user->id ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <form method="POST" action="<?= route('admin.users.delete') ?>" style="display: inline;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $user->id ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-transparent border-0 cursor-pointer">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pagination)): ?>
            <div class="mt-4 flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Toplam <span class="font-medium"><?= $pagination['total'] ?></span> kullanıcı
                </div>
                <div class="flex space-x-2">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="px-3 py-1 border rounded text-gray-700 hover:bg-gray-50">
                            Önceki
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-3 py-1 border rounded <?= $i === $pagination['current_page'] ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="px-3 py-1 border rounded text-gray-700 hover:bg-gray-50">
                            Sonraki
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->end() ?>
