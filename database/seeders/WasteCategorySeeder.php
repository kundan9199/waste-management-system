<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use Illuminate\Database\Seeder;

class WasteCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Plastic',
                'color' => '#3b82f6', // blue
                'description' => 'Plastic bottles, containers, bags, etc.',
            ],
            [
                'name' => 'Organic',
                'color' => '#22c55e', // green
                'description' => 'Food waste, leaves, natural materials.',
            ],
            [
                'name' => 'Metal',
                'color' => '#64748b', // slate
                'description' => 'Cans, tins, scrap metal items.',
            ],
            [
                'name' => 'Glass',
                'color' => '#0ea5e9', // sky
                'description' => 'Glass bottles, broken glass, jars.',
            ],
            [
                'name' => 'Paper',
                'color' => '#eab308', // yellow
                'description' => 'Newspapers, cardboard, magazines.',
            ],
            [
                'name' => 'E-Waste',
                'color' => '#ef4444', // red
                'description' => 'Old electronics, batteries, cables.',
            ],
        ];

        foreach ($categories as $category) {
            WasteCategory::create($category);
        }
    }
}
