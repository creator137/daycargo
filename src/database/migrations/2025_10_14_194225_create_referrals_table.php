<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('referrer_id')->constrained('clients')->cascadeOnDelete(); // кто пригласил
            $t->foreignId('referee_id')->nullable()->constrained('clients')->nullOnDelete(); // приглашённый (после регистрации)
            $t->string('code')->index(); // реф-код (можно совпадать у разных, если стратегия иная)
            $t->enum('status', ['pending', 'registered', 'rewarded'])->default('pending')->index();
            $t->timestamp('bonus_awarded_at')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['code', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
