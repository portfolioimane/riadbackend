<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSettings;

class PaymentSettingsSeeder extends Seeder
{
    public function run()
    {
        PaymentSettings::create([
            'type' => 'stripe',
            'public_key' => null,
            'secret_key' => null,
            'api_url' => null,
            'enabled' => false,
        ]);

        PaymentSettings::create([
            'type' => 'paypal',
            'public_key' => null,
            'secret_key' => null,
            'api_url' => 'https://api-m.sandbox.paypal.com',
            'enabled' => false,
        ]);
    }
}
