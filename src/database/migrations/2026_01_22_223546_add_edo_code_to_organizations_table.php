<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE organizations
            ADD edo_code VARCHAR(100) NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE organizations
            DROP COLUMN edo_code
        ");
    }
};
