<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_gateway ENUM('stripe', 'paypal', 'bypass') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_gateway ENUM('stripe', 'paypal') NOT NULL");
    }
};
