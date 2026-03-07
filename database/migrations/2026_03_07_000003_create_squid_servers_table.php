<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('squid_servers', function (Blueprint $table) {
            $table->id();
            $table->string('hostname', 255)->nullable()->comment('e.g. proxy.euproxy.com');
            $table->string('ip', 45)->comment('IPv4 or IPv6 address');
            $table->unsignedSmallInteger('port')->default(3128);
            $table->string('location', 100)->nullable()->comment('e.g. Frankfurt, DE');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('squid_servers');
    }
};
