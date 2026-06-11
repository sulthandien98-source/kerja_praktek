<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu')->with('error', 'Keranjang kosong!');
        }
        return view('pages.checkout', compact('cart'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => ['required', 'string', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'address' => 'required|string|max:500',
        ]);

        session(['checkout' => $request->only('name', 'phone', 'address')]);
        return redirect()->route('payment');
    }

    public function payment()
    {
        if (!session('checkout')) return redirect()->route('checkout');
        if (empty(session('cart', []))) return redirect()->route('menu');
        return view('pages.payment');
    }

    public function store(Request $request)
    {
        $cart     = session('cart', []);
        $checkout = session('checkout');

        if (empty($cart) || !$checkout) {
            return redirect()->route('menu');
        }

        DB::beginTransaction();
        try {
            $productIds = array_keys($cart);
            $products   = Product::lockForUpdate()
                            ->whereIn('id', $productIds)
                            ->get()
                            ->keyBy('id');

            $total = 0;
            foreach ($cart as $productId => $item) {
                $product = $products[$productId] ?? null;

                if (!$product || !$product->is_available) {
                    throw new \Exception("Produk tidak tersedia.");
                }
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak cukup.");
                }

                $total += $product->price * $item['qty'];
            }

            $order = Order::create([
                'user_id'        => Auth::id(),
                'customer_name'  => $checkout['name'],
                'phone'          => $checkout['phone'],
                'address'        => $checkout['address'],
                'total_price'    => $total,
                'status'         => Order::STATUS_WAITING,
                'payment_status' => Order::PAYMENT_UNPAID,
            ]);

            foreach ($cart as $productId => $item) {
                $product = $products[$productId];
                $product->decrement('stock', $item['qty']);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $item['qty'],
                    'price'      => $product->price,
                ]);
            }

            DB::commit();
            event(new OrderCreated($order));
            session()->forget(['cart', 'checkout']);

            return redirect()->route('order.waiting', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function waiting(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load('items.product');
        return view('pages.waiting', compact('order'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        if (!$order->canUploadPayment()) {
            return back()->with('error', 'Bukti pembayaran tidak dapat diupload saat ini.');
        }

        $request->validate([
            'payment_proof' => [
                'required', 'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:2048',
            ],
        ]);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof'       => $path,
            'payment_status'      => Order::PAYMENT_UPLOADED,
            'payment_note'        => null,
            'payment_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        return view('pages.orders', compact('orders'));
    }

    public function adminOrders(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders             = $query->paginate(20);
        $pendingPaymentCount = Order::where('payment_status', Order::PAYMENT_UPLOADED)->count();

        return view('admin.orders.index', compact('orders', 'pendingPaymentCount'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::STATUSES)),
        ]);
        Order::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function approvePayment($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status !== Order::PAYMENT_UPLOADED) {
            return back()->with('error', 'Tidak ada bukti pembayaran untuk diverifikasi.');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_APPROVED,
            'payment_note'   => null,
            'status'         => Order::STATUS_DIPROSES,
        ]);

        return back()->with('success', 'Pembayaran disetujui. Pesanan masuk ke Diproses.');
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'payment_note' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($id);

        if ($order->payment_status !== Order::PAYMENT_UPLOADED) {
            return back()->with('error', 'Tidak ada bukti pembayaran untuk ditolak.');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_REJECTED,
            'payment_note'   => $request->payment_note,
        ]);

        return back()->with('success', 'Pembayaran ditolak. Customer akan diminta upload ulang.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}