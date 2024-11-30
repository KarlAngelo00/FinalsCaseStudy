<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by price range if provided
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Filter by available quantity if provided
        if ($request->has('min_quantity')) {
            $query->where('quantity', '>=', $request->input('min_quantity'));
        }
        if ($request->has('max_quantity')) {
            $query->where('quantity', '<=', $request->input('max_quantity'));
        }

        // Search by description if provided
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->input('search') . '%');
        }

        // Paginate the results
        $products = $query->paginate(10); // Adjust the number as needed

        // Check if products were found
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found matching your criteria.'], 404);
        }

        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category' => 'required|string|max:100',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201); // Return created product with 201 status
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'barcode' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
                'category' => 'required|string|max:100',
            ]);

            $product = Product::findOrFail($id);
            $product->update($validated);
            return response()->json($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(null, 204); // Return no content status
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
}
