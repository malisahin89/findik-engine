<?php $this->layout('layouts/base') ?>

<?php $this->start('body') ?>
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-gray-800">FindikEngine</span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="/" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Ana Sayfa
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Hakkımızda
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        İletişim
                    </a>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= route('admin.users.index') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Yönetim Paneli
                    </a>
                    <form method="POST" action="<?= route('admin.logout') ?>" class="inline ml-4">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                            Çıkış Yap
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= route('admin.login.show') ?>" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Giriş Yap
                    </a>
                <?php endif; ?>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <button onclick="toggleMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/" class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Ana Sayfa</a>
            <a href="#" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Hakkımızda</a>
            <a href="#" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">İletişim</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= route('admin.users.index') ?>" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Yönetim Paneli</a>
            <?php else: ?>
                <a href="<?= route('admin.login.show') ?>" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Giriş Yap</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <?php if (isset($success)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= $success ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= $error ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?= $this->section('main') ?>
</main>

<footer class="bg-white mt-12">
    <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
        <nav class="-mx-5 -my-2 flex flex-wrap justify-center" aria-label="Footer">
            <div class="px-5 py-2">
                <a href="#" class="text-base text-gray-500 hover:text-gray-900">Hakkımızda</a>
            </div>
            <div class="px-5 py-2">
                <a href="#" class="text-base text-gray-500 hover:text-gray-900">İletişim</a>
            </div>
            <div class="px-5 py-2">
                <a href="#" class="text-base text-gray-500 hover:text-gray-900">Gizlilik Politikası</a>
            </div>
            <div class="px-5 py-2">
                <a href="#" class="text-base text-gray-500 hover:text-gray-900">Kullanım Koşulları</a>
            </div>
        </nav>
        <p class="mt-8 text-center text-base text-gray-400">
            &copy; <?= date('Y') ?> FindikEngine. Tüm hakları saklıdır.
        </p>
    </div>
</footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}
</script>
<?php $this->end() ?>
