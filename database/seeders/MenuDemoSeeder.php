<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
class MenuDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            'Ana Yemekler' => [
                ['name' => 'Izgara Tavuk', 'price' => 180],
                ['name' => 'Köfte Porsiyon', 'price' => 195],
            ],
            'İçecekler' => [
                ['name' => 'Ayran', 'price' => 25],
                ['name' => 'Kola', 'price' => 35],
            ],
            'Tatlılar' => [
                ['name' => 'Sütlaç', 'price' => 70],
                ['name' => 'Baklava (3 dilim)', 'price' => 110],
            ],
        ];
        foreach ($cats as $catName => $prods) {
            $c = Category::firstOrCreate(['name' => $catName]);
            foreach ($prods as $p) {
                Product::firstOrCreate([
                    'name' => $p['name'],
                    'category_id' => $c->id
                ], [
                    'price' => $p['price'],
                    'stock' => 999999
                ]);
            }
        }
    }
}
