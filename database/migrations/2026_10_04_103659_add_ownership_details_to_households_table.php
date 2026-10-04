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
            $table->string('ownership_status')->default('owner')->after('occupancy_status');
            $table->string('owner_name')->nullable()->after('ownership_status');
            $table->string('owner_phone')->nullable()->after('owner_name');
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table): void {
            $table->dropColumn([
                'ownership_status',
                'owner_name',
                'owner_phone',
            ]);
        });
    }
};
