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
        $userCategories = array(
            [
                'name' => 'Assinaturas',
                'type' => 'expense',
                'color' => '#fdd835',
                'icon' => 'Podcast',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Carro',
                'type' => 'expense',
                'color' => '#a5009f',
                'icon' => 'Car',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Casa',
                'type' => 'expense',
                'color' => '#0099cc',
                'icon' => 'House',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Compras',
                'type' => 'expense',
                'color' => '#ff4444',
                'icon' => 'Store',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Educação',
                'type' => 'expense',
                'color' => '#9933cc',
                'icon' => 'BookOpen',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Eletrônicos',
                'type' => 'expense',
                'color' => '#ffbd21',
                'icon' => 'Monitor',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Faturas',
                'type' => 'expense',
                'color' => '#930101',
                'icon' => 'CreditCard',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Investimento',
                'type' => 'expense',
                'color' => '#669900',
                'icon' => 'ChartLine',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Lazer',
                'type' => 'expense',
                'color' => '#ff8a00',
                'icon' => 'TreePalm',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Outros',
                'type' => 'expense',
                'color' => '#686868',
                'icon' => 'Ellipsis',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Resturante',
                'type' => 'expense',
                'color' => '#cc0000',
                'icon' => 'UtensilsCrossed',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Saúde',
                'type' => 'expense',
                'color' => '#669900',
                'icon' => 'BriefcaseMedical',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Serviços',
                'type' => 'expense',
                'color' => '#004e09',
                'icon' => 'NotepadText',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Supermercado',
                'type' => 'expense',
                'color' => '#ff4444',
                'icon' => 'ShoppingCart',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Transporte',
                'type' => 'expense',
                'color' => '#2a23ff',
                'icon' => 'BusFront',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Vestuário',
                'type' => 'expense',
                'color' => '#a5119f',
                'icon' => 'Shirt',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Viagem',
                'type' => 'expense',
                'color' => '#2cb1e1',
                'icon' => 'Plane',
                'is_system' => false,
                'code' => null,
            ],

            [
                'name' => 'Férias',
                'type' => 'income',
                'color' => '#669900',
                'icon' => 'Banknote',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Investimento',
                'type' => 'income',
                'color' => '#9933cc',
                'icon' => 'ChartLine',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Outros',
                'type' => 'income',
                'color' => '#cc0000',
                'icon' => 'Ellipsis',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Prêmio',
                'type' => 'income',
                'color' => '#ff8a00',
                'icon' => 'Gem',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Presente',
                'type' => 'income',
                'color' => '#ff8a00',
                'icon' => 'Gift',
                'is_system' => false,
                'code' => null,
            ],
            [
                'name' => 'Salário',
                'type' => 'income',
                'color' => '#669900',
                'icon' => 'Banknote',
                'is_system' => false,
                'code' => null,
            ],
        );

        foreach ($userCategories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type'], 'user_id' => $userId],
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
