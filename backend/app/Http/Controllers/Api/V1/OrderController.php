<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Certificate;

class OrderController extends Controller
{
    // 注文決済処理（チェックアウト）
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,paypay',
            'shipping_address' => 'required|string|max:500',
            'coupon_code' => 'nullable|string',
            'card_token' => 'required_if:payment_method,credit_card|string',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'カートが空です。'], 400);
            }

            // 小計計算
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // クーポン割引適用
            $discountAmount = 0;
            if ($request->filled('coupon_code')) {
                // 簡易クーポン検証
                $discountAmount = $request->input('discount_amount', 0);
            }

            $tax = floor(($subtotal - $discountAmount) * 0.10);
            $totalAmount = ($subtotal - $discountAmount) + $tax;

            // 注文情報作成
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'CJ-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'shipping_address' => $request->shipping_address,
            ]);

            // 注文明細の保存および鑑定書（Certificate）の自動生成
            foreach ($cartItems as $cart) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->title,
                    'price' => $cart->product->price,
                    'quantity' => $cart->quantity,
                    'ring_size' => $cart->ring_size,
                    'engraving_text' => $cart->engraving_text,
                ]);

                // 宝石商品の場合、デジタル鑑定書を発行
                Certificate::create([
                    'order_item_id' => $orderItem->id,
                    'certificate_number' => 'CERT-' . strtoupper(Str::random(12)),
                    'gem_type' => $cart->product->gem_type ?? 'Diamond',
                    'carat' => $cart->product->carat ?? 0.5,
                    'clarity' => 'VVS1',
                    'cut' => 'Excellent',
                    'issued_at' => now(),
                ]);
            }

            // カートを空にする（仮ロック解除完了・購入確定）
            CartItem::where('user_id', $user->id)->delete();

            return response()->json([
                'message' => 'ご注文が正常に完了いたしました。',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ], 201);
        });
    }

    // 購入履歴一覧
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    // 注文詳細取得
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'items.certificate'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    // デジタル鑑定書取得
    public function getCertificate(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);
        
        $certificates = Certificate::whereHas('orderItem', function ($query) use ($order) {
            $query->where('order_id', $order->id);
        })->get();

        return response()->json(['certificates' => $certificates]);
    }
}