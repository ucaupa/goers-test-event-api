<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::query()->create(['name' => 'Permata Virtual Account', 'key' => 'permata', 'type' => 'bank_transfer', 'group' => 'Bank Transfer', 'payload' => 'data/payment/bank_transfer/permata.json']);
        PaymentMethod::query()->create(['name' => 'BCA Virtual Account', 'key' => 'bca', 'type' => 'bank_transfer', 'group' => 'Bank Transfer', 'payload' => 'data/payment/bank_transfer/bca.json']);
        PaymentMethod::query()->create(['name' => 'Mandiri Bill Payment', 'key' => 'mandiri', 'type' => 'echannel', 'group' => 'Bank Transfer', 'payload' => 'data/payment/bank_transfer/mandiri.json']);
        PaymentMethod::query()->create(['name' => 'BNI Virtual Account', 'key' => 'bni', 'type' => 'bank_transfer', 'group' => 'Bank Transfer', 'payload' => 'data/payment/bank_transfer/bni.json']);
        PaymentMethod::query()->create(['name' => 'BRI Virtual Account', 'key' => 'bri', 'type' => 'bank_transfer', 'group' => 'Bank Transfer', 'payload' => 'data/payment/bank_transfer/bri.json']);
    }
}
