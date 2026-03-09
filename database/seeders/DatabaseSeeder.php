<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        $this->call(CreateAdministratorSeeder::class);

        // 2. Proxy types, plans and their features
        $this->call(ProxyTypesAndPlansSeeder::class);

        // 3. Proxy owners with subscriptions, proxy servers and squid users
        $this->call(ProxyOwnerSeeder::class);
    }
}
