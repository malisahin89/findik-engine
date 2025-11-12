<?php

namespace App\Controllers;

use App\Models\Post;
use Core\View;
use Core\Logger;
use Core\Cache;
use Core\FileUpload;

class PostController
{
    public function index()
    {
        $posts = Cache::remember('posts_list', 300, function() {
            return Post::with('user')->orderBy('created_at', 'desc')->get();
        });

        View::render('posts/index', ['posts' => $posts]);
    }

    public function create()
    {
        View::render('posts/create');
    }

    public function store()
    {
        $allowedFields = ['title', 'slug', 'content', 'short_description', 'meta_keywords', 'status', 'is_featured', 'comment_enabled', 'order'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));

        // Input sanitization
        $request = array_map('trim', $request);
        $request['title'] = htmlspecialchars($request['title'] ?? '');
        $request['short_description'] = htmlspecialchars($request['short_description'] ?? '');
        $request['meta_keywords'] = htmlspecialchars($request['meta_keywords'] ?? '');

        // Slug oluştur (boşsa)
        if (empty($request['slug'])) {
            $request['slug'] = $this->createSlug($request['title']);
        } else {
            $request['slug'] = $this->createSlug($request['slug']);
        }

        // Slug unique kontrolü
        $slugExists = Post::where('slug', $request['slug'])->first();
        if ($slugExists) {
            $request['slug'] = $request['slug'] . '-' . time();
        }

        $errors = validate($request, [
            'title' => 'required',
            'slug' => 'required',
            'content' => 'required',
            'status' => 'required|in:draft,published',
        ]);

        if ($errors) {
            View::render('posts/create', [
                'errors' => $errors,
                'old' => $request
            ]);
            return;
        }

        $postData = $request;
        $postData['user_id'] = $_SESSION['user_id'];

