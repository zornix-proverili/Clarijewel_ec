<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class InventoryController extends Controller
{
    // 在庫一覧取得[cite: 1]
    public function index(Request $request)
    {
        $query = Product::select('id', 'sku', 'title', 'stock', 'price', 'item_category');

        if ($request->boolean('low_stock')) {
            $query->where('stock', '<=', 5); // 在庫切れ間近の絞り込み
        }

        $inventory = $query->orderBy('stock', 'asc')->get();

        return response()->json(['inventory' => $inventory]);
    }

    // 在庫数の更新[cite: 1]
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->stock = $request->stock;
        $product->save();

        return response()->json([
            'message' => '在庫数を更新しました。',
            'product_id' => $product->id,
            'new_stock' => $product->stock,
        ]);
    }
}