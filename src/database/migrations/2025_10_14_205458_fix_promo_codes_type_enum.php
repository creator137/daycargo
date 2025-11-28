<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Приводим ENUM к нужным значениям
        DB::statement("
            ALTER TABLE promo_codes
            MODIFY COLUMN type ENUM('percent','fixed','bonus') NOT NULL
        ");
    }

    public function down(): void
    {
        // На случай отката — сделаем строкой (чтобы не ломаться на старых ENUM)
        DB::statement("
            ALTER TABLE promo_codes
            MODIFY COLUMN type VARCHAR(32) NOT NULL
        ");
    }
};
