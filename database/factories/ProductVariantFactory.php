<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'lampu' => [
                'daya' => ['5W', '10W', '20W', '30W', '50W'],
                'tegangan' => ['220V'],
                'warna_cahaya' => ['Warm White', 'Cool White', 'Daylight'],
            ],
            'kabel' => [
                'jenis' => ['NYA', 'NYM', 'NYY'],
                'ukuran' => ['1.5mm', '2.5mm', '4mm'],
                'inti' => ['1', '2', '3'],
                'panjang' => ['50m', '100m'],
            ],
            'mcb' => [
                'arus' => ['2A', '4A', '6A', '10A', '16A', '20A', '32A'],
                'curve' => ['B', 'C', 'D'],
            ],
        ];

        $type = fake()->randomElement(array_keys($categories));
        $specs = $categories[$type];

        $options = [];

        foreach ($specs as $key => $values) {
            $options[$key] = fake()->randomElement($values);
        }

        //  Generate nama variant 
        $nameParts = array_values($options);
        $name = strtoupper($type) . ' - ' . implode(' - ', $nameParts);

        //  SKU generate
        $sku = strtoupper($type) . '-' . implode('-', array_map(function ($val) {
            return str_replace([' ', '.'], '', $val);
        }, $nameParts));
        $sku .= '-' . strtoupper(Str::random(4));

        $price = fake()->numberBetween(10000, 500000);

        return [
            'product_id' => Product::factory(),
            'sku' => $sku,
            'name' => $name,
            'options' => json_encode($options),

            'price' => $price,
            'compare_price' => fake()->boolean(30) ? (int) ($price * 1.2) : null,

            'stock_quantity' => fake()->numberBetween(10, 500),
            'stock_status' => fake()->randomElement(['in_stock', 'in_stock', 'out_of_stock']),

            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];

        // default e-commerce fake data generate(factory)
        // $color = fake()->randomElement(['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow']);
        // $size = fake()->randomElement(['Small', 'Medium', 'Large', 'XL', 'XXL']);
        // $name = "$color - $size";
        // $price = fake()->randomFloat(2, 15, 600);

        // return [
        //     'product_id' => Product::factory(),
        //     'sku' => 'VAR-' . strtoupper(Str::random(8)),
        //     'name' => $name,
        //     'options' => json_encode(['color' => $color, 'size' => $size]),
        //     'price' => $price,
        //     'compare_price' => fake()->boolean(30) ? $price * 1.3 : null,
        //     'stock_quantity' => fake()->numberBetween(0, 100),
        //     'stock_status' => fake()->randomElement(['in_stock', 'in_stock', 'out_of_stock']),
        //     'is_active' => true,
        //     'sort_order' => fake()->numberBetween(0, 10),
        // ];
    }
}
