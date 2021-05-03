<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectToU;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//linktests

Route::middleware(['auth'])->group(function () {
    Route::get('/links', [LinkController::class, 'index'])
        ->name('links');

    Route::get('/links/create', [LinkController::class, 'create'])
        ->name('links.create');

    Route::get('/links/edit', [LinkController::class, 'edit'])
        ->name('links.edit');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('{link:slug}', RedirectToU::class)->name('redirect');