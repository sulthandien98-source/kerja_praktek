<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_available', true)->orWhere('stock', '>', 0)->latest()->get();
        return view('pages.menu', compact('products'));
    }

    public function getCart()
    {
        return response()->json(session('cart', []));
    }

    public function addToCart(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:products,id']);

        $product = Product::findOrFail($request->id);

        if ($product->stock <= 0 || !$product->is_available) {
            return response()->json(['error' => 'Produk tidak tersedia'], 422);
        }

        $cart       = session('cart', []);
        $currentQty = $cart[$product->id]['qty'] ?? 0;

        if ($currentQty >= $product->stock) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 422);
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'name'  => $product->name,
                'price' => (int) $product->price,
                'qty'   => 1,
            ];
        }

        session()->put('cart', $cart);

        return response()->json($cart);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer|exists:products,id',
            'action' => 'required|in:plus,minus',
        ]);

        $cart    = session('cart', []);
        $product = Product::findOrFail($request->id);

        if (isset($cart[$request->id])) {
            if ($request->action === 'plus') {
                if ($cart[$request->id]['qty'] >= $product->stock) {
                    return response()->json(['error' => 'Stok maksimal tercapai'], 422);
                }
                $cart[$request->id]['qty']++;
            } else {
                $cart[$request->id]['qty']--;
                if ($cart[$request->id]['qty'] <= 0) {
                    unset($cart[$request->id]);
                }
            }
        }

        session()->put('cart', $cart);

        return response()->json($cart);
    }

    public function clearCart()
    {
        session()->forget('cart');
        return response()->json([]);
    }

    public function adminIndex()
    {
        $products = Product::latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|integer|min:0|max:99999999',
            'stock'       => 'required|integer|min:0|max:99999',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create(array_merge($validated, ['is_available' => true]));

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|integer|min:0|max:99999999',
            'stock'       => 'required|integer|min:0|max:99999',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return back()->with('success', 'Produk berhasil diupdate.');
    }

    public function addStock(Request $request, $id)
    {
        $request->validate(['stock' => 'required|integer|min:1|max:9999']);

        $product = Product::findOrFail($id);
        $product->increment('stock', $request->stock);

        return back()->with('success', "Stok +{$request->stock} berhasil ditambahkan.");
    }

    public function toggle($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_available' => !$product->is_available]);

        $status = $product->is_available ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk berhasil {$status}.");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }
}