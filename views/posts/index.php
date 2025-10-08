<?php $this->layout('layouts/admin/app', ['title' => 'Blog Yönetimi']) ?>

<?php $this->start('body') ?>
<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Blog Yönetimi</h2>
                <a href="<?= route('admin.posts.create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Yeni Post
                </a>
            </div>

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Başlık
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Yazar
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durum
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tarih
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">İşlemler</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Henüz hiç post eklenmemiş. <a href="<?= route('admin.posts.create') ?>" class="text-indigo-600 hover:text-indigo-900">Yeni post ekle</a>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if ($post->cover_image): ?>
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded object-cover" src="/<?= htmlspecialchars($post->cover_image) ?>" alt="">
                                        </div>
                                    <?php endif; ?>
                                    <div class="<?= $post->cover_image ? 'ml-4' : '' ?>">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($post->title) ?>
                                            <?php if ($post->is_featured): ?>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Öne Çıkan
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($post->slug) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($post->user->name ?? 'Bilinmiyor') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($post->status === 'published'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Yayınlandı
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Taslak
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d.m.Y', strtotime($post->created_at)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= route('admin.posts.edit') ?>?id=<?= $post->id ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <form method="POST" action="<?= route('admin.posts.delete') ?>" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $post->id ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Bu postu silmek istediğinize emin misiniz?')">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->end() ?>