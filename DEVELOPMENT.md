# Clarijewel 全体開発手順書 & API統合仕様書

本ドキュメントは、高級ジュエリーEC『Clarijewel』のバックエンド（Laravel 11）およびフロントエンド（Next.js 16）の環境構築・実装・動作確認をまとめた統合手順書です。

---

## 1. 動作環境とセットアップ手順

### ディレクトリ構成

* バックエンド: `C:\xampp\htdocs\Clarijewel_ec\backend`
* フロントエンド: `C:\xampp\htdocs\Clarijewel_ec\frontend`

### バックエンド初期化手順

```bash
cd C:\xampp\htdocs\Clarijewel_ec\backend

# APIルーティングのインストール・有効化
php artisan install:api

# マイグレーションと初期ダミーデータの適用
php artisan migrate:fresh --seed

# サーバー起動 (Port: 8000)
php artisan serve

```

### フロントエンド初期化手順（別ターミナル）

```bash
cd C:\xampp\htdocs\Clarijewel_ec\frontend

# パッケージインストールと起動 (Port: 3000)
npm install
npm run dev

```

---

## 2. APIエンドポイント定義

| メソッド | エンドポイント | 認証 | 機能概要 |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/login` | 不要 | ログイン・Sanctumトークン発行 |
| `POST` | `/api/v1/auth/register` | 不要 | 会員登録 |
| `GET` | `/api/v1/products` | 不要 | 商品一覧・多角絞り込み検索 |
| `GET` | `/api/v1/products/{id}` | 不要 | 商品詳細（VIP閲覧フラグ含む） |
| `POST` | `/api/v1/inquiries` | 不要 | カテゴリー別自動割り分けお問い合わせ |
| `GET` | `/api/v1/cart` | 要 | カート取得（15分仮ロック残り時間算出） |
| `POST` | `/api/v1/cart/items` | 要 | カート追加（15分排他ロック適用） |
| `DELETE` | `/api/v1/cart/items/{id}` | 要 | カート商品削除 |
| `GET` | `/api/v1/wishlist` | 要 | お気に入り一覧 |
| `POST` | `/api/v1/wishlist/toggle` | 要 | お気に入り登録/解除 |
| `POST` | `/api/v1/orders/checkout` | 要 | 注文確定処理・鑑定書データ連動 |

---

## 3. バックエンドコア設定 (`backend/bootstrap/app.php`)

Laravel 11でAPIルートを正常に読み込ませるための必須記述です。

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

```

---

## 4. フロントエンドAPIクライアント (`frontend/lib/api.ts`)

```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL || '[http://127.0.0.1:8000/api/v1](http://127.0.0.1:8000/api/v1)',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }
  return config;
});

export default api;

```

---

## 5. 一括疎通確認コマンド

```bash
# ルーティング一覧の確認
php artisan route:list

# 商品APIのレスポンス確認
curl.exe -i [http://127.0.0.1:8000/api/v1/products](http://127.0.0.1:8000/api/v1/products)

```
