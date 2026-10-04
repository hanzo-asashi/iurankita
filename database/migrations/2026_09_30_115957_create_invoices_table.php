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
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('household_id')->constrained('households')->cascadeOnDelete();
            $table->string('invoice_type');
            $table->string('billing_period', 7)->nullable(); // YYYY-MM
            $table->date('issue_date');
            $table->date('due_date');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->unsignedBigInteger('balance');
            $table->string('status')->default('unpaid');
            $table->foreignId('construction_project_id')->nullable()->constrained('construction_projects')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['household_id', 'invoice_type', 'billing_period'], 'household_monthly_unique');
            $table->unique(['construction_project_id', 'invoice_type'], 'construction_project_invoice_unique');
            $table->index(['invoice_type', 'status']);
            $table->index(['billing_period', 'status']);
        });

        // Add foreign key constraint for construction_projects.fee_invoice_id
        Schema::table('construction_projects', function (Blueprint $table): void {
            $table->foreign('fee_invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table): void {
            $table->dropForeign(['fee_invoice_id']);
        });

        Schema::dropIfExists('invoices');
    }
};
