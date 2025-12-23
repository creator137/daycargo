<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // ВАЖНО: change() требует doctrine/dbal
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->change();
            $table->string('license_plate', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_type_id')->nullable(false)->change();
            $table->string('license_plate', 255)->nullable(false)->change();
        });
    }
};
