<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatis generate tagihan rutin bulanan setiap tanggal 1 awal bulan
Schedule::command('billing:generate')->monthlyOn(1, '01:00');

// Otomatis periksa tagihan jatuh tempo setiap hari pukul 00:30
Schedule::command('billing:check-overdue')->dailyAt('00:30');

// Otomatis verifikasi dan lengkapi invoice proyek pembangunan aktif setiap hari pukul 01:30
Schedule::command('construction:check --fix')->dailyAt('01:30');
