<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartWebController extends Controller
{
    private function getCartItems(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'color'      => 'nullable|string|max:50',
            'size'       => 'nullable|string|max:10',
            'quantity'   => 'integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Auto-pick first available color/size if not provided
        if (empty($data['color']) && !empty($product->available_colors)) {
            $colors = $product->available_colors;
            $data['color'] = is_array($colors[0]) ? $colors[0]['name'] : $colors[0];
        }
        if (empty($data['size']) && !empty($product->available_sizes)) {
            $sizes = $product->available_sizes;
            if (is_string($sizes)) {
                $sizes = json_decode($sizes, true) ?? [];
            }
            $data['size'] = $sizes[0] ?? null;
        }

        $variant = ProductVariant::where('product_id', $product->id)
            ->where('color', $data['color'] ?? '')
            ->where('size', $data['size'] ?? '')
            ->first();

        $price = $product->final_price + ($variant?->price_adjustment ?? 0);
        $key   = $product->id . '_' . ($data['color'] ?? 'default') . '_' . ($data['size'] ?? 'one-size');
        $cart  = $this->getCartItems();
        $qty   = $data['quantity'] ?? 1;

        // Resolve the color-specific image, fall back to product default
        $cartImage = $product->image;
        if (!empty($data['color'])) {
            $cols = is_array($product->available_colors)
                ? $product->available_colors
                : (json_decode($product->available_colors ?? '[]', true) ?? []);
            foreach ($cols as $col) {
                if (is_array($col) && ($col['name'] ?? '') === $data['color'] && !empty($col['image'])) {
                    $cartImage = $col['image'];
                    break;
                }
            }
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'name'       => $product->name,
                'image'      => $cartImage,
                'color'      => $data['color'] ?? null,
                'size'       => $data['size'] ?? null,
                'price'      => $price,
                'quantity'   => $qty,
            ];
        }

        $this->saveCart($cart);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'cart_count' => count($cart)]);
        }

        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function update(Request $request)
    {
        $key  = $request->input('key');
        $qty  = (int) $request->input('quantity', 1);
        $cart = $this->getCartItems();

        if (isset($cart[$key])) {
            if ($qty < 1) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $qty;
            }
        }

        $this->saveCart($cart);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'cart_count' => collect($cart)->sum('quantity')]);
        }
        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $key  = $request->input('key');
        $cart = $this->getCartItems();
        unset($cart[$key]);
        $this->saveCart($cart);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'cart_count' => collect($cart)->sum('quantity')]);
        }
        return redirect()->back()->with('success', 'Item removed.');
    }

    public function applyDiscount(Request $request)
    {
        $code     = strtoupper(trim($request->input('discount_code', '')));
        $cart     = $this->getCartItems();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discount = Discount::where('code', $code)->first();

        if (!$discount || !$discount->isValid($subtotal)) {
            return redirect()->back()->with('discount_error', 'Invalid or expired discount code.');
        }

        session(['discount_code' => $code, 'discount_amount' => $discount->calculate($subtotal)]);
        return redirect()->back()->with('success', 'Discount applied!');
    }

    public function checkout()
    {
        $cart = $this->getCartItems();
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }

        $subtotal       = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = session('discount_amount', 0);
        $discountCode   = session('discount_code', null);
        $total          = max(0, $subtotal - $discountAmount);

        return view('shop.checkout', compact('cart', 'subtotal', 'discountAmount', 'discountCode', 'total'));
    }

    public function placeOrder(Request $request)
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
        ]);

        $cart     = $this->getCartItems();
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty.');
        }

        $subtotal       = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = (float) session('discount_amount', 0);
        $discountCode   = session('discount_code');
        $total          = max(0, $subtotal - $discountAmount);

        $order = Order::create([
            'user_id'         => auth()->id(),
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'address'         => $data['address'],
            'city'            => $data['city'],
            'postal_code'     => $data['postal_code'],
            'country'         => $data['country'] ?? 'FR',
            'payment_method'  => $data['payment_method'],
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_code'   => $discountCode,
            'shipping'        => 0,
            'total'           => $total,
            'payment_status'  => 'pending',
            'status'          => 'pending',
        ]);

        foreach ($cart as $item) {
            $order->items()->create([
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'color'        => $item['color'],
                'size'         => $item['size'],
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
            ]);

            $orderProduct = Product::find($item['product_id']);
            $orderVariant = ! empty($item['variant_id'])
                ? ProductVariant::find($item['variant_id'])
                : null;
            if ($orderProduct) {
                ProductStock::decrementAfterOrder($orderProduct, $orderVariant, (int) $item['quantity']);
            }
        }

        // Increment discount usage
        if ($discountCode) {
            Discount::where('code', $discountCode)->increment('uses');
        }

        session()->forget(['cart', 'discount_code', 'discount_amount']);

        return redirect()->route('checkout.payment', $order);
    }

    public function payment(Order $order)
    {
        return view('shop.payment', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        if ($order->payment_method === 'credit_card') {
            $request->validate([
                'card_number' => 'required|string',
                'expiry'      => 'required|string',
                'cvc'         => 'required|string',
            ]);
            // Real integration: use Stripe\StripeClient
            // Simulated successful payment
            $order->update(['payment_status' => 'paid', 'status' => 'processing', 'payment_id' => 'sim_' . Str::random(16)]);
        } elseif ($order->payment_method === 'paypal') {
            // Real integration: redirect to PayPal, handle IPN
            $order->update(['payment_status' => 'paid', 'status' => 'processing', 'payment_id' => 'pp_' . Str::random(16)]);
        }

        return redirect()->route('order.confirmation', $order);
    }

    public function confirmation(Order $order)
    {
        return view('shop.confirmation', compact('order'));
    }
}
