<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $t) {
            if (!Schema::hasColumn('cities', 'slug')) {
                $t->string('slug')->nullable();
            }
            if (!Schema::hasColumn('cities', 'sort')) {
                $t->unsignedInteger('sort')->default(100);
            }
            if (!Schema::hasColumn('cities', 'active')) {
                $t->boolean('active')->default(true);
            }
        });

        // индексы — только если колонки уже есть
        Schema::table('cities', function (Blueprint $t) {
            if (Schema::hasColumn('cities', 'slug')) {
                $t->index('slug');
            }
            if (Schema::hasColumn('cities', 'sort')) {
                $t->index('sort');
            }
            if (Schema::hasColumn('cities', 'active')) {
                $t->index('active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $t) {
            if (Schema::hasColumn('cities', 'active')) $t->dropColumn('active');
            if (Schema::hasColumn('cities', 'sort'))   $t->dropColumn('sort');
            if (Schema::hasColumn('cities', 'slug'))   $t->dropColumn('slug');
        });
    }
};
