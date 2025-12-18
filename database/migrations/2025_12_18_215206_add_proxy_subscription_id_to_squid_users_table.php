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
            $table->foreignId('proxy_subscription_id')->nullable()->after('user_id')->constrained('proxy_subscriptions')->onDelete('set null');
            $table->index('proxy_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squid_users', function (Blueprint $table) {
            $table->dropForeign(['proxy_subscription_id']);
            $table->dropIndex(['proxy_subscription_id']);
            $table->dropColumn('proxy_subscription_id');
        });
    }
};
