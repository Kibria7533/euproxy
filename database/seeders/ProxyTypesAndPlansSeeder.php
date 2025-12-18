<?php

namespace Database\Seeders;

use App\Models\ProxyType;
use App\Models\ProxyPlan;
use App\Models\ProxyPlanFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProxyTypesAndPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Proxy Types
        $proxyTypes = [
            [
                'name' => 'Rotating Residential Proxies',
                'slug' => 'rotating-residential',
                'description' => 'High-quality rotating residential proxies with automatic IP rotation for maximum anonymity.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Unlimited Residential Proxies',
                'slug' => 'unlimited-residential',
                'description' => 'Unlimited bandwidth residential proxies with no restrictions on traffic.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Static Residential Proxies',
                'slug' => 'static-residential',
                'description' => 'Dedicated static residential IPs for consistent sessions and stable connections.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Datacenter Proxies',
                'slug' => 'datacenter',
                'description' => 'High-speed datacenter proxies optimized for performance and throughput.',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($proxyTypes as $typeData) {
            $proxyType = ProxyType::create($typeData);

            // Create plans for each proxy type
            $this->createPlansForType($proxyType);
        }
    }

    /**
     * Create plans for a specific proxy type
     */
    private function createPlansForType(ProxyType $proxyType): void
    {
        $plans = [
            [
                'name' => '1 GB Plan',
                'bandwidth_gb' => 1.00,
                'base_price' => 5.00,
                'discount_percentage' => 0,
                'is_popular' => false,
                'is_free_trial' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '2 GB Plan',
                'bandwidth_gb' => 2.00,
                'base_price' => 30.00,
                'discount_percentage' => 20.00,
                'is_popular' => false,
                'is_free_trial' => false,
                'sort_order' => 2,
            ],
            [
                'name' => '5 GB Plan',
                'bandwidth_gb' => 5.00,
                'base_price' => 60.00,
                'discount_percentage' => 20.00,
                'is_popular' => false,
                'is_free_trial' => false,
                'sort_order' => 3,
            ],
            [
                'name' => '20 GB Plan',
                'bandwidth_gb' => 20.00,
                'base_price' => 200.00,
                'discount_percentage' => 20.00,
                'is_popular' => true,
                'is_free_trial' => false,
                'sort_order' => 4,
            ],
            [
                'name' => '50 GB Plan',
                'bandwidth_gb' => 50.00,
                'base_price' => 350.00,
                'discount_percentage' => 20.00,
                'is_popular' => false,
                'is_free_trial' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = $proxyType->plans()->create($planData);

            // Create features for each plan
            $this->createFeaturesForPlan($plan);
        }
    }

    /**
     * Create features for a specific plan
     */
    private function createFeaturesForPlan(ProxyPlan $plan): void
    {
        $features = [
            [
                'feature_key' => 'max_requests_per_second',
                'feature_value' => $plan->bandwidth_gb >= 20 ? '50' : '50',
                'display_label' => $plan->bandwidth_gb >= 20 ? '50 requests/s' : '50 requests/s',
                'sort_order' => 1,
            ],
            [
                'feature_key' => 'max_ips',
                'feature_value' => 'unlimited',
                'display_label' => 'Unlimited IPs',
                'sort_order' => 2,
            ],
            [
                'feature_key' => 'sub_accounts',
                'feature_value' => 'yes',
                'display_label' => 'Sub-accounts',
                'sort_order' => 3,
            ],
            [
                'feature_key' => 'country_selection',
                'feature_value' => 'yes',
                'display_label' => 'Country selection',
                'sort_order' => 4,
            ],
            [
                'feature_key' => 'dedicated_server',
                'feature_value' => $plan->bandwidth_gb >= 20 ? 'yes' : 'no',
                'display_label' => $plan->bandwidth_gb >= 20 ? 'Dedicated server' : 'Shared server',
                'sort_order' => 5,
            ],
        ];

        foreach ($features as $featureData) {
            $plan->features()->create($featureData);
        }
    }
}
