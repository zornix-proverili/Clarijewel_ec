<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('rank', ['regular', 'silver', 'gold', 'platinum'])->default('regular');
            $table->unsignedBigInteger('total_spent')->default(0); // 累計購入金額
            $table->integer('points')->default(0);
            $table->integer('cleaning_count_used')->default(0); // 今年のクリーニング利用回数
            $table->integer('resizing_count_used')->default(0);  // 今年のサイズ直し利用回数
            $table->boolean('is_banned')->default(false); // BANフラグ
            $table->string('favorite_metal')->nullable(); // お好み地金
            $table->string('ring_size')->nullable();     // お好み指サイズ
            $table->string('share_token')->nullable()->unique(); // お気に入りリスト共有トークン
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};