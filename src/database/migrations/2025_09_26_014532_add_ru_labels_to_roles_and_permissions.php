<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $t) {
            $t->string('display_name')->nullable()->after('name');   // «Администратор»
            $t->text('description')->nullable()->after('display_name');
        });
        Schema::table('permissions', function (Blueprint $t) {
            $t->string('display_name')->nullable()->after('name');   // «Водители: просмотр»
            $t->string('group')->nullable()->after('display_name');  // «Исполнители», «Справочники» и т.п.
            $t->text('description')->nullable()->after('group');
        });
    }
    public function down(): void
    {
        Schema::table('roles', fn(Blueprint $t) => $t->dropColumn(['display_name', 'description']));
        Schema::table('permissions', fn(Blueprint $t) => $t->dropColumn(['display_name', 'group', 'description']));
    }
};
