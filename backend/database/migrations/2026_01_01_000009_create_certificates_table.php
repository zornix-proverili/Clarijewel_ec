<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique(); // 例: CJ-CERT-982371
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('gem_name');
            $table->string('carat')->nullable();
            $table->string('cut')->nullable();
            $table->string('color')->nullable();
            $table->string('clarity')->nullable();
            $table->decimal('estimated_value', 12, 2)->nullable()->comment('資産評価額');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};