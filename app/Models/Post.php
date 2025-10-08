<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'short_description',
        'cover_image',
        'gallery_images',
        'status',
        'is_featured',
        'comment_enabled',
        'order',
        'user_id'
    ];
    
    protected $hidden = [];
    
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    // Gallery images JSON olarak saklanıyor
    public function getGalleryImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
    
    public function setGalleryImagesAttribute($value)
    {
        $this->attributes['gallery_images'] = json_encode($value);
    }
    
    // Scope: Yayınlanan postlar
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    // Scope: Öne çıkan postlar
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }
    
    // Relation: Yazar
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Custom methods
    public static function getAllPosts()
    {
        return DB::table('posts')->orderBy('created_at', 'desc')->get();
    }
    
    public static function findPost($id)
    {
        return DB::table('posts')->where('id', $id)->first();
    }
    
    public static function createPost($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        if (isset($data['gallery_images'])) {
            $data['gallery_images'] = json_encode($data['gallery_images']);
        }
        
        return DB::table('posts')->insertGetId($data);
    }
    
    public static function updatePost($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        if (isset($data['gallery_images'])) {
            $data['gallery_images'] = json_encode($data['gallery_images']);
        }
        
        return DB::table('posts')->where('id', $id)->update($data);
    }
    
    public static function deletePost($id)
    {
        return DB::table('posts')->where('id', $id)->delete();
    }
}