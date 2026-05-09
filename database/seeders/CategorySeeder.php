<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Lighting System', 'description' => 'Sistem Pencahayaan dan Lampu Berkualitas Tinggi'],
            ['name' => 'Electrical Wiring', 'description' => 'Produk instalasi listrik dan infrastruktur bangunan'],
            ['name' => 'Switches & Sockets', 'description' => 'saklar dan stop kontak untuk kebutuhan listrik rumah dan industri'],
            ['name' => 'Electrical Protection', 'description' => 'Perlindungan listrik untuk keamanan dan keandalan sistem listrik'],
            ['name' => 'Installation Materials', 'description' => 'Bahan dan perlengkapan untuk instalasi listrik yang efisien dan aman'],
            ['name' => 'Electrical Tools', 'description' => 'Alat-alat listrik untuk instalasi, perawatan, dan perbaikan sistem listrik'],
            ['name' => 'Power Distribution', 'description' => 'Peralatan distribusi listrik untuk kebutuhan industri dan komersial'],
            ['name' => 'Accessories & Components', 'description' => 'Aksesoris dan komponen listrik untuk melengkapi sistem listrik Anda'],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
                'sort_order' => $index,
                'meta_title' => $category['name'] . ' - Shop Online',
                'meta_description' => $category['description'],
            ]);
        }

        $this->command->info('Categories created successfully!');
    }
}
