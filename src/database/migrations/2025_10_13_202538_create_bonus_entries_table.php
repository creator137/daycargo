<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bonus_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'spend', 'expire'])->index();   // начисление / списание / сгорание
            $table->decimal('points', 12, 2);
            $table->string('source', 50)->nullable(); // order|promo|manual|referral ...
            $table->timestamp('expires_at')->nullable();
            $table->string('comment', 1000)->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_entries');
    }
};
