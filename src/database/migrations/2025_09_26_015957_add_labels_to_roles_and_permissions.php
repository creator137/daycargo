<?php

// database/migrations/XXXX_xx_xx_xxxxxx_add_labels_to_roles_and_permissions.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $t) {
                $t->string('display_name')->nullable()->after('name');
                $t->string('description')->nullable()->after('display_name');
            });
        }
        if (!Schema::hasColumn('permissions', 'display_name')) {
            Schema::table('permissions', function (Blueprint $t) {
                $t->string('display_name')->nullable()->after('name');
                $t->string('description')->nullable()->after('display_name');
            });
        }
    }
    public function down(): void
    {
        if (Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', fn(Blueprint $t) => $t->dropColumn(['display_name', 'description']));
        }
        if (Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', fn(Blueprint $t) => $t->dropColumn(['display_name', 'description']));
        }
    }
};
