<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Cotton Bath Towels (Draps)',
            'sku' => 'RIAD-TWL-001',
            'category' => 'Housekeeping Supplies',
            'supplier' => 'Local Textile Supplier',
            'quantity' => 100,
            'price' => 120, // cost price in MAD per towel
            'image' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);

        Product::create([
            'name' => 'Liquid Hand Soap',
            'sku' => 'RIAD-SOP-001',
            'category' => 'Cleaning Supplies',
            'supplier' => 'Moroccan Soap Company',
            'quantity' => 50, // bottles in stock
            'price' => 35, // price per bottle in MAD
            'image' => 'https://images.unsplash.com/photo-1572374787012-65e4469ca8d0?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);

        Product::create([
            'name' => 'Fouta Towels (Traditional Moroccan Towels)',
            'sku' => 'RIAD-FTA-001',
            'category' => 'Housekeeping Supplies',
            'supplier' => 'Local Artisan Supplier',
            'quantity' => 150, // towels in stock
            'price' => 90, // price per towel in MAD
            'image' => 'https://images.unsplash.com/photo-1552456851-8c79593f6665?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);

        Product::create([
            'name' => 'Traditional Moroccan Pouf',
            'sku' => 'RIAD-POU-001',
            'category' => 'Decor & Furniture',
            'supplier' => 'Local Artisan Supplier',
            'quantity' => 30,
            'price' => 650, // price per pouf in MAD
            'image' => 'https://images.unsplash.com/photo-1601972538692-6d58c80f0e94?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);

        Product::create([
            'name' => 'Handwoven Moroccan Tapis (Rug)',
            'sku' => 'RIAD-TAP-001',
            'category' => 'Decor & Furniture',
            'supplier' => 'Local Artisan Supplier',
            'quantity' => 20,
            'price' => 1500, // price per rug in MAD
            'image' => 'https://images.unsplash.com/photo-1505692952040-0bb72a3d5ac0?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);

        Product::create([
            'name' => 'Traditional Moroccan Tajine Pot',
            'sku' => 'RIAD-TAJ-001',
            'category' => 'Kitchen & Dining',
            'supplier' => 'Local Pottery Workshop',
            'quantity' => 40,
            'price' => 250, // price per tajine in MAD
            'image' => 'https://images.unsplash.com/photo-1577044284827-56c52734d8fa?w=300&h=300&fit=crop',
            'last_updated' => now()->toDateString(),
        ]);
    }
}
