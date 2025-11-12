<?php

namespace App\Models;

use Core\Model;
use Core\Relations\HasManyRelation;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'surname',
        'username',
        'profile_image',
        'bio',
        'status',
        'email',
        'password'
    ];
    
    // Güvenlik: Hassas alanları gizle
    protected $hidden = [
        'password',
        'remember_token'
    ];
    
    // Otomatik tarih alanları
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    // Mutator: Şifre otomatik hash
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
        }
    }
    
    // Accessor: Tam ad
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->surname;
    }
    
    // Scope: Aktif kullanıcılar
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // Relation: Posts
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }
}
