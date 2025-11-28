<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_code_redemptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['applied', 'rejected'])->default('applied')->index();
            $t->string('reason', 500)->nullable();  // причина отказа (если rejected)
            $t->decimal('amount', 12, 2)->default(0); // начисленные бонусы (если применимо)
            $t->unsignedBigInteger('order_id')->nullable(); // позже свяжем с заказом
            $t->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['promo_code_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_redemptions');
    }
};
