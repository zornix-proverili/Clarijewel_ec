<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    // カート一覧取得（15分タイマー同期用）
    public function index(Request $request)
    {
        // 期限切れカートを自前クリーンアップ
        $this->clearExpiredInternal();

        $cartItems = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($item) {
                $remainingSeconds = max(0, now()->diffInSeconds($item->expires_at, false));
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'ring_size' => $item->ring_size,
                    'engraving_text' => $item->engraving_text,
                    'expires_at' => $item->expires_at,
                    'remaining_seconds' => $remainingSeconds,
                ];
            });

        return response()->json(['cart' => $cartItems]);
    }

    // カート追加（在庫仮ロック＆15分有効期限）
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
            'ring_size' => 'nullable|string',
            'engraving_text' => 'nullable|string|max:30',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        return DB::transaction(function () use ($userId, $productId, $quantity, $request) {
            // 悲観的ロックで在庫取得
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($product->stock < $quantity) {
                return response()->json(['message' => '申し訳ございません。在庫が不足しています。'], 422);
            }

            // 在庫を減算（仮ロック）
            $product->decrement('stock', $quantity);

            // カートに追加（有効期限15分）
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'ring_size' => $request->ring_size,
                'engraving_text' => $request->engraving_text,
                'expires_at' => now()->addMinutes(15),
            ]);

            return response()->json(['message' => 'カートに追加し、15分間在庫をキープしました。', 'cart_item' => $cartItem]);
        });
    }

    // カート削除（仮ロック解除）
    public function remove(Request $request, $id)
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)->where('id', $id)->firstOrFail();

        DB::transaction(function () use ($cartItem) {
            // 在庫を戻す
            Product::where('id', $cartItem->product_id)->increment('stock', $cartItem->quantity);
            $cartItem->delete();
        });

        return response()->json(['message' => 'カートから削除し、在庫ロックを解除しました。']);
    }

    // 内部用・期限切れ仮ロック解除
    private function clearExpiredInternal()
    {
        $expiredItems = CartItem::where('expires_at', '<', now())->get();

        foreach ($expiredItems as $item) {
            DB::transaction(function () use ($item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                $item->delete();
            });
        }
    }
}