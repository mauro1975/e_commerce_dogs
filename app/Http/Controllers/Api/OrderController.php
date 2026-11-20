<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\ProductStock;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'required|string',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'country'        => 'nullable|string|max:5',
            'payment_method' => 'required|in:credit_card,paypal',
            'discount_code'  => 'nullable|string',
            'session_id'     => 'nullable|string',
        ]);

        $cart = $request->user()
            ? Cart::where('user_id', $request->user()->id)->with('items.product')->first()
            : Cart::where('session_id', $request->input('session_id'))->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $subtotal = $cart->getTotal();
        $discountAmount = 0;
        $discountCode = null;

        if (!empty($data['discount_code'])) {
            $discount = Discount::where('code', strtoupper($data['discount_code']))->first();
            if ($discount && $discount->isValid($subtotal)) {
                $discountAmount = $discount->calculate($subtotal);
                $discountCode = $discount->code;
                $discount->increment('uses');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        $order = Order::create([
            'user_id'         => $request->user()?->id,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'address'         => $data['address'],
            'city'            => $data['city'],
            'postal_code'     => $data['postal_code'],
            'country'         => $data['country'] ?? 'IT',
            'payment_method'  => $data['payment_method'],
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_code'   => $discountCode,
            'shipping'        => 0,
            'total'           => $total,
            'payment_status'  => 'pending',
            'status'          => 'pending',
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name ?? 'N/A',
                'color'        => $item->color,
                'size'         => $item->size,
                'quantity'     => $item->quantity,
                'price'        => $item->price,
            ]);

            $itemProduct = Product::find($item->product_id);
            $itemVariant = $item->variant_id
                ? ProductVariant::find($item->variant_id)
                : null;
            if ($itemProduct) {
                ProductStock::decrementAfterOrder($itemProduct, $itemVariant, (int) $item->quantity);
            }
        }

        $cart->items()->delete();

        return (new OrderResource($order->load('items')))->response()->setStatusCode(201);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()?->id && !$request->user()?->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return new OrderResource($order->load('items'));
    }
}
