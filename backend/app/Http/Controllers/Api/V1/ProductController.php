<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // 多角絞り込み検索＆商品一覧
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('item_category')) {
            $query->where('item_category', $request->item_category);
        }
        if ($request->filled('gem_type')) {
            $query->where('gem_type', $request->gem_type);
        }
        if ($request->filled('metal')) {
            $query->where('metal', $request->metal);
        }
        if ($request->filled('birth_month')) {
            $query->where('birth_month', $request->birth_month);
        }
        if ($request->filled('scene')) {
            $query->where('scene', $request->scene);
        }
        if ($request->boolean('is_vip_only')) {
            $query->where('is_vip_only', true);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('description', 'like', "%{$kw}%")
                  ->orWhere('sku', 'like', "%{$kw}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($products);
    }

    // 商品詳細取得（VIP制限ロジック適用）
    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user('sanctum');

        // VIP専用商品かつ、未ログインまたはVIP（platinum）ランク以外の場合の表示制御
        if ($product->is_vip_only && (!$user || $user->rank !== 'platinum')) {
            return response()->json([
                'id' => $product->id,
                'title' => $product->title,
                'sku' => $product->sku,
                'item_category' => $product->item_category,
                'is_vip_only' => true,
                'requires_vip' => true,
                'message' => 'この商品はプラチナVIP会員様限定公開作品です。VIPアカウントでログインしてご覧ください。',
                'images_json' => $product->images_json,
            ]);
        }

        return response()->json([
            'product' => $product,
            'requires_vip' => false,
        ]);
    }
}