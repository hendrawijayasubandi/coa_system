<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\COAController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ProfitLossController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('kategori', KategoriController::class);
    Route::resource('coa', COAController::class);
    Route::get('/kategori-list', [COAController::class, 'getKategori'])->name('kategori.list');

    Route::resource('transaksi', TransaksiController::class);

    Route::get('/profit-loss', [ProfitLossController::class, 'index'])->name('profit.loss');
    Route::get('/profit-loss/export', [ProfitLossController::class, 'export'])->name('profit.loss.export');
});
