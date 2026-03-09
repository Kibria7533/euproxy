<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('squid_users', function (Blueprint $table) {
            $table->foreignId('proxy_type_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('proxy_types')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('squid_users', function (Blueprint $table) {
            $table->dropForeign(['proxy_type_id']);
            $table->dropColumn('proxy_type_id');
        });
    }
};
