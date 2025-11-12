<?php $this->layout('layouts/base') ?>

<?php $this->start('body') ?>
<nav class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-xl font-bold">FindikEngine</span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?= route('admin.users.index') ?>" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Kullanıcılar</a>
                        <a href="<?= route('admin.posts.index') ?>" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Blog</a>
                        <a href="#" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Ayarlar</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <?php if (auth()): ?>
                        <span class="mr-4">Hoş geldin, <?= htmlspecialchars(auth()->name) ?></span>
                        <form method="POST" action="<?= route('admin.logout') ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium text-white border-0 cursor-pointer">Çıkış</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <button onclick="toggleMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="<?= route('admin.users.index') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-700">Kullanıcılar</a>
            <a href="<?= route('admin.posts.index') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-700">Blog</a>
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-700">Ayarlar</a>
            <?php if (auth()): ?>
                <form method="POST" action="<?= route('admin.logout') ?>" class="block">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium hover:bg-gray-700 bg-transparent text-gray-300 border-0 cursor-pointer">Çıkış Yap</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php if (flash('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars(flash('success')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (flash('error')): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars(flash('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->section('body') ?>
</main>
<?php $this->end() ?>
