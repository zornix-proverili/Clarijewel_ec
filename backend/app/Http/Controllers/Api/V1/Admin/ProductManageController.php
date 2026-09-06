<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductManageController extends Controller
{
    // 管理者用商品一覧
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->paginate(20);
        return response()->json($products);
    }

    // 商品新規追加[cite: 1]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_category' => 'required|string',
            'gem_type' => 'nullable|string',
            'metal' => 'nullable|string',
            'birth_month' => 'nullable|integer|between:1,12',
            'is_vip_only' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json(['message' => '商品を追加しました。', 'product' => $product], 201);
    }

    // 商品情報更新[cite: 1]
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'sku' => 'string|unique:products,sku,' . $id,
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'item_category' => 'string',
            'gem_type' => 'nullable|string',
            'metal' => 'nullable|string',
            'birth_month' => 'nullable|integer|between:1,12',
            'is_vip_only' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json(['message' => '商品情報を更新しました。', 'product' => $product]);
    }

    // 商品削除[cite: 1]
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => '商品を削除しました。']);
    }
}