<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;

class ReviewController extends Controller
{
    // 対象商品のレビュー一覧
    public function index($productId)
    {
        $reviews = ProductReview::with('user:id,name')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();

        $likesCount = ProductReview::where('product_id', $productId)->where('is_recommended', true)->count();
        $dislikesCount = ProductReview::where('product_id', $productId)->where('is_recommended', false)->count();

        return response()->json([
            'reviews' => $reviews,
            'summary' => [
                'likes' => $likesCount,
                'dislikes' => $dislikesCount,
            ]
        ]);
    }

    // レビュー投稿
    public function store(Request $request, $productId)
    {
        $request->validate([
            'is_recommended' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = ProductReview::create([
            'product_id' => $productId,
            'user_id' => $request->user()->id,
            'is_recommended' => $request->is_recommended,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'レビューを投稿しました。', 'review' => $review], 201);
    }
}