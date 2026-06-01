<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    /**
     * Get or create the user's active cart.
     *
     * @param \App\Models\User $user
     * @return \App\Models\Cart
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Get user's cart with items and products.
     *
     * @param \App\Models\User $user
     * @return \App\Models\Cart|null
     */
    public function getCartForUser(User $user): ?Cart
    {
        return Cart::with(['items.product.primaryImage'])->where('user_id', $user->id)->first();
    }

    /**
     * Add a product to the user's cart.
     *
     * @param \App\Models\User $user
     * @param int $productId
     * @param int $quantity
     * @return \App\Models\CartItem
     * @throws \Exception
     */
    public function addItem(User $user, int $productId, int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_active) {
            throw new \Exception("This product is currently inactive and cannot be purchased.");
        }

        $cart = $this->getOrCreateCart($user);
        
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $targetQuantity = $quantity;
        if ($cartItem) {
            $targetQuantity += $cartItem->quantity;
        }

        if ($product->stock < $targetQuantity) {
            throw new \Exception("Cannot add product. Only {$product->stock} units are left in stock.");
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $targetQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $targetQuantity,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param \App\Models\User $user
     * @param int $itemId
     * @param int $quantity
     * @return \App\Models\CartItem
     * @throws \Exception
     */
    public function updateItemQuantity(User $user, int $itemId, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be at least 1.");
        }

        $cart = $this->getOrCreateCart($user);
        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);
        $product = $cartItem->product;

        if ($product->stock < $quantity) {
            throw new \Exception("Cannot update quantity. Only {$product->stock} units are left in stock.");
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    /**
     * Remove an item from the cart.
     *
     * @param \App\Models\User $user
     * @param int $itemId
     * @return bool
     */
    public function removeItem(User $user, int $itemId): bool
    {
        $cart = $this->getOrCreateCart($user);
        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);
        return $cartItem->delete();
    }

    /**
     * Clear all items from the cart.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function clearCart(User $user): void
    {
        $cart = $this->getOrCreateCart($user);
        CartItem::where('cart_id', $cart->id)->delete();
    }

    /**
     * Get the subtotal amount of items in the cart.
     *
     * @param \App\Models\User $user
     * @return float
     */
    public function getCartSubtotal(User $user): float
    {
        $cart = $this->getCartForUser($user);
        if (!$cart) {
            return 0.00;
        }

        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->product) {
                $subtotal += $item->product->price * $item->quantity;
            }
        }

        return (float) $subtotal;
    }
}
