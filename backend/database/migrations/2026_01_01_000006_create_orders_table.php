<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->string('stripe_payment_intent_id')->nullable();
            
            // ステータス
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled', 'return_requested', 'returned', 'rejected'])->default('pending');
            $table->string('tracking_number')->nullable()->comment('追跡番号');
            $table->timestamp('delivered_at')->nullable()->comment('配達完了日時（48時間判定用）');
            
            // ギフト指定
            $table->boolean('is_gift')->default(false);
            $table->boolean('hide_price')->default(false);
            
            // 返品・検収・法的管理
            $table->text('return_reason')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->enum('return_inspection_status', ['none', 'pending', 'passed', 'rejected_damaged', 'rejected_swapped'])->default('none');
            $table->text('legal_memo')->nullable()->comment('損害賠償・法的手続きメモ');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};