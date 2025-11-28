<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('cities')->whereNull('name')
            ->update(['name' => DB::raw('COALESCE(slug, "Город")')]);
    }
    public function down(): void
    {
        // откат не обязателен; оставим как есть
    }
};