        // Handle checkboxes
        $postData['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $postData['comment_enabled'] = isset($_POST['comment_enabled']) ? 1 : 0;

        // Kapak resmi yükleme (slug-based isimlendirme ile WebP)
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $customFilename = $postData['slug'] . '-cover';
                $postData['cover_image'] = FileUpload::upload($_FILES['cover_image'], 'uploads/posts', true, $customFilename);
            } catch (\Exception $e) {
                View::render('posts/create', [
                    'errors' => ['cover_image' => [$e->getMessage()]],
                    'old' => $request
                ]);
                return;
            }
        }

        // Galeri resimleri yükleme (WebP dönüştürme ile)
        $galleryImages = [];
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['tmp_name'])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    try {
                        $file = [
                            'tmp_name' => $tmpName,
                            'name' => $_FILES['gallery_images']['name'][$key],
                            'size' => $_FILES['gallery_images']['size'][$key]
                        ];
                        $customFilename = $postData['slug'] . '-gallery-' . ($key + 1);
                        $galleryImages[] = FileUpload::upload($file, 'uploads/posts/gallery', true, $customFilename);
                    } catch (\Exception $e) {
                        // Hata durumunda devam et
                        Logger::warning('Gallery image upload failed', [
                            'error' => $e->getMessage(),
                            'file_key' => $key
                        ]);
                    }
                }
            }
        }
        $postData['gallery_images'] = $galleryImages;

        $newPost = Post::create($postData);

        Cache::forget('posts_list');

        Logger::info('Post created', [
            'post_id' => $newPost->id,
            'slug' => $newPost->slug,
            'created_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        flash('success', 'Post başarıyla oluşturuldu!');
        redirect('admin.posts.index');
    }

    public function edit()
    {
        $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            flash('error', 'Geçersiz post ID!');
            redirect('admin.posts.index');
            return;
        }
        
        $post = Post::find($id);
        if (!$post) {
            flash('error', 'Post bulunamadı!');
            redirect('admin.posts.index');
            return;
        }
        
        View::render('posts/edit', ['post' => $post]);
    }

    public function update()
    {
        $postId = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$postId) {
            flash('error', 'Geçersiz post ID!');
            redirect('admin.posts.index');
            return;
        }

        // Post'u bul
        $post = Post::find($postId);
        if (!$post) {
            flash('error', 'Post bulunamadı!');
            redirect('admin.posts.index');
            return;
        }

        $allowedFields = ['title', 'slug', 'content', 'short_description', 'meta_keywords', 'status', 'is_featured', 'comment_enabled', 'order'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));

        // Input sanitization
        $request = array_map('trim', $request);
        $request['title'] = htmlspecialchars($request['title'] ?? '');
        $request['short_description'] = htmlspecialchars($request['short_description'] ?? '');
        $request['meta_keywords'] = htmlspecialchars($request['meta_keywords'] ?? '');

        // Slug oluştur (boşsa)
        if (empty($request['slug'])) {
            $request['slug'] = $this->createSlug($request['title']);
        } else {
            $request['slug'] = $this->createSlug($request['slug']);
        }

        // Slug unique kontrolü (kendi ID'si hariç)
        $slugExists = Post::where('slug', $request['slug'])
            ->where('id', '!=', $postId)
            ->first();
        if ($slugExists) {
            $request['slug'] = $request['slug'] . '-' . time();
        }

        $errors = validate($request, [
            'title' => 'required',
            'slug' => 'required',
            'content' => 'required',
            'status' => 'required|in:draft,published',
        ]);

        if ($errors) {
            View::render('posts/edit', [
                'errors' => $errors,
                'post' => (object) array_merge((array) $post, $request)
            ]);
            return;
        }

        $data = $request;

        // Handle checkboxes
        $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $data['comment_enabled'] = isset($_POST['comment_enabled']) ? 1 : 0;

        // Slug değişti mi kontrol et
        $slugChanged = ($post->slug !== $data['slug']);

        // Kapak resmi yükleme
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $oldCoverImage = $post->cover_image ?? null;

                // Yeni kapak resmini yükle
                $customFilename = $data['slug'] . '-cover';
                $data['cover_image'] = FileUpload::upload($_FILES['cover_image'], 'uploads/posts', true, $customFilename);

                // Eski kapak resmini sil (farklı dosya ise)
                if ($oldCoverImage && $oldCoverImage !== $data['cover_image']) {
                    FileUpload::delete($oldCoverImage);
                }
            } catch (\Exception $e) {
                View::render('posts/edit', [
                    'errors' => ['cover_image' => [$e->getMessage()]],
                    'post' => (object) array_merge((array) $post, $request)
                ]);
                return;
            }
        } else {
            // Yeni resim yüklenmediyse ama slug değiştiyse, mevcut resmi yeniden adlandır
            if ($slugChanged && $post->cover_image) {
                $newFilename = $data['slug'] . '-cover';
                $newPath = FileUpload::rename($post->cover_image, $newFilename);
                if ($newPath) {
                    $data['cover_image'] = $newPath;
                }
            }
        }

        // Galeri resimleri silme işlemi
        $existingGalleryImages = $post->gallery_images ?? [];
        $deleteGalleryIndexes = $_POST['delete_gallery_images'] ?? [];
        $galleryChanged = false;

        if (!empty($deleteGalleryIndexes) && is_array($deleteGalleryIndexes)) {
            foreach ($deleteGalleryIndexes as $deleteIndex) {
                $deleteIndex = (int)$deleteIndex;
                if (isset($existingGalleryImages[$deleteIndex])) {
                    // Dosyayı sil
                    FileUpload::delete($existingGalleryImages[$deleteIndex]);
                    // Array'den çıkar
                    unset($existingGalleryImages[$deleteIndex]);
                    $galleryChanged = true;

                    Logger::info('Gallery image deleted', [
                        'post_id' => $postId,
                        'deleted_index' => $deleteIndex
                    ]);
                }
            }
            // Array indexlerini yeniden düzenle
            $existingGalleryImages = array_values($existingGalleryImages);
        }

        // Yeni galeri resimleri yükleme
        $newGalleryImages = [];
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['tmp_name'])) {
            $galleryCount = count($existingGalleryImages);
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    try {
                        $file = [
                            'tmp_name' => $tmpName,
                            'name' => $_FILES['gallery_images']['name'][$key],
                            'size' => $_FILES['gallery_images']['size'][$key]
                        ];
                        $customFilename = $data['slug'] . '-gallery-' . ($galleryCount + $key + 1);
                        $newGalleryImages[] = FileUpload::upload($file, 'uploads/posts/gallery', true, $customFilename);
                        $galleryChanged = true;
                    } catch (\Exception $e) {
                        Logger::warning('Gallery image upload failed', [
                            'error' => $e->getMessage(),
                            'file_key' => $key,
                            'post_id' => $postId
                        ]);
                    }
                }
            }
        }

        // Galeri resimlerini birleştir
        $finalGalleryImages = array_merge($existingGalleryImages, $newGalleryImages);

        // Slug değişti veya galeri değişti ise TÜM galeri resimlerini yeniden numaralandır
        if (($slugChanged || $galleryChanged) && !empty($finalGalleryImages)) {
            $renumberedGalleryImages = [];
            foreach ($finalGalleryImages as $index => $imagePath) {
                $newFilename = $data['slug'] . '-gallery-' . ($index + 1);
                $newPath = FileUpload::rename($imagePath, $newFilename);
                $renumberedGalleryImages[] = $newPath ?: $imagePath;
            }
            $finalGalleryImages = $renumberedGalleryImages;
        }

        // Galeri resimleri değiştiyse data'ya ekle
        if ($galleryChanged || $slugChanged) {
            $data['gallery_images'] = $finalGalleryImages;
        }

        $post->update($data);

        Cache::forget('posts_list');

        Logger::info('Post updated', [
            'post_id' => $postId,
            'slug' => $data['slug'],
            'slug_changed' => $slugChanged,
            'updated_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        flash('success', 'Post başarıyla güncellendi!');
        redirect('admin.posts.index');
    }

    public function delete()
    {
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            flash('error', 'Geçersiz post ID!');
            redirect('admin.posts.index');
            return;
        }

        $post = Post::find($id);
        if (!$post) {
            flash('error', 'Post bulunamadı!');
            redirect('admin.posts.index');
            return;
        }

        // Kapak resmini sil
        if ($post->cover_image) {
            FileUpload::delete($post->cover_image);
        }

        // Galeri resimlerini sil
        $galleryImages = $post->gallery_images ?? [];
        if (!empty($galleryImages) && is_array($galleryImages)) {
            foreach ($galleryImages as $imagePath) {
                FileUpload::delete($imagePath);
            }
        }

        $post->delete();

        Cache::forget('posts_list');

        Logger::warning('Post deleted', [
            'deleted_post_id' => $id,
            'deleted_post_slug' => $post->slug,
            'deleted_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);

        flash('success', 'Post ve tüm resimleri başarıyla silindi!');
        redirect('admin.posts.index');
    }
    
    private function createSlug($title)
    {
        // Turkish character replacements
        $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'];
        
        $slug = str_replace($turkish, $english, $title);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}