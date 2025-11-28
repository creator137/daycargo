<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) добавим недостающее поле applied_amount
        if (!Schema::hasColumn('promo_code_redemptions', 'applied_amount')) {
            Schema::table('promo_code_redemptions', function (Blueprint $table) {
                $table->decimal('applied_amount', 12, 2)
                    ->nullable()
                    ->after('status');
            });
        }

        // 2) подстрахуемся от ENUM в status (меняем на VARCHAR(32))
        // (часто сидеры падают именно из-за несоответствия значений ENUM)
        try {
            DB::statement("
                ALTER TABLE promo_code_redemptions
                MODIFY COLUMN status VARCHAR(32) NOT NULL
            ");
        } catch (\Throwable $e) {
            // если уже VARCHAR — тихо игнорируем
        }
    }

    public function down(): void
    {
        // откат: поле удалим, тип status не трогаем
        if (Schema::hasColumn('promo_code_redemptions', 'applied_amount')) {
            Schema::table('promo_code_redemptions', function (Blueprint $table) {
                $table->dropColumn('applied_amount');
            });
        }
    }
};
