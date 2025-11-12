<?php

namespace App\Models;

use Core\Model;
use Core\Relations\BelongsToRelation;

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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}