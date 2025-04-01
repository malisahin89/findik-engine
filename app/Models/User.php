<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
