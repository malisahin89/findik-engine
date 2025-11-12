<?php $this->layout('layouts/base', ['title' => 'Şifremi Unuttum']) ?>

<?php $this->start('body') ?>
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Şifrenizi mi unuttunuz?
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            E-posta adresinizi girin, size şifre sıfırlama linki gönderelim.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="<?= route('admin.password.email') ?>">
                <?= $this->csrf() ?>

                <?php
                $success = flash('success');
                if ($success): ?>
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    <?= htmlspecialchars($success) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

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
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        E-Posta Adresi
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="ornek@email.com">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Şifre Sıfırlama Linki Gönder
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
