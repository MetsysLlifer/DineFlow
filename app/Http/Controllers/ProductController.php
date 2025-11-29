<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
}
