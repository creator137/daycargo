<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('promo_code_redemptions')) return;

        Schema::create('promo_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('status', ['applied', 'pending', 'rejected'])->default('applied');
            $table->decimal('applied_amount', 12, 2)->default(0);
            $table->string('order_uid')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['promo_code_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_redemptions');
    }
};
