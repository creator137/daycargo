<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('driver_group_client_tariff')) {
            Schema::table('driver_group_client_tariff', function (Blueprint $t) {
                if (!Schema::hasColumn('driver_group_client_tariff', 'created_at')) {
                    $t->timestamp('created_at')->nullable()->after('client_tariff_id');
                }
                if (!Schema::hasColumn('driver_group_client_tariff', 'updated_at')) {
                    $t->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('driver_group_client_tariff')) {
            Schema::table('driver_group_client_tariff', function (Blueprint $t) {
                if (Schema::hasColumn('driver_group_client_tariff', 'updated_at')) {
                    $t->dropColumn('updated_at');
                }
                if (Schema::hasColumn('driver_group_client_tariff', 'created_at')) {
                    $t->dropColumn('created_at');
                }
            });
        }
    }
};
