<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontStoreController extends Controller
{
    /**
     * Display a searchable and filterable product catalog.
     */
    public function listProducts(Request $request)
    {
        $query = Product::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Search by description
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->input('search') . '%');
        }

        // Sort by price
        if ($request->has('sort_price')) {
            $query->orderBy('price', $request->input('sort_price'));
        }

        // Paginate results
        $products = $query->paginate(10);

        return response()->json($products);
    }

    /**
     * Add a product to the shopping cart.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('cart', []);

        // Check if the product is already in the cart
        if (isset($cart[$validated['product_id']])) {
            $cart[$validated['product_id']]['quantity'] += $validated['quantity'];
        } else {
            $product = Product::find($validated['product_id']);
            $cart[$validated['product_id']] = [
                'name' => $product->description,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'total' => $product->price * $validated['quantity'],
            ];
        }

        Session::put('cart', $cart);

        return response()->json(['message' => 'Product added to cart', 'cart' => $cart]);
    }

    /**
     * View the shopping cart.
     */
    public function viewCart()
    {
        $cart = Session::get('cart', []);
        $grandTotal = array_sum(array_column($cart, 'total'));

        return response()->json(['cart' => $cart, 'grand_total' => $grandTotal]);
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$validated['product_id']])) {
            if ($validated['quantity'] == 0) {
                unset($cart[$validated['product_id']]);
            } else {
                $cart[$validated['product_id']]['quantity'] = $validated['quantity'];
                $cart[$validated['product_id']]['total'] = $cart[$validated['product_id']]['price'] * $validated['quantity'];
            }
        }

        Session::put('cart', $cart);

        return response()->json(['message' => 'Cart updated', 'cart' => $cart]);
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$validated['product_id']])) {
            unset($cart[$validated['product_id']]);
            Session::put('cart', $cart);
        }

        return response()->json(['message' => 'Product removed from cart', 'cart' => $cart]);
    }

    /**
     * Checkout and clear the cart.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'shipping_details' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash_on_delivery',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Process the order (this is a placeholder, you'd save order details to a database)
        Session::forget('cart');

        return response()->json(['message' => 'Checkout successful', 'details' => $validated]);
    }
}
