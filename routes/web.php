<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/feesetup', [PaymentController::class, 'feeSetUpIndex'])->name('feesetup.index');
    Route::get('/feesetup/create', [PaymentController::class, 'feeSetUpCreate'])->name('feesetup.create');
    Route::post('/feesetup', [PaymentController::class, 'feeSetUpStore'])->name('feesetup.store');
    Route::get('/feesetup/{fee}', [PaymentController::class, 'feeSetUpEdit'])->name('feesetup.edit');
    Route::patch('/feesetup/{fee}', [PaymentController::class, 'feeSetUpUpdate'])->name('feesetup.update');
    Route::delete('/feesetup/{fee}', [PaymentController::class, 'feeSetUpDestroy'])->name('feesetup.destroy');
    Route::get('/transactions', [PaymentController::class, 'transactions'])->name('transactions');
    
    Route::get('/pay', [PaymentController::class, 'index'])->name('pay');
    Route::post('/pay', [PaymentController::class, 'initialize'])->name('pay.initialize');
    Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
