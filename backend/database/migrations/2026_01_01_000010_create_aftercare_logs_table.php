<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aftercare_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['cleaning', 'resizing', 'repair']);
            $table->enum('method', ['store_bring', 'mail_in']); // 店舗持ち込み / 郵送
            $table->foreignId('salon_id')->nullable()->constrained(); // 持ち込み店舗
            $table->enum('status', ['applied', 'received', 'in_progress', 'completed', 'returned'])->default('applied');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aftercare_logs');
    }
};