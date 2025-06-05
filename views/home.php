<?php $this->layout('layouts/main', ['title' => 'Ana Sayfa']) ?>

<?php $this->start('body') ?>
<!-- Hero Section -->
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
            <span class="block">FindikEngine'e Hoş Geldiniz</span>
            <span class="block text-indigo-600">Modern Web Uygulamaları</span>
        </h1>
        <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
            Hızlı, güvenli ve ölçeklenebilir web uygulamaları geliştirmek için güçlü bir altyapı.
        </p>
        <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="rounded-md shadow">
                    <a href="<?= route('admin.login.show') ?>" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                        Giriş Yap
                    </a>
                </div>
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                    <a href="<?= route('admin.users.index') ?>" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        Kullanıcıları Görüntüle
                    </a>
                </div>
            <?php else: ?>
                <div class="rounded-md shadow">
                    <a href="<?= route('admin.users.index') ?>" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                        Panele Git
                    </a>
                </div>
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                    <a href="<?= route('admin.logout') ?>" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        Çıkış Yap
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User List Section -->
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-10">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Kullanıcılar</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Sistemdeki Kullanıcılar
            </p>
            <p class="mt-3 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                FindikEngine kullanıcı listesi
            </p>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <ul class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <li class="px-6 py-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-medium"><?= strtoupper(substr($user->name, 0, 1) . substr($user->surname, 0, 1)) ?></span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($user->name) ?> <?= htmlspecialchars($user->surname) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    @<?= htmlspecialchars($user->username) ?>
                                </div>
                            </div>
                            <div class="ml-auto">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $user->status === 'active' ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Özellikler</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Daha iyi bir geliştirme deneyimi
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                FindikEngine ile modern web uygulamalarını hızlı ve kolay bir şekilde geliştirin.
            </p>
        </div>

        <div class="mt-10">
            <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Hızlı Kurulum</p>
                    <p class="mt-2 ml-16 text-base text-gray-500">
                        Sadece birkaç adımda projenizi çalışır hale getirin ve hemen kodlamaya başlayın.
                    </p>
                </div>

                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Güvenlik</p>
                    <p class="mt-2 ml-16 text-base text-gray-500">
                        Gelişmiş güvenlik önlemleri ile verileriniz güvende.
                    </p>
                </div>

                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Duyarlı Tasarım</p>
                    <p class="mt-2 ml-16 text-base text-gray-500">
                        Tüm cihazlarda mükemmel görünen duyarlı arayüzler oluşturun.
                    </p>
                </div>

                <div class="relative">
                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                        <i class="fas fa-cog"></i>
                    </div>
                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Kolay Özelleştirme</p>
                    <p class="mt-2 ml-16 text-base text-gray-500">
                        İhtiyaçlarınıza göre kolayca özelleştirilebilir yapı.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-indigo-700">
    <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
            <span class="block">Hazır mısınız?</span>
            <span class="block">Hemen başlayın.</span>
        </h2>
        <p class="mt-4 text-lg leading-6 text-indigo-200">
            FindikEngine ile projenizi hızlıca hayata geçirin.
        </p>
        <a href="#" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 sm:w-auto">
            Ücretsiz Deneyin
        </a>
    </div>
</div>
<?php $this->end() ?>
