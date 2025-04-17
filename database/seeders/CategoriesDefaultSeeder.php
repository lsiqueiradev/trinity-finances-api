<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesDefaultSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultCategories = array(
            [
                'name' => 'Ajuste de saldo*',
                'type' => 'expense',
                'color' => '#ff4444',
                'icon' => 'Ratio',
                'is_system' => true,
                'code' => 'adjustment_balance',
            ],
            [
                'name' => 'Ajuste de saldo*',
                'type' => 'income',
                'color' => '#669900',
                'icon' => 'Ratio',
                'is_system' => true,
                'code' => 'adjustment_balance',
            ],
            [
                'name' => 'Agrupada Cartão',
                'type' => 'expense',
                'color' => '#009688',
                'icon' => 'Group',
                'is_system' => true,
                'code' => 'grouped_card',
            ],
            [
                'name' => 'Estorno*',
                'type' => 'income',
                'color' => '#669900',
                'icon' => 'Banknote',
                'is_system' => true,
                'code' => 'reversal',
            ],
        );

        foreach ($defaultCategories as $category) {
            Category::firstOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    'color' => $category['color'],
                    'is_system' => $category['is_system'],
                    'code' => $category['code'],
                    'icon' => $category['icon'],
                ]
            );
        };
    }
}
