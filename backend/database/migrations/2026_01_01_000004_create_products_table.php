<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->integer('stock')->default(0);
            $table->integer('stock_threshold')->default(2)->comment('在庫警報しきい値');
            
            // 多角検索軸
            $table->string('item_category'); // リング, ネックレス・ペンダント, ピアス・イヤリング, ブレスレット, ルース
            $table->string('scene');         // ブライダル・アニバーサリー, デイリー・ファインジュエリー, ハイジュエリー・ステートメント, オフィス・フォーマル
            $table->string('gem_type');      // ダイヤモンド, サファイア, ルビー, エメラルド, アレキサイドライト, アクアマリン, パール, その他
            $table->string('metal');         // プラチナ(Pt950/Pt900), イエローゴールド(K18YG), ピンクゴールド(K18PG), ホワイトゴールド(K18WG)
            $table->integer('birth_month')->nullable()->comment('1〜12月');
            
            // フラグ・フィルター
            $table->boolean('is_vip_only')->default(false);
            $table->boolean('has_certificate')->default(false);
            $table->boolean('is_resizable')->default(true);
            $table->boolean('is_published')->default(true);
            
            $table->text('images_json')->nullable()->comment('Unsplashデモ画像URL配列');
            $table->date('restock_date')->nullable()->comment('入荷予定日');
            $table->integer('restock_quantity')->default(0)->comment('入荷予定数');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};