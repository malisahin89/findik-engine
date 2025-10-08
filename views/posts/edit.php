<?php $this->layout('layouts/admin/app', ['title' => 'Post Düzenle']) ?>

<?php $this->start('body') ?>
<div class="max-w-7xl mx-auto mt-10">
    <?php if (isset($errors) && $errors): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= route('admin.posts.update') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= $post->id ?>">

        <!-- Genel Bilgiler -->
        <div class="bg-white p-6 shadow rounded-lg space-y-4">
            <h2 class="text-xl font-semibold mb-4">Post Düzenle</h2>

            <!-- Başlık -->
            <div class="space-y-2">
                <label class="block text-gray-700">Başlık:</label>
                <input type="text" name="title" value="<?= htmlspecialchars($post->title) ?>" 
                       class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                       placeholder="Post başlığı" required>
            </div>

            <!-- Slug -->
            <div class="space-y-2">
                <label class="block text-gray-700">Slug:</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($post->slug) ?>" 
                       class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                       placeholder="SEO dostu URL">
            </div>

            <!-- Kısa Açıklama -->
            <div class="space-y-2">
                <label class="block text-gray-700">Kısa Açıklama:</label>
                <textarea name="short_description" rows="3" 
                          class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                          placeholder="Özet bilgi"><?= htmlspecialchars($post->short_description) ?></textarea>
            </div>

            <!-- İçerik -->
            <div class="space-y-2">
                <label class="block text-gray-700">İçerik:</label>
                <textarea name="content" rows="10" 
                          class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                          placeholder="Post içeriği" required><?= htmlspecialchars($post->content) ?></textarea>
            </div>

            <!-- Kapak Fotoğrafı -->
            <div class="space-y-2">
                <label class="block text-gray-700">Kapak Fotoğrafı:</label>
                <div class="flex items-center space-x-4">
                    <?php if ($post->cover_image): ?>
                        <div class="relative group">
                            <img src="/<?= htmlspecialchars($post->cover_image) ?>" class="h-32 w-32 object-cover rounded-lg shadow" id="cover-preview">
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <input type="file" name="cover_image" id="cover_image" accept="image/*" 
                               class="border rounded-lg w-full px-3 py-2" onchange="previewCover(event)">
                    </div>
                </div>
            </div>

            <!-- Galeri Fotoğrafları -->
            <div class="space-y-2">
                <label class="block text-gray-700">Galeri Fotoğrafları:</label>
                <input type="file" name="gallery_images[]" multiple accept="image/*" 
                       class="border rounded-lg w-full px-3 py-2" onchange="previewGallery(event)">
                
                <!-- Mevcut Galeri -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4" id="gallery-preview">
                    <?php 
                    $galleryImages = $post->gallery_images ? json_decode($post->gallery_images, true) : [];
                    if (!empty($galleryImages) && is_array($galleryImages)): 
                    ?>
                        <?php foreach ($galleryImages as $image): ?>
                            <div class="relative group">
                                <img src="/<?= htmlspecialchars($image) ?>" class="w-full h-32 object-cover rounded-lg shadow">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Diğer Alanlar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label class="block text-gray-700">Sıralama:</label>
                    <input type="number" name="order" value="<?= htmlspecialchars($post->order) ?>" 
                           class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-2">
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="is_featured" value="1" <?= $post->is_featured ? 'checked' : '' ?>
                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-gray-700">Öne Çıkar</span>
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="comment_enabled" value="1" <?= $post->comment_enabled ? 'checked' : '' ?>
                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-gray-700">Yorum Açık</span>
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-gray-700">Durum:</label>
                <select name="status" class="border rounded-lg w-full px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="draft" <?= $post->status === 'draft' ? 'selected' : '' ?>>Taslak</option>
                    <option value="published" <?= $post->status === 'published' ? 'selected' : '' ?>>Yayınla</option>
                </select>
            </div>
        </div>

        <!-- Gönder Butonu -->
        <div class="flex justify-between">
            <a href="<?= route('admin.posts.index') ?>" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition">
                İptal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Güncelle
            </button>
        </div>
    </form>
</div>

<script>
function previewCover(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('cover-preview');
        if (preview) {
            preview.src = reader.result;
        }
    };
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function previewGallery(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('gallery-preview');

    for (let file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow">`;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
<?php $this->end() ?>