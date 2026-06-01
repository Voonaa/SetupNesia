<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display the store landing page.
     */
    public function welcome(): View
    {
        $categories = Category::all();
        
        $featuredProducts = Product::with('primaryImage')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('welcome', compact('categories', 'featuredProducts'));
    }

    /**
     * Display a listing of products with filters and search.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'primaryImage'])->where('is_active', true);

        // Filter by search query
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by min price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        $products = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::all();

        return view('shop.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['category', 'images', 'primaryImage'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Fetch related products in the same category
        $relatedProducts = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
