<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductService
{
    /**
     * Get all products with their category and primary image.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProducts(): Collection
    {
        return Product::with(['category', 'primaryImage'])->get();
    }

    /**
     * Get product by ID.
     *
     * @param int $id
     * @return \App\Models\Product
     */
    public function getProductById(int $id): Product
    {
        return Product::with(['category', 'images', 'primaryImage'])->findOrFail($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function createProduct(array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);
        
        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'weight' => $data['weight'] ?? 500,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['image'];
            $filename = time() . '_' . Str::slug($product->name) . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            $destinationPath = public_path('images/products');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = '/images/products/' . $filename;

            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => true,
            ]);
        }

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return \App\Models\Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);
        
        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'weight' => $data['weight'] ?? 500,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Handle new image upload
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['image'];
            $filename = time() . '_' . Str::slug($product->name) . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('images/products');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $imagePath = '/images/products/' . $filename;

            // Remove previous primary status
            $product->images()->update(['is_primary' => false]);

            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => true,
            ]);
        }

        return $product;
    }

    /**
     * Delete a product safely.
     *
     * @param \App\Models\Product $product
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(Product $product): bool
    {
        // Safety check: Cannot delete if it is associated with orders (relational restrict)
        // Wait, does it have orders? Product has many OrderItems.
        // Let's count order items. If order items > 0, restrict.
        // In Laravel 12, we can check relations using `orderItems()` if declared, or manual check on DB.
        // Let's declare orderItems relationship in Product model if we need it, or we can check via DB or models directly:
        // We know that OrderItem references product_id, so we can do:
        $orderedCount = \Illuminate\Support\Facades\DB::table('order_items')
            ->where('product_id', $product->id)
            ->count();

        if ($orderedCount > 0) {
            throw new \Exception("Cannot delete product because it has been ordered in historical transactions.");
        }

        // Delete physical images first
        foreach ($product->images as $img) {
            $fullPath = public_path($img->image_path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        // Cascading deletion handles product images in DB
        return $product->delete();
    }
}
