<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'Premium customizable mechanical keyboards with outstanding sound profiles and typing feel.',
            ],
            [
                'name' => 'Keycaps',
                'description' => 'High quality cherry, OEM, and KAT profile keycap sets in gorgeous colorways.',
            ],
            [
                'name' => 'Deskmat',
                'description' => 'Aesthetic large desk mats with stitched edges and premium tracking surfaces.',
            ],
            [
                'name' => 'Mouse',
                'description' => 'Ergonomic, high-precision gaming and productivity wireless mice.',
            ],
            [
                'name' => 'Monitor Stand',
                'description' => 'Solid wooden and aluminum monitor stands to elevate your viewing height and organize your desk.',
            ],
            [
                'name' => 'Cable Management',
                'description' => 'Sleek routing trays, clips, and premium custom coiled keyboard cables.',
            ],
            [
                'name' => 'Workspace Accessories',
                'description' => 'Minimalist felt desk pads, light bars, macro pads, and wooden trays.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
