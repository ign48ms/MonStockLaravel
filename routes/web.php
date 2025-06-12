<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test-db', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return "Database connection successful!";
    } catch (\Exception $e) {
        return "Database connection failed: " . $e->getMessage();
    }
});

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

Route::get('/article', function () {
    require_once app_path('Legacy/function.php');
    return view('article');
})->name('article');

Route::get('/categorie', function () {
    require_once app_path('Legacy/function.php');
    return view('categorie');
})->name('categorie');
