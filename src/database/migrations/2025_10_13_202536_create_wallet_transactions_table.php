<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            // Полиморфический владелец кошелька: клиент сейчас, позже сможем добавить Organization
            $table->morphs('owner'); // owner_type, owner_id
            $table->enum('wallet', ['money', 'bonus'])->index();     // какой кошелёк
            $table->enum('operation', ['topup', 'debit', 'adjust'])->index();
            $table->decimal('amount', 12, 2); // для бонусов храним «баллы» тоже как decimal
            $table->string('currency', 3)->default('RUB'); // на будущее, для бонусов можно игнорить
            $table->string('comment', 1000)->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id', 'wallet']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
