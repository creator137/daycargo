<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Если таблицы промокодов нет — ничего не делаем
        if (!Schema::hasTable('promo_codes')) {
            return;
        }

        // Приводим ENUM к нужным значениям
        DB::statement("
            ALTER TABLE promo_codes
            MODIFY COLUMN type ENUM('percent','fixed','bonus') NOT NULL
        ");
    }

    public function down(): void
    {
        // Если нет таблицы — просто выходим
        if (!Schema::hasTable('promo_codes')) {
            return;
        }

        DB::statement("
            ALTER TABLE promo_codes
            MODIFY COLUMN type VARCHAR(32) NOT NULL
        ");
    }
};
