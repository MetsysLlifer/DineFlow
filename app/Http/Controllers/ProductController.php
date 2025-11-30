<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Events\ProductChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogger;

class ProductController extends Controller
{
    /**
     * Show products page (Blade view).
     */
    public function index()
    {
        ActivityLogger::log('products.view');
        return view('products.index');
    }

    /**
     * Return all products as JSON.
     */
    public function getProducts(Request $request)
    {
        $category = $request->query('category');
        $products = Product::query()
            ->when($category, fn($q) => $q->where('category', $category))
            ->get();
        ActivityLogger::log('products.list');
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

        ActivityLogger::log('cart.add', Product::class, $productId, [
            'quantity' => $cart[$productId]['quantity']
        ]);

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
            ActivityLogger::log('cart.remove', Product::class, $productId);
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
        $category = $request->query('category');

        $products = Product::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->when($category, fn($query) => $query->where('category', $category))
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.index', [
            'products' => $products,
            'q' => $q,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.menu.create', compact('categories'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);
        event(new ProductChanged('created', $product));
        ActivityLogger::log('product.create', Product::class, $product->id, [
            'name' => $product->name,
            'price' => $product->price,
        ]);

        return redirect()->route('admin.menu-items.index')->with('status', 'Product created');
    }

    /**
     * Show admin menu item editor.
     */
    public function edit(Product $product)
    {
        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.menu.edit', compact('product', 'categories'));
    }

    /**
     * Update a product (admin).
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle optional image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);
        event(new ProductChanged('updated', $product));

        ActivityLogger::log('product.update', Product::class, $product->id, [
            'name' => $product->name,
            'price' => $product->price,
        ]);

        return redirect()
            ->route('admin.menu-items.edit', $product)
            ->with('status', 'Product updated successfully');
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        $id = $product->id;
        $snapshot = ['name' => $product->name, 'price' => $product->price, 'image' => $product->image];
        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        event(new ProductChanged('deleted', $product));
        ActivityLogger::log('product.delete', Product::class, $id, $snapshot);
        return redirect()->route('admin.menu-items.index')->with('status', 'Product deleted');
    }
}
