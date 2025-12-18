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
        Schema::create('proxy_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('proxy_order_id')->constrained('proxy_orders')->onDelete('cascade');
            $table->foreignId('proxy_type_id')->constrained('proxy_types')->onDelete('cascade');
            $table->decimal('bandwidth_total_gb', 10, 2);
            $table->bigInteger('bandwidth_remaining_bytes')->default(0);
            $table->decimal('bandwidth_used_gb', 10, 2)->default(0);
            $table->enum('status', ['active', 'depleted', 'expired', 'expired_with_balance', 'suspended', 'cancelled'])->default('active');
            $table->enum('auto_renew', ['enabled', 'disabled'])->default('disabled');
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('notify_low_bandwidth')->default(true);
            $table->timestamps();

            // Critical indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['proxy_type_id', 'status']);
            $table->index('expires_at');
            $table->index('bandwidth_remaining_bytes');
            $table->index(['user_id', 'status', 'bandwidth_remaining_bytes'], 'idx_subscription_active_bandwidth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_subscriptions');
    }
};
