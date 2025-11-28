<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('driver_groups', function (Blueprint $t) {
            $t->enum('visibility_mode', ['own_and_lower', 'manual'])
                ->default('own_and_lower')
                ->after('description');
            $t->json('visible_vehicle_type_ids')->nullable()->after('visibility_mode');
        });
    }

    public function down(): void
    {
        Schema::table('driver_groups', function (Blueprint $t) {
            $t->dropColumn(['visibility_mode', 'visible_vehicle_type_ids']);
        });
    }
};
