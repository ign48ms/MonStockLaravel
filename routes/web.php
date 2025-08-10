<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;


// Article routes
Route::get('/article', [ArticleController::class, 'index'])->name('article');
Route::post('/article/store', [ArticleController::class, 'store'])->name('article.store');
Route::post('/article/update', [ArticleController::class, 'update'])->name('article.update');
Route::get('/article/delete', [ArticleController::class, 'destroy'])->name('article.delete');


Route::get('/', function () {
    require_once app_path('Legacy/function.php');
    return view('dashboard');
})->name('dashboard');


Route::get('/dashboard', function () {
    require_once app_path('Legacy/function.php');
    return view('dashboard');
})->name('dashboard');

Route::get('/achat', function () {
    require_once app_path('Legacy/function.php');
    return view('achat');
})->name('achat');

Route::get('/vente', function () {
    require_once app_path('Legacy/function.php');
    return view('vente');
})->name('vente');

Route::get('/client', function () {
    require_once app_path('Legacy/function.php');
    return view('client');
})->name('client');

Route::get('/fournisseur', function () {
    require_once app_path('Legacy/function.php');
    return view('fournisseur');
})->name('fournisseur');

Route::get('/categorie', function () {
    require_once app_path('Legacy/function.php');
    return view('categorie');
})->name('categorie');
