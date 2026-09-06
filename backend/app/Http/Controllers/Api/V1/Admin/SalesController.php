<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Carbon;

class SalesController extends Controller
{
    // 売上ダッシュボード・集計情報[cite: 1]
    public function dashboard()
    {
        $now = Carbon::now();

        // 当月売上[cite: 1]
        $monthlySales = Order::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('status', 'completed')
            ->sum('total_amount');

        // 累計売上[cite: 1]
        $totalSales = Order::where('status', 'completed')->sum('total_amount');

        // 当月注文数
        $monthlyOrdersCount = Order::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('status', 'completed')
            ->count();

        // 部署・直近の注文5件
        $recentOrders = Order::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'monthly_sales' => $monthlySales,
            'total_sales' => $totalSales,
            'monthly_orders_count' => $monthlyOrdersCount,
            'recent_orders' => $recentOrders,
        ]);
    }
}