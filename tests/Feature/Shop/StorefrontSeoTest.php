<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSeoTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create([
            'name' => 'Mechanical Keyboard',
            'slug' => 'mechanical-keyboard',
        ]);

        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Keychron Q1 Pro',
            'slug' => 'keychron-q1-pro',
            'description' => 'A custom mechanical keyboard with wireless capability and a full solid aluminum body for enthusiasts.',
            'price' => 2500000.00,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $this->product->id,
            'image_path' => '/images/products/keychron_q1_pro.png',
            'is_primary' => true,
        ]);
    }

    /**
     * Test homepage renders successful default and custom SEO tags.
     */
    public function test_homepage_renders_default_seo_tags(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<title>SetupNesia - Premium Mechanical Keyboards &amp; Workspace Accessories</title>', false);
        $response->assertSee('<meta name="description" content="Discover premium custom mechanical keyboards, high-fidelity keycaps, workspace deskmats, mouse, and cable management accessories to level up your setup at SetupNesia.">', false);
        $response->assertSee('<meta name="keywords" content="mechanical keyboard, custom keyboard, keycaps, deskmat, workspace accessories, programmer setup, gaming gear, setupnesia">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
    }

    /**
     * Test catalog page renders custom SEO tags.
     */
    public function test_catalog_page_renders_custom_seo_tags(): void
    {
        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('<title>Shop Premium Keyboards &amp; Workspace Accessories - SetupNesia</title>', false);
        $response->assertSee('Browse SetupNesia catalog of custom mechanical keyboards', false);
    }

    /**
     * Test product details page renders dynamic, product-specific SEO tags.
     */
    public function test_product_details_renders_dynamic_seo_tags(): void
    {
        $response = $this->get(route('shop.show', $this->product->slug));

        $response->assertStatus(200);
        
        // Assert title includes product name
        $response->assertSee('<title>Buy Keychron Q1 Pro - SetupNesia</title>', false);
        
        // Assert description excerpt stripped of html and limited
        $response->assertSee('<meta name="description" content="A custom mechanical keyboard with wireless capability and a full solid aluminum body for enthusiasts.">', false);
        
        // Assert OG Image contains absolute URL path to the primary product image
        $expectedImageUrl = url('/images/products/keychron_q1_pro.png');
        $response->assertSee('<meta property="og:image" content="' . $expectedImageUrl . '">', false);
        
        // Assert OG Type is product
        $response->assertSee('<meta property="og:type" content="product">', false);
    }
}
