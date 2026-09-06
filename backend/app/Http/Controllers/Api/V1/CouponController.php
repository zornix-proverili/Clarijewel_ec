<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    // クーポンコードの検証と割引計算
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json(['message' => '無効または期限切れのクーポンコードです。'], 422);
        }

        if ($request->subtotal < $coupon->min_purchase_amount) {
            return response()->json([
                'message' => "このクーポンは¥" . number_format($coupon->min_purchase_amount) . "以上のお買い上げでご利用いただけます。"
            ], 422);
        }

        // 割引額計算
        $discount = 0;
        if ($coupon->discount_type === 'fixed') {
            $discount = $coupon->discount_value;
        } elseif ($coupon->discount_type === 'percent') {
            $discount = floor($request->subtotal * ($coupon->discount_value / 100));
        }

        $discount = min($discount, $request->subtotal);

        return response()->json([
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'message' => 'クーポンが適用されました。',
        ]);
    }
}