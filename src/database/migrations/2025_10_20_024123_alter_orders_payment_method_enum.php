<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Добавляем недостающие варианты оплаты, чтобы сидер проходил:
        // cash, card, cashless, client_balance, org_balance
        DB::statement("
            ALTER TABLE `orders`
            MODIFY `payment_method`
            ENUM('cash','card','cashless','client_balance','org_balance')
            NOT NULL DEFAULT 'cash'
        ");
    }

    public function down(): void
    {
        // Возврат к исходному составу (если нужен). УБЕРИ client_balance при откате.
        DB::statement("
            ALTER TABLE `orders`
            MODIFY `payment_method`
            ENUM('cash','card','cashless','org_balance')
            NOT NULL DEFAULT 'cash'
        ");
    }
};
