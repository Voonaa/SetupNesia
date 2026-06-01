<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the shopping cart page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $cart = $this->cartService->getCartForUser($user);
        $subtotal = $this->cartService->getCartSubtotal($user);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * Add an item to the shopping cart.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $quantity = $request->input('quantity', 1);

        try {
            $this->cartService->addItem(Auth::user(), $product->id, $quantity);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $quantity = $request->input('quantity');

        try {
            $this->cartService->updateItemQuantity(Auth::user(), $id, $quantity);
            return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove an item from the shopping cart.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->cartService->removeItem(Auth::user(), $id);
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}
