<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'bypass' to the payment_gateway enum
        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_gateway ENUM('stripe', 'paypal', 'bypass') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'bypass' from the payment_gateway enum
        DB::statement("ALTER TABLE payment_transactions MODIFY COLUMN payment_gateway ENUM('stripe', 'paypal') NOT NULL");
    }
};
