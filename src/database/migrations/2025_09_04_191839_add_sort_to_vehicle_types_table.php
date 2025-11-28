<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            // порядок вывода; по умолчанию 0
            $table->unsignedInteger('sort')->default(0)->after('capacity_kg');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
