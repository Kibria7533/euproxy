<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE squid_users MODIFY bandwidth_limit_gb DECIMAL(10,3) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE squid_users MODIFY bandwidth_limit_gb DECIMAL(10,2) NULL');
    }
};
