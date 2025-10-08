<?php $this->layout('layouts/base', ['title' => '403 - Erişim Yasak']) ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-300">403</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Erişim Yasak</h2>
            <p class="text-gray-500 mb-8"><?= $this->e($message ?? 'Bu sayfaya erişim yetkiniz yok.') ?></p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= $this->url('admin.login.show') ?>" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Giriş Yap
            </a>
        </div>
    </div>
</div>