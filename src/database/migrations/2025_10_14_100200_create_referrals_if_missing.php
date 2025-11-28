<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('referrals')) return;

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('referee_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('reward_points', 10, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'referee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
