<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // после ИНН удобно, но after() может не работать на всех драйверах одинаково — в MySQL обычно ок
            $table->string('snils', 20)->nullable()->after('inn');
            $table->index('snils');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['snils']);
            $table->dropColumn('snils');
        });
    }
};
