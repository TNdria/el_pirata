<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_types')->insert([
            [
                'id' => Str::uuid(),
                'code' => 'ticket_purchase',
                'label_fr' => 'Achat de ticket',
                'direction' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'code' => 'withdrawal',
                'label_fr' => 'Retrait de gain',
                'direction' => 'debit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'code' => 'refund',
                'label_fr' => 'Remboursement',
                'direction' => 'credit',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
