<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('not_in_list')->default(false)->after('vehicle_type_id');

            $table->unsignedBigInteger('external_car_class_id')->nullable()->after('not_in_list');
            $table->index('external_car_class_id');

            $table->json('dimensions')->nullable()->after('options');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['external_car_class_id']);
            $table->dropColumn([
                'not_in_list',
                'external_car_class_id',
                'dimensions',
            ]);
        });
    }
};
