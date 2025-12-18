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
        Schema::create('proxy_subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proxy_subscription_id')->constrained('proxy_subscriptions')->onDelete('cascade');
            $table->bigInteger('proxy_request_id');
            $table->foreign('proxy_request_id')->references('id')->on('proxy_requests')->onDelete('cascade');
            $table->bigInteger('bytes_consumed');
            $table->timestamp('consumed_at');
            $table->timestamps();

            // Indexes
            $table->index(['proxy_subscription_id', 'consumed_at']);
            $table->unique('proxy_request_id'); // One request charges one subscription
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_subscription_usage');
    }
};
