<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table): void {
            $table->id();
            $table->string('house_code')->unique();
            $table->string('block');
            $table->string('house_number');
            $table->string('head_of_family');
            $table->string('kk_number')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('occupancy_status')->default('occupied');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['block', 'house_number']);
            $table->index('occupancy_status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
