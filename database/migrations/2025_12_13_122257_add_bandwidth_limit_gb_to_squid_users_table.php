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
        Schema::table('squid_users', function (Blueprint $table) {
            $table->decimal('bandwidth_limit_gb', 10, 2)->nullable()->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squid_users', function (Blueprint $table) {
            $table->dropColumn('bandwidth_limit_gb');
        });
    }
};
