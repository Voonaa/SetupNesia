<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;
    protected CartService $cartService;

    public function __construct(CheckoutService $checkoutService, CartService $cartService)
    {
        $this->checkoutService = $checkoutService;
        $this->cartService = $cartService;
    }

    /**
     * Display the checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCartForUser($user);

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your shopping cart is empty.');
        }

        $subtotal = $this->cartService->getCartSubtotal($user);
        $shipping = $this->checkoutService->calculateShipping($user);

        return view('checkout.index', compact('cart', 'subtotal', 'shipping'));
    }

    /**
     * Place a new order.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->checkoutService->placeOrder(Auth::user(), $request->validated());
            
            // Redirect to order success/payment page
            // We will define customer orders routes in Phase 6, so redirect to customer dashboard for now or a custom success page
            return redirect()->route('dashboard')->with('success', "Order placed successfully! Order Number: {$order->order_number}. Please complete payment.");
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }
    }
}
