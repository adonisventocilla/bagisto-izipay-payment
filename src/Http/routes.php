<?php

use Illuminate\Support\Facades\Route;
use AVS\Izipay\Http\Controllers\IzipayController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('izipay')->group(function () {

        Route::get('/redirect', [IzipayController::class, 'redirect'])->name('izipay.payment.redirect');

        Route::get('/success', [IzipayController::class, 'success'])->name('izipay.payment.success');

        Route::get('/error', [IzipayController::class, 'failure'])->name('izipay.payment.error');

        Route::get('/refused', [IzipayController::class, 'refused'])->name('izipay.payment.refused');

        Route::get('/cancel', [IzipayController::class, 'cancel'])->name('izipay.payment.cancel');
    });
});

Route::post('izipay/ipn', [IzipayController::class, 'ipn'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('izipay.payment.ipn');