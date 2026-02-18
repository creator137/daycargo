<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedSmallInteger('passenger_seats')
                ->nullable()
                ->after('body_type_id');

            $table->unsignedInteger('actual_capacity_kg')
                ->nullable()
                ->after('passenger_seats');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'passenger_seats',
                'actual_capacity_kg',
            ]);
        });
    }
};
