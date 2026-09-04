<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('salon_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['store', 'online']);
            $table->datetime('reserved_at');
            $table->string('jitsi_meeting_url')->nullable()->comment('オンライン相談用URL');
            $table->text('pre_consultation_notes')->nullable()->comment('事前ヒアリング内容');
            $table->enum('status', ['confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};