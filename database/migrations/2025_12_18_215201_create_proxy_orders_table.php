<?php

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
        Schema::create('proxy_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('proxy_plan_id')->constrained('proxy_plans')->onDelete('cascade');
            $table->foreignId('proxy_type_id')->constrained('proxy_types')->onDelete('cascade');
            $table->decimal('bandwidth_gb', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['stripe', 'paypal', 'manual'])->nullable();
            $table->string('payment_transaction_id', 255)->nullable();
            $table->text('payment_details')->nullable();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'payment_status']);
            $table->index(['payment_status', 'created_at']);
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_orders');
    }
};
