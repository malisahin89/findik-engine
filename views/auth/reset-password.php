<?php $this->layout('layouts/base', ['title' => 'Şifre Sıfırla']) ?>

<?php $this->start('body') ?>
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Yeni Şifre Belirleyin
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Lütfen yeni şifrenizi girin
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="<?= route('admin.password.update') ?>">
                <?= $this->csrf() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

                <?php
                $error = flash('error');
                if ($error): ?>
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    <?= htmlspecialchars($error) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Yeni Şifre
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="En az 8 karakter">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Şifre en az 8 karakter olmalıdır</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Şifre Tekrar
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Şifrenizi tekrar girin">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Şifreyi Değiştir
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?= route('admin.login.show') ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Giriş sayfasına dön
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->end() ?>
