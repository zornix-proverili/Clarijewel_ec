<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginId = $request->input('login_id');
        $password = $request->input('password');

        // 管理者アカウントの判定 (clari_jewel_no○○)
        if (str_starts_with($loginId, 'clari_jewel_no')) {
            $user = User::where('admin_id', $loginId)->where('is_admin', true)->first();
            
            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['message' => '管理者IDまたはパスワードが正しくありません'], 401);
            }

            // 最終アクティビティ日時の更新
            $user->update(['last_activity_at' => now()]);

            $token = $user->createToken('admin_token')->plainTextToken;

            return response()->json([
                'message' => '管理者ログイン成功',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'admin_id' => $user->admin_id,
                    'name' => $user->name,
                    'is_admin' => true,
                    'store_id' => $user->store_id,
                ]
            ]);
        }

        // 一般ユーザーの判定 (メールアドレス)
        $user = User::where('email', $loginId)->where('is_admin', false)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'message' => 'ログイン成功',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'rank' => $user->rank, // regular, silver, gold, platinum
                'is_admin' => false,
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => '現在のパスワードが正しくありません'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'パスワードを変更しました']);
    }
}