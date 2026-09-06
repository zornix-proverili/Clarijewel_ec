<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    // お気に入り一覧
    public function index(Request $request)
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['wishlist' => $wishlists]);
    }

    // お気に入り追加・削除（トグル）
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'お気に入りから削除しました。', 'is_favo' => false]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json(['message' => 'お気に入りに登録しました。', 'is_favo' => true]);
    }
}