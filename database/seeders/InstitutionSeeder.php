<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run()
    {

        $banks = array(
            [
                'name' => 'Banco do Brasil',
                'logo_path' => 'bb.svg',
            ],
            [
                'name' => 'Caixa Econômica',
                'logo_path' => 'caixa.svg',
            ],
            [
                'name' => 'Banco Itaú',
                'logo_path' => 'itau.svg',
            ],
            [
                'name' => 'Banco Bradesco',
                'logo_path' => 'bradesco.svg',
            ],
            [
                'name' => 'Banco Santander',
                'logo_path' => 'santander.svg',
            ],
            [
                'name' => 'Nubank',
                'logo_path' => 'nubank.svg',
            ],
            [
                'name' => 'Btg Pactual',
                'logo_path' => 'btg.svg',
            ],
            [
                'name' => 'C6 Bank',
                'logo_path' => 'c6-bank.svg',
            ],
            [
                'name' => 'Xp Investimentos',
                'logo_path' => 'xp.svg',
            ],
            [
                'name' => 'Banco BMG',
                'logo_path' => 'bmg.svg',
            ],
            [
                'name' => 'Banco Inter',
                'logo_path' => 'inter.svg',
            ],
            [
                'name' => 'Mercado Pago',
                'logo_path' => 'mercado-pago.svg',
            ],
            [
                'name' => 'Mastercard',
                'logo_path' => 'mastercard.svg',
                'type' => 'card',
            ],
            [
                'name' => 'Visa',
                'logo_path' => 'visa.svg',
                'type' => 'card',
            ],
            [
                'name' => 'Dinners',
                'logo_path' => 'dinners.svg',
                'type' => 'card',
            ],
            [
                'name' => 'American Express',
                'logo_path' => 'amex.svg',
                'type' => 'card',
            ],
            [
                'name' => 'BNDES',
                'logo_path' => 'bndes.svg',
                'type' => 'card',
            ],
            [
                'name' => 'Hipercard',
                'logo_path' => 'hipercard.svg',
                'type' => 'card',
            ],
            [
                'name' => 'Elo',
                'logo_path' => 'elo.svg',
                'type' => 'card',
            ],
        );

        foreach ($banks as $bank) {
            Institution::factory()->create(
                [
                    'name' => $bank['name'],
                    'logo_path' => $bank['logo_path'],
                    'type' => $bank['type'] ?? 'bank',
                ]
            );
        }

    }
}
