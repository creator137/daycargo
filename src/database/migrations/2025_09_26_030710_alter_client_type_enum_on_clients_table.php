<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL: меняем ENUM на нужный набор значений
        DB::statement(
            "ALTER TABLE clients
             MODIFY client_type ENUM('person','org','guest')
             NOT NULL DEFAULT 'person'"
        );
    }

    public function down(): void
    {
        // Верни к безопасному минимуму (подстрой под то, что было, если знаешь)
        DB::statement(
            "ALTER TABLE clients
             MODIFY client_type ENUM('person')
             NOT NULL DEFAULT 'person'"
        );
    }
};
