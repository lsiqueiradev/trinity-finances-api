<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run($userId): void
    {
        $userCategories = [
            [
                'name'      => 'Assinaturas',
                'type'      => 'expense',
                'color'     => '#EAB308',
                'icon'      => 'pin',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Carro',
                'type'      => 'expense',
                'color'     => '#701A75',
                'icon'      => 'car',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Casa',
                'type'      => 'expense',
                'color'     => '#0EA5E9',
                'icon'      => 'home',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Compras',
                'type'      => 'expense',
                'color'     => '#EF4444',
                'icon'      => 'shopping-bag',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Educação',
                'type'      => 'expense',
                'color'     => '#C026D3',
                'icon'      => 'book',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Eletrônicos',
                'type'      => 'expense',
                'color'     => '#EAB308',
                'icon'      => 'chip',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Faturas',
                'type'      => 'expense',
                'color'     => '#9F1239',
                'icon'      => 'card',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Investimento',
                'type'      => 'expense',
                'color'     => '#65A30D',
                'icon'      => 'chart-vertical',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Lazer',
                'type'      => 'expense',
                'color'     => '#F97316',
                'icon'      => 'sunshade',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Outros',
                'type'      => 'expense',
                'color'     => '#686868',
                'icon'      => 'ellipsis',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Resturante',
                'type'      => 'expense',
                'color'     => '#B91C1C',
                'icon'      => 'dinner',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Saúde',
                'type'      => 'expense',
                'color'     => '#4D7C0F',
                'icon'      => 'heart-beat',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Serviços',
                'type'      => 'expense',
                'color'     => '#004e09',
                'icon'      => 'list-check',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Supermercado',
                'type'      => 'expense',
                'color'     => '#EF4444',
                'icon'      => 'shopping-card',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Transporte',
                'type'      => 'expense',
                'color'     => '#1E3A8A',
                'icon'      => 'bus',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Vestuário',
                'type'      => 'expense',
                'color'     => '#701A75',
                'icon'      => 'shirt',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Viagem',
                'type'      => 'expense',
                'color'     => '#0E7490',
                'icon'      => 'flight',
                'is_system' => false,
                'code'      => null,
            ],

            [
                'name'      => 'Investimento',
                'type'      => 'income',
                'color'     => '#4D7C0F',
                'icon'      => 'chart-vertical',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Outros',
                'type'      => 'income',
                'color'     => '#686868',
                'icon'      => 'ellipsis',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Prêmio',
                'type'      => 'income',
                'color'     => '#8B5CF6',
                'icon'      => 'gift',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Presente',
                'type'      => 'income',
                'color'     => '#D97706',
                'icon'      => 'award',
                'is_system' => false,
                'code'      => null,
            ],
            [
                'name'      => 'Salário',
                'type'      => 'income',
                'color'     => '#4D7C0F',
                'icon'      => 'currency - dollar',
                'is_system' => false,
                'code'      => null,
            ],
        ];

        foreach ($userCategories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type'], 'user_id' => $userId],
                [
                    'color'     => $category['color'],
                    'is_system' => $category['is_system'],
                    'code'      => $category['code'],
                    'icon'      => $category['icon'],
                ]
            );
        }
    }
}
