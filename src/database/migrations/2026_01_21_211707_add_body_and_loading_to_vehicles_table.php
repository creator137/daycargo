<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // тип кузова (один)
            $table->unsignedBigInteger('body_type_id')->nullable()->after('vehicle_type_id');

            // виды погрузки (много) -> json
            $table->json('loading_types')->nullable()->after('body_type_id');

            $table->foreign('body_type_id')
                ->references('id')
                ->on('vehicle_body_types')
                ->nullOnDelete();

            $table->index('body_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['body_type_id']);
            $table->dropIndex(['body_type_id']);

            $table->dropColumn('loading_types');
            $table->dropColumn('body_type_id');
        });
    }
};
