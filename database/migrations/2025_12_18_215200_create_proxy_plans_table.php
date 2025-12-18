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
        Schema::create('proxy_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proxy_type_id')->constrained('proxy_types')->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('bandwidth_gb', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_free_trial')->default(false);
            $table->boolean('is_renewable')->default(true);
            $table->integer('validity_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['proxy_type_id', 'is_active']);
            $table->index('bandwidth_gb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_plans');
    }
};
