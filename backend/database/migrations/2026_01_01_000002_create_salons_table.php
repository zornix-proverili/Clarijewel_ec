<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_code')->unique(); // 例: clari_jewel_no01
            $table->string('name');
            $table->string('password');
            $table->foreignId('salon_id')->nullable()->comment('所属店舗ID');
            $table->timestamp('last_activity_at')->nullable(); // リアルタイムアクティビティ確認用
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};