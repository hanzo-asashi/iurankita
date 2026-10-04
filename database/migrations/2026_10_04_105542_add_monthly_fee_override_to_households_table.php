<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table): void {
            $table->unsignedInteger('monthly_fee_override')
                ->nullable()
                ->after('ownership_status')
                ->comment('Penyesuaian nominal tarif iuran bulanan kustom khusus unit ini (Rp)');
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table): void {
            $table->dropColumn('monthly_fee_override');
        });
    }
};
