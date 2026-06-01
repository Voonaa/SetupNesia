<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $productsData = [
            // Mechanical Keyboards
            [
                'category_slug' => 'mechanical-keyboard',
                'name' => 'Keychron Q1 Pro Wireless Keyboard',
                'description' => 'Fully customizable wireless mechanical keyboard with QMK/VIA support, a solid CNC aluminum body, hot-swappable switches, and double-shot KSA keycaps. Delivers a magnificent typing experience.',
                'price' => 2899000.00,
                'stock' => 15,
                'weight' => 1800,
                'image' => '/images/products/keyboard.jpg',
            ],
            [
                'category_slug' => 'mechanical-keyboard',
                'name' => 'GMMK Pro 75% Barebone Keyboard',
                'description' => 'An ultra-premium, gasket-mounted, 75% layout modular mechanical keyboard built for enthusiasts. Featuring a rotary encoder, RGB side lighting, and pre-lubed stabilizers.',
                'price' => 2499000.00,
                'stock' => 10,
                'weight' => 1500,
                'image' => '/images/products/keyboard_gmmk.jpg',
            ],

            // Keycaps
            [
                'category_slug' => 'keycaps',
                'name' => 'PBTfans Retro Dark Lights Keycaps',
                'description' => 'A set of high-quality doubleshot PBT keycaps. Features classic dark keycaps with retro neon accents. High durability with standard Cherry profile support.',
                'price' => 1350000.00,
                'stock' => 8,
                'weight' => 450,
                'image' => '/images/products/keycaps.jpg',
            ],
            [
                'category_slug' => 'keycaps',
                'name' => 'GMK Laser Custom Keycap Set',
                'description' => 'Iconic cyber-themed double-shot ABS keycap set designed by MiTo. Cyberpunk-inspired aesthetic with vivid neon purples, blues, and pinks.',
                'price' => 2199000.00,
                'stock' => 5,
                'weight' => 500,
                'image' => '/images/products/keycaps_laser.jpg',
            ],

            // Deskmats
            [
                'category_slug' => 'deskmat',
                'name' => 'SetupNesia Topographic Dark Deskmat',
                'description' => 'Elevate your desk setups with our signature dark topographic patterns. Designed with precision micro-woven cloth, anti-slip rubber base, and professional double-stitched edges.',
                'price' => 249000.00,
                'stock' => 50,
                'weight' => 700,
                'image' => '/images/products/deskmat.jpg',
            ],
            [
                'category_slug' => 'deskmat',
                'name' => 'Kanagawa Wave Minimalist Deskmat',
                'description' => 'Beautiful Japanese-inspired wave desk mat. Soft tracking surface optimized for optical and laser mice with a clean aesthetic.',
                'price' => 249000.00,
                'stock' => 30,
                'weight' => 700,
                'image' => '/images/products/deskmat_wave.jpg',
            ],

            // Mice
            [
                'category_slug' => 'mouse',
                'name' => 'Logitech G Pro X Superlight 2 Wireless',
                'description' => 'The ultimate esports wireless mouse, now lighter and faster. Features a HERO 2 sensor with 32,000 DPI tracking and hybrid optical-mechanical switches.',
                'price' => 2249000.00,
                'stock' => 12,
                'weight' => 60,
                'image' => '/images/products/mouse.jpg',
            ],
            [
                'category_slug' => 'mouse',
                'name' => 'Logitech MX Master 3S Ergonomic Mouse',
                'description' => 'The legendary productivity wireless mouse with MagSpeed electromagnetic scrolling, silent click switches, and an 8,000 DPI track-on-glass sensor.',
                'price' => 1499000.00,
                'stock' => 20,
                'weight' => 141,
                'image' => '/images/products/mouse_mx.jpg',
            ],

            // Monitor Stands
            [
                'category_slug' => 'monitor-stand',
                'name' => 'SetupNesia Solid Walnut Monitor Riser',
                'description' => 'Handcrafted solid walnut wood monitor riser. Elevates your monitor to the perfect ergonomic height while providing space underneath for keyboard storage.',
                'price' => 899000.00,
                'stock' => 7,
                'weight' => 2500,
                'image' => '/images/products/monitor_stand.jpg',
            ],

            // Cable Management
            [
                'category_slug' => 'cable-management',
                'name' => 'Premium Custom Coiled Type-C Keyboard Cable',
                'description' => 'Double-sleeved custom coiled cable with detachable GX16 aviator connector. Paracord and Techflex design with premium purple and blue colorways.',
                'price' => 299000.00,
                'stock' => 25,
                'weight' => 200,
                'image' => '/images/products/cable.jpg',
            ],

            // Workspace Accessories
            [
                'category_slug' => 'workspace-accessories',
                'name' => 'Premium Merino Wool Felt Desk Pad',
                'description' => 'Luxurious minimalist felt desk pad made from premium merino wool. Soft, warm, and highly tactile texture that transforms your desktop aesthetics.',
                'price' => 399000.00,
                'stock' => 35,
                'weight' => 600,
                'image' => '/images/products/deskpad.jpg',
            ],
            [
                'category_slug' => 'workspace-accessories',
                'name' => 'LED Screenbar Monitor Smart Lightbar',
                'description' => 'Monitor light bar with auto-dimming sensor and hue adjustment. Asymmetrical optical design eliminates screen glare and illuminates your working space.',
                'price' => 649000.00,
                'stock' => 18,
                'weight' => 800,
                'image' => '/images/products/lightbar.jpg',
            ],
        ];

        foreach ($productsData as $data) {
            $cat = $categories->get($data['category_slug']);
            if (!$cat) continue;

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $cat->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'weight' => $data['weight'],
                    'is_active' => true,
                ]
            );

            // Add primary image
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'image_path' => $data['image'],
                ],
                [
                    'is_primary' => true,
                ]
            );
        }
    }
}
