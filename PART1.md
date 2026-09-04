`format.md`の定義内容に基づき、環境構築から初期DBセットアップ、認証API実装、Gitプッシュまでを網羅した手順書 `PART1.md` を作成しました。プロジェクト直下に `PART1.md` として保存して利用できます。

```markdown
# Clarijewel 開発手順書 - Part 1: バックエンド基盤 & 認証API構築

本書は宝石ECサイト『Clarijewel』の初期セットアップからデータベース構築、共通ログイン認証APIの実装、GitHub同期までの手順をまとめた開発ガイドです。

---

## 1. 開発環境・ディレクトリ構成

### 対象環境
* **ローカルパス**: `C:\xampp\htdocs\Clarijewel_ec`
* **DB管理ツール**: XAMPP MySQL (ポート `3306` / パスワード `password`)
* **DB名**: `clarijewel_ec`

### プロジェクト構造
```text
Clarijewel_ec/
├── backend/      # Laravel 11 API サーバー
├── frontend/     # Next.js 16 (App Router / TypeScript / Tailwind CSS)
├── .gitignore
├── format.md
└── PART1.md

```

---

## 2. データベースマイグレーション設計

外部キー制約のエラーを防止するため、マイグレーションファイルは先頭に連番を付与して順番通り生成・実行します。

### 実行順序一覧 (`backend/database/migrations/`)

1. `2026_01_01_000001_create_users_table.php`
2. `2026_01_01_000002_create_salons_table.php`
3. `2026_01_01_000003_create_admins_table.php`
4. `2026_01_01_000004_create_products_table.php`
5. `2026_01_01_000005_create_cart_items_table.php`
6. `2026_01_01_000006_create_orders_table.php`
7. `2026_01_01_000007_create_order_items_table.php`
8. `2026_01_01_000008_create_reservations_table.php`
9. `2026_01_01_000009_create_certificates_table.php`
10. `2026_01_01_000010_create_aftercare_logs_table.php`
11. `2026_01_01_000011_create_user_memos_table.php`
12. `2026_01_01_000012_create_admin_activity_logs_table.php`
13. `2026_01_01_000013_create_product_reviews_table.php`
14. `2026_01_01_000014_create_anniversaries_table.php`
15. `2026_01_01_000015_create_search_logs_table.php`

---

## 3. シーダーによる初期データ投入

`backend/database/seeders/DatabaseSeeder.php` にて、規定の初期マスターデータを投入します。

### 初期投入データ仕様

* **管理者アカウント**: `clari_jewel_no01` / 初期PW `#12c124j`
* **実店舗（4サロン）**: 銀座本店、横浜サロン、名古屋サロン、大阪心斎橋サロン
* **デモ商品データ**: VIP限定品含むサンプルデータ

### 実行コマンド

```bash
cd C:\xampp\htdocs\Clarijewel_ec\backend
php artisan migrate:fresh --seed

```

---

## 4. Eloquentモデル一括作成

データベース操作用のモデルクラスを生成します。

```bash
php artisan make:model Admin
php artisan make:model Salon
php artisan make:model Product
php artisan make:model CartItem
php artisan make:model Order
php artisan make:model OrderItem
php artisan make:model Reservation
php artisan make:model Certificate
php artisan make:model AftercareLog
php artisan make:model UserMemo
php artisan make:model AdminActivityLog
php artisan make:model ProductReview
php artisan make:model Anniversary
php artisan make:model SearchLog

```

※ `User`モデルおよび`Admin`モデルには `use Laravel\Sanctum\HasApiTokens;` を追加記述してください。

---

## 5. 共通ログイン・自動分岐認証API

`POST /api/v1/auth/login` エンドポイントにて、入力された `login_id` の文字列形式パターンで一般ユーザーと管理者を自動分岐します。

* **`clari_jewel_no` から始まる場合**: 管理者（`admins` テーブル）を照合
* **メールアドレス形式の場合**: 一般ユーザー（`users` テーブル）を照合

### コントローラー配置

ファイルパス: `backend/app/Http/Controllers/Api/V1/AuthController.php`

### ルーティング設定

ファイルパス: `backend/routes/api.php`

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});

```

---

## 6. Gitバージョン管理 & GitHub送信手順

ルートディレクトリ（`C:\xampp\htdocs\Clarijewel_ec`）で環境設定ファイル等を除外した状態でリモートリポジトリへ送信します。

### 1. `.gitignore` の配置（ルート直下）

```text
backend/vendor/
backend/.env
backend/storage/*.key
backend/public/storage
frontend/node_modules/
frontend/.next/
frontend/out/
.DS_Store
.vscode/

```

### 2. コミット & プッシュコマンド

```bash
cd C:\xampp\htdocs\Clarijewel_ec
git init
git add .
git commit -m "feat: Part 1 complete - DB migrations, seeders, models, and Auth API"
git branch -M main
git remote add origin [https://github.com/zornix-proverili/Clarijewel_ec.git](https://github.com/zornix-proverili/Clarijewel_ec.git)
git push -u origin main

```

```

```