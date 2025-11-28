<?php
// database/migrations/XXXX_XX_XX_XXXXXX_add_name_to_cities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $t) {
            if (!Schema::hasColumn('cities', 'name')) {
                $t->string('name')->nullable(); // безопасно для существующих строк
                $t->index('name');
            }
        });
    }
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $t) {
            if (Schema::hasColumn('cities', 'name')) {
                $t->dropIndex(['name']);
                $t->dropColumn('name');
            }
        });
    }
};
