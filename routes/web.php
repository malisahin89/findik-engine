<?php

use Core\Route;

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('/admin')->group(function () {
    Route::get('/login', 'AuthController@showLogin')->name('admin.login.show');
    Route::post('/login', 'AuthController@login')->name('admin.login.do');
    Route::get('/logout', 'AuthController@logout')->middleware('auth')->name('admin.logout');
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

        Route::get('/delete', 'UserController@delete')
            ->middleware('auth')
            ->name('admin.users.delete');
    });
});
