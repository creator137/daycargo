<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->enum('tariff_type', ['per_minute', 'fixed'])
                ->default('per_minute')
                ->after('vehicle_type_id');

            $table->unsignedInteger('base_hours')
                ->nullable()
                ->after('base_price');

            $table->decimal('extra_hour_price', 10, 2)
                ->nullable()
                ->after('base_hours');

            $table->decimal('loader_hour_price', 10, 2)
                ->nullable()
                ->after('extra_hour_price');

            $table->decimal('top_loading_price', 10, 2)
                ->nullable()
                ->after('loader_hour_price');

            $table->decimal('side_loading_price', 10, 2)
                ->nullable()
                ->after('top_loading_price');
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn([
                'tariff_type',
                'base_hours',
                'extra_hour_price',
                'loader_hour_price',
                'top_loading_price',
                'side_loading_price',
            ]);
        });
    }
};
