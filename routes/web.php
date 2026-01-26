<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/pages', function () {
    return 'Pages admin stub';
})->name('admin.pages.index');
