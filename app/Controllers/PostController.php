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
            return \Illuminate\Database\Capsule\Manager::table('posts')
                ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                ->select('posts.*', 'users.name as user_name')
                ->orderBy('posts.created_at', 'desc')
                ->get();
        });
        
        // Convert to objects with user property
        $posts = array_map(function($post) {
            $postObj = (object) $post;
            $postObj->user = (object) ['name' => $post->user_name ?? 'Bilinmiyor'];
            $postObj->gallery_images = $post->gallery_images ? json_decode($post->gallery_images, true) : [];
            return $postObj;
        }, $posts->toArray());
        
        View::render('posts/index', ['posts' => $posts]);
    }

    public function create()
    {
        View::render('posts/create');
    }

    public function store()
    {
        $allowedFields = ['title', 'slug', 'content', 'short_description', 'status', 'is_featured', 'comment_enabled', 'order'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));
        
        // Input sanitization
        $request = array_map('trim', $request);
        $request['title'] = htmlspecialchars($request['title'] ?? '');
        $request['short_description'] = htmlspecialchars($request['short_description'] ?? '');
        
        $errors = validate($request, [
            'title' => 'required',
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
        
        // Slug oluştur
        if (empty($postData['slug'])) {
            $postData['slug'] = $this->createSlug($postData['title']);
        }
        
        // Kapak resmi yükleme
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $postData['cover_image'] = FileUpload::upload($_FILES['cover_image'], 'uploads/posts');
            } catch (\Exception $e) {
                View::render('posts/create', [
                    'errors' => ['cover_image' => [$e->getMessage()]],
                    'old' => $request
                ]);
                return;
            }
        }
        
        // Galeri resimleri yükleme
        $galleryImages = [];
        if (isset($_FILES['gallery_images'])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    try {
                        $file = [
                            'tmp_name' => $tmpName,
                            'name' => $_FILES['gallery_images']['name'][$key],
                            'size' => $_FILES['gallery_images']['size'][$key]
                        ];
                        $galleryImages[] = FileUpload::upload($file, 'uploads/posts/gallery');
                    } catch (\Exception $e) {
                        // Hata durumunda devam et
                    }
                }
            }
        }
        $postData['gallery_images'] = $galleryImages;
        
        $newPostId = Post::createPost($postData);
        
        Cache::forget('posts_list');
        
        Logger::info('Post created', [
            'post_id' => $newPostId,
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
        
        $post = Post::findPost($id);
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
        
        $allowedFields = ['title', 'slug', 'content', 'short_description', 'status', 'is_featured', 'comment_enabled', 'order'];
        $request = array_intersect_key($_POST, array_flip($allowedFields));
        $request['id'] = $postId;
        
        // Input sanitization
        $request = array_map('trim', $request);
        $request['title'] = htmlspecialchars($request['title'] ?? '');
        $request['short_description'] = htmlspecialchars($request['short_description'] ?? '');
        
        $errors = validate($request, [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required|in:draft,published',
        ]);

        if ($errors) {
            $post = Post::findPost($postId);
            View::render('posts/edit', [
                'errors' => $errors,
                'post' => (object) array_merge((array) $post, $request)
            ]);
            return;
        }

        $data = $request;
        unset($data['id']);
        
        // Handle checkboxes
        $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $data['comment_enabled'] = isset($_POST['comment_enabled']) ? 1 : 0;
        
        // Kapak resmi yükleme
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $data['cover_image'] = FileUpload::upload($_FILES['cover_image'], 'uploads/posts');
            } catch (\Exception $e) {
                $post = Post::findPost($postId);
                View::render('posts/edit', [
                    'errors' => ['cover_image' => [$e->getMessage()]],
                    'post' => (object) array_merge((array) $post, $request)
                ]);
                return;
            }
        }

        $post = Post::findPost($postId);
        if (!$post) {
            flash('error', 'Post bulunamadı!');
            redirect('admin.posts.index');
            return;
        }
        
        Post::updatePost($postId, $data);
        
        Cache::forget('posts_list');
        
        Logger::info('Post updated', [
            'post_id' => $postId,
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
        
        $post = Post::findPost($id);
        if (!$post) {
            flash('error', 'Post bulunamadı!');
            redirect('admin.posts.index');
            return;
        }
        
        Post::deletePost($id);
        
        Cache::forget('posts_list');
        
        Logger::warning('Post deleted', [
            'deleted_post_id' => $id,
            'deleted_by' => $_SESSION['user_id'] ?? 'unknown'
        ]);
        
        flash('success', 'Post başarıyla silindi!');
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