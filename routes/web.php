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

Route::middleware('auth')->prefix('admin/reports/print')->name('reports.print.')->group(function (): void {
    Route::get('/monthly', [App\Http\Controllers\ReportPrintController::class, 'monthly'])->name('monthly');
    Route::get('/outstanding', [App\Http\Controllers\ReportPrintController::class, 'outstanding'])->name('outstanding');
    Route::get('/payments', [App\Http\Controllers\ReportPrintController::class, 'payments'])->name('payments');
    Route::get('/construction', [App\Http\Controllers\ReportPrintController::class, 'construction'])->name('construction');
    Route::get('/expenses', [App\Http\Controllers\ReportPrintController::class, 'expenses'])->name('expenses');
    Route::get('/cash-book', [App\Http\Controllers\ReportPrintController::class, 'cashBook'])->name('cash-book');
});
