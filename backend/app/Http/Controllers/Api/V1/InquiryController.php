<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    // お問い合わせ送信（選択式カテゴリーによる担当振分）[cite: 3]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|in:product,certificate,shipping,other',[cite: 3]
            'message' => 'required|string|max:2000',
        ]);

        // カテゴリーごとの担当窓口の振り分け定義[cite: 3]
        $departmentMap = [
            'product' => 'support-product@clarijewel.example.com',
            'certificate' => 'appraisal@clarijewel.example.com',[cite: 3]
            'shipping' => 'logistics@clarijewel.example.com',
            'other' => 'info@clarijewel.example.com',
        ];

        $assignedDepartment = $departmentMap[$validated['category']] ?? 'info@clarijewel.example.com';

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'assigned_email' => $assignedDepartment,
        ]);

        return response()->json([
            'message' => 'お問い合わせを受け付けました。担当部署より折り返しご連絡いたします。',
            'inquiry_id' => $inquiry->id,
        ], 201);
    }
}