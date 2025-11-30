<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Если таблицы промокодов нет — ничего не делаем.
        // Это как раз твой случай на свежей базе stage.
        if (! Schema::hasTable('promo_codes')) {
            return;
        }

        // Если таблицы списаний ещё нет — создаём
        if (! Schema::hasTable('promo_code_redemptions')) {
            Schema::create('promo_code_redemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promo_code_id')
                    ->constrained('promo_codes')
                    ->cascadeOnDelete();
                $table->foreignId('user_id')->nullable();
                $table->string('context')->nullable(); // заказ, клиент и т.п.
                $table->unsignedInteger('amount')->default(1);
                $table->timestamps();
            });
        } else {
            // Если таблица уже есть, но внешнего ключа нет — добавим
            Schema::table('promo_code_redemptions', function (Blueprint $table) {
                // Важно: промокоды точно существуют (мы проверили выше),
                // значит FK можно создавать.
                if (! Schema::hasColumn('promo_code_redemptions', 'promo_code_id')) {
                    $table->foreignId('promo_code_id')
                        ->after('id')
                        ->constrained('promo_codes')
                        ->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        // Если таблицы нет — выходим
        if (! Schema::hasTable('promo_code_redemptions')) {
            return;
        }

        Schema::dropIfExists('promo_code_redemptions');
    }
};
