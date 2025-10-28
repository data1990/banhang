<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'sku' => 'IP15PM-256',
                'price' => 29990000,
                'sale_price' => 28990000,
                'stock' => 50,
                'short_desc' => 'iPhone 15 Pro Max 256GB - Thiết kế titan cao cấp',
                'long_desc' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP chuyên nghiệp và thiết kế titan bền bỉ.',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'sku' => 'SGS24U-512',
                'price' => 27990000,
                'stock' => 30,
                'short_desc' => 'Samsung Galaxy S24 Ultra 512GB - S Pen tích hợp',
                'long_desc' => 'Galaxy S24 Ultra với S Pen tích hợp, camera 200MP và màn hình Dynamic AMOLED 2X.',
            ],
            [
                'name' => 'MacBook Pro M3',
                'slug' => 'macbook-pro-m3',
                'sku' => 'MBP-M3-512',
                'price' => 45990000,
                'stock' => 20,
                'short_desc' => 'MacBook Pro 14" M3 chip - Hiệu năng vượt trội',
                'long_desc' => 'MacBook Pro với chip M3 mới nhất, màn hình Liquid Retina XDR và thời lượng pin lên đến 18 giờ.',
            ],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'sku' => 'APP2-USB-C',
                'price' => 5990000,
                'sale_price' => 5490000,
                'stock' => 100,
                'short_desc' => 'AirPods Pro 2 với USB-C - Chống ồn chủ động',
                'long_desc' => 'AirPods Pro thế hệ 2 với chip H2, chống ồn chủ động và thời lượng pin lên đến 6 giờ.',
            ],
            [
                'name' => 'Apple Watch Series 9',
                'slug' => 'apple-watch-series-9',
                'sku' => 'AWS9-45MM',
                'price' => 8990000,
                'stock' => 75,
                'short_desc' => 'Apple Watch Series 9 45mm - GPS + Cellular',
                'long_desc' => 'Apple Watch Series 9 với chip S9, màn hình Always-On Retina và theo dõi sức khỏe toàn diện.',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                ...$productData,
                'category_id' => $categories->random()->id,
                'is_active' => true,
            ]);

            // Only create placeholder if no real images exist
            if ($product->images()->count() === 0) {
                $product->images()->create([
                    'path' => 'products/placeholder.jpg',
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
