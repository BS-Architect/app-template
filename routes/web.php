<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/pages', function () {
    return 'Pages admin stub';
})->name('admin.pages.index');

Route::get('/admin/catalog', function () {
    return 'Catalog admin stub';
})->name('admin.catalog.index');
