<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('promo_codes')) {
            return;
        }

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code', 64)->unique();

            // чтобы твои миграции 2025_10_14_100000 / 205458 работали без сюрпризов
            $table->enum('type', ['percent', 'fixed', 'bonus'])->default('percent');
            $table->decimal('value', 10, 2)->default(0);

            $table->timestamp('starts_at')->nullable();

            // основное поле, которое у тебя в fillable
            $table->timestamp('expires_at')->nullable();

            // ВАЖНО: в модели scopeActive сейчас используется ends_at (иначе будут SQL-ошибки)
            $table->timestamp('ends_at')->nullable();

            $table->unsignedInteger('per_user_limit')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();

            $table->boolean('active')->default(true);
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
