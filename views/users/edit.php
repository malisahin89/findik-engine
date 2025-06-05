<?php $this->layout('layouts/admin/app', ['title' => 'Kullanıcı Düzenle']) ?>

<?php $this->start('body') ?>
<div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Kullanıcı Düzenle</h3>
            <p class="mt-1 text-sm text-gray-600">
                Kullanıcı bilgilerini güncelleyin.
            </p>
        </div>
    </div>
    <div class="mt-5 md:mt-0 md:col-span-2">
        <form method="POST" action="<?= route('admin.users.update') ?>" enctype="multipart/form-data">
            <?= $this->csrf() ?>
            <input type="hidden" name="id" value="<?= $user->id ?>">
            
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">Ad</label>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user->name) ?>" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="surname" class="block text-sm font-medium text-gray-700">Soyad</label>
                            <input type="text" name="surname" id="surname" value="<?= htmlspecialchars($user->surname) ?>" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>


                        <div class="col-span-6 sm:col-span-3">
                            <label for="username" class="block text-sm font-medium text-gray-700">Kullanıcı Adı</label>
                            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user->username) ?>" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">E-Posta</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email) ?>" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="password" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
                            <input type="password" name="password" id="password"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Değiştirmek istemiyorsanız boş bırakın">
                            <p class="mt-1 text-sm text-gray-500">Şifreyi değiştirmek istemiyorsanız boş bırakın</p>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Durum</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="active" <?= $user->status === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= $user->status === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                            </select>
                        </div>

                        <div class="col-span-6">
                            <label for="profile_image" class="block text-sm font-medium text-gray-700">Profil Resmi</label>
                            <div class="mt-1 flex items-center">
                                <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                    <?php if (!empty($user->profile_image)): ?>
                                        <img src="<?= asset('uploads/' . $user->profile_image) ?>" alt="Profil Resmi" class="h-full w-full">
                                    <?php else: ?>
                                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    <?php endif; ?>
                                </span>
                                <input type="file" name="profile_image" id="profile_image"
                                       class="ml-5 py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="col-span-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biyografi</label>
                            <div class="mt-1">
                                <textarea id="bio" name="bio" rows="3"
                                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"><?= htmlspecialchars($user->bio) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="<?= route('admin.users.index') ?>" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                        İptal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->end() ?>
