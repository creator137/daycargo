<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Делает referrals.code NULLABLE (без зависимости от doctrine/dbal)
        if (Schema::hasTable('referrals')) {
            try {
                DB::statement("ALTER TABLE referrals MODIFY COLUMN code VARCHAR(64) NULL");
            } catch (\Throwable $e) {
                // если столбца нет — игнор, если уже nullable — тоже ок
            }
        }
    }

    public function down(): void
    {
        // Возврат к NOT NULL (на случай отката)
        if (Schema::hasTable('referrals')) {
            try {
                DB::statement("ALTER TABLE referrals MODIFY COLUMN code VARCHAR(64) NOT NULL");
            } catch (\Throwable $e) {
                // nothing
            }
        }
    }
};
