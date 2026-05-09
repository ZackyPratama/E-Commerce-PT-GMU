<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Schneider Electric',
            'Philips',
            'Panasonic',
            'Siemens',
            'ABB',
            'Legrand',
            'Osram',
            'Mitsubishi Electric',
            'Hager',
            'Chint',
            'Eaton',
            'Honeywell',
        ];

        foreach ($brands as $index => $brandName) {
            Brand::create([
                'name' => $brandName,
                'slug' => \Illuminate\Support\Str::slug($brandName),
                'description' => "Quality products from {$brandName}",
                'website' => "https://www.{$brandName}.com",
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        $this->command->info('Brands created successfully!');
    }
}
