<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // добавим недостающее поле для награды по рефералке
        if (!Schema::hasColumn('referrals', 'reward_points')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->decimal('reward_points', 12, 2)
                    ->nullable()
                    ->after('status');
            });
        }

        // подстрахуем тип поля status (иногда был ENUM) -> VARCHAR(32)
        try {
            DB::statement("
                ALTER TABLE referrals
                MODIFY COLUMN status VARCHAR(32) NOT NULL
            ");
        } catch (\Throwable $e) {
            // уже VARCHAR — ок
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('referrals', 'reward_points')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->dropColumn('reward_points');
            });
        }
        // status назад в ENUM не откатываем намеренно
    }
};
