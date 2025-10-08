<?php

use Core\Route;


// CSRF kontrolü olmasın isterseniz
// Route::post('/api/endpoint', 'ApiController@store')->middleware('api');

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('/admin')->group(function () {
    Route::get('/login', 'AuthController@showLogin')->name('admin.login.show');
    Route::post('/login', 'AuthController@login')->name('admin.login.do');
    Route::post('/logout', 'AuthController@logout')->middleware('auth')->name('admin.logout');
});

Route::prefix('/admin')->group(function () {
    Route::prefix('/users')->group(function () {
        Route::get('/', 'UserController@index')->middleware('auth')->name('admin.users.index');

        Route::get('/create', 'UserController@create')
            ->middleware('auth')
            ->name('admin.users.create');

        Route::post('/store', 'UserController@store')
            ->middleware('auth')
            ->name('admin.users.store');

        Route::get('/edit', 'UserController@edit')
            ->middleware('auth')
            ->name('admin.users.edit');

        Route::post('/update', 'UserController@update')
            ->middleware('auth')
            ->name('admin.users.update');

        Route::post('/delete', 'UserController@delete')
            ->middleware('auth')
            ->name('admin.users.delete');
    });
    
    Route::prefix('/posts')->group(function () {
        Route::get('/', 'PostController@index')
            ->middleware('auth')
            ->name('admin.posts.index');

        Route::get('/create', 'PostController@create')
            ->middleware('auth')
            ->name('admin.posts.create');

        Route::post('/store', 'PostController@store')
            ->middleware('auth')
            ->name('admin.posts.store');

        Route::get('/edit', 'PostController@edit')
            ->middleware('auth')
            ->name('admin.posts.edit');

        Route::post('/update', 'PostController@update')
            ->middleware('auth')
            ->name('admin.posts.update');

        Route::post('/delete', 'PostController@delete')
            ->middleware('auth')
            ->name('admin.posts.delete');
    });
});
