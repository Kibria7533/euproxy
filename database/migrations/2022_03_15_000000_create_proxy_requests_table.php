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
        Schema::create('proxy_requests', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->double('ts')->nullable();
            $table->string('client_ip', 45)->nullable();
            $table->string('username', 64)->nullable()->index();
            $table->string('method', 10)->nullable();
            $table->text('url')->nullable();
            $table->integer('status')->nullable();
            $table->bigInteger('bytes')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_requests');
    }
};
