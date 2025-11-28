<?php

// database/migrations/XXXX_xx_xx_xxxxxx_add_section_to_permissions.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('permissions', 'section')) {
            Schema::table('permissions', function (Blueprint $t) {
                $t->string('section')->nullable()->after('description'); // русское название группы
            });
        }
    }
    public function down(): void
    {
        Schema::table('permissions', fn(Blueprint $t) => $t->dropColumn('section'));
    }
};
