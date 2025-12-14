<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * https://wiki.squid-cache.org/ConfigExamples/Authenticate/Mysql
         * Changing column names will not work
         */
        Schema::create('squid_users', function (Blueprint $table) {
            $table->id();
            $table->string('user')->unique();
            $table->string('password');
            $table->tinyInteger('enabled')->default(1);
            $table->string('fullname')->default(null)->nullable();
            $table->string('comment')->default(null)->nullable();
            $table->decimal('bandwidth_limit_gb', 10, 2)->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable()->default(null);
            $table->bigInteger('quota_bytes')->unsigned()->default(0);
            $table->bigInteger('used_bytes')->unsigned()->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->tinyInteger('is_blocked')->default(0);
            $table->timestamp('reset_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squid_users');
    }
};
