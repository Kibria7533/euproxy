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
        Schema::create('proxy_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proxy_plan_id')->constrained('proxy_plans')->onDelete('cascade');
            $table->string('feature_key', 50);
            $table->string('feature_value', 100);
            $table->string('display_label', 200);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('proxy_plan_id');
            $table->unique(['proxy_plan_id', 'feature_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_plan_features');
    }
};
