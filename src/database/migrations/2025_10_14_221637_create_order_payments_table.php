<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->enum('method', ['cash', 'card', 'org_balance', 'client_balance'])->index();
            $t->decimal('amount', 12, 2);
            $t->string('currency', 3)->default('RUB');
            $t->enum('status', ['pending', 'captured', 'failed', 'refunded'])->default('captured')->index();
            $t->string('provider')->nullable();
            $t->string('provider_txn_id')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
