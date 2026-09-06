<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchLog;

class SearchLogController extends Controller
{
    // 検索ログの記録
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        SearchLog::create([
            'keyword' => trim($request->keyword),
            'user_id' => $request->user('sanctum')?->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['status' => 'logged']);
    }
}