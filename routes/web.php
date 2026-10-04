<?php

declare(strict_types=1);

use App\Http\Controllers\PublicBillingCheckController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/cek-tagihan', [PublicBillingCheckController::class, 'search'])
    ->name('public.billing.check');

Route::get('/receipt/{payment}', [ReceiptController::class, 'show'])
    ->name('receipt.print');
