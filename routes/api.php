<?php

use Illuminate\Support\Facades\Route;
use Nurdaulet\FluxWallet\Http\Controllers\PaymentController;
use Nurdaulet\FluxWallet\Http\Controllers\BankcardController;
use Nurdaulet\FluxWallet\Http\Controllers\TransactionController;

Route::prefix('api')->group(function () {

    Route::group(['prefix' => 'bankcards', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [BankcardController::class, 'index']);
        Route::get('url', [BankcardController::class, 'getRedirectLink']);
        Route::post('/{id}/top-up', [BankcardController::class, 'topUp']);
        Route::delete('/{id}', [BankcardController::class, 'destroy']);
    });
    Route::get('transactions', [TransactionController::class, 'index'])->middleware('auth:sanctum');

    Route::group(['prefix' => 'payments'], function () {
        Route::get('success', [PaymentController::class, 'success'])->name('rsuccess');
        Route::get('error', [PaymentController::class, 'error'])->name('error');
        Route::post('epay/callback', [PaymentController::class, 'epayCallback']);
    });
});
