<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Show products page (Blade view).
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Return all products as JSON.
     */
    public function getProducts()
    {
        $products = Product::all();
        return response()->json(['products' => $products], 200);
    }

    /**
     * Add a product to the session cart.
     * Expects JSON payload: { product_id: <int> }
     */
    public function addToCart(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $productId = (int) $request->input('product_id');
        $product = Product::find($productId);

        // Retrieve current cart (associative array keyed by product id)
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += 1;
        } else {
            $cart[$productId] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (float) $product->price,
                'image'    => $product->image ?? null,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart], 200);
    }

    /**
     * Remove a product from the session cart.
     * Expects JSON payload: { product_id: <int> }
     */
    public function removeFromCart(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);

        $productId = (int) $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => true, 'cart' => $cart], 200);
    }

    /**
     * Return current session cart as JSON.
     */
    public function getCart()
    {
        $cart = session()->get('cart', []);
        return response()->json(['cart' => $cart], 200);
    }

    /**
     * Admin list of menu items with optional search.
     */
    public function adminIndex(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.index', [
            'products' => $products,
            'q' => $q,
        ]);
    }

    /**
     * Show admin menu item editor.
     */
    public function edit(Product $product)
    {
        return view('admin.menu.edit', compact('product'));
    }

    /**
     * Update a product (admin).
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle optional image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()
            ->route('admin.menu-items.edit', $product)
            ->with('status', 'Product updated successfully');
    }
}
