<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'sort_order' => 1],
            ['name' => 'Laptop', 'slug' => 'laptop', 'sort_order' => 2],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'sort_order' => 3],
            ['name' => 'Đồng hồ thông minh', 'slug' => 'dong-ho-thong-minh', 'sort_order' => 4],
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create subcategories
        $phoneCategory = Category::where('slug', 'dien-thoai')->first();
        if ($phoneCategory) {
            Category::create([
                'name' => 'iPhone',
                'slug' => 'iphone',
                'parent_id' => $phoneCategory->id,
                'sort_order' => 1,
            ]);

            Category::create([
                'name' => 'Samsung',
                'slug' => 'samsung',
                'parent_id' => $phoneCategory->id,
                'sort_order' => 2,
            ]);
        }
    }
}
