<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('promo_codes')) return;

        Schema::table('promo_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('promo_codes', 'type')) {
                $table->enum('type', ['percent', 'fixed', 'bonus'])->default('percent')->after('code');
            }
            if (!Schema::hasColumn('promo_codes', 'value')) {
                $table->decimal('value', 10, 2)->default(0)->after('type');
            }
            if (!Schema::hasColumn('promo_codes', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('value');
            }
            if (!Schema::hasColumn('promo_codes', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('starts_at');
            }
            if (!Schema::hasColumn('promo_codes', 'per_user_limit')) {
                $table->unsignedInteger('per_user_limit')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('promo_codes', 'usage_limit')) {
                $table->unsignedInteger('usage_limit')->nullable()->after('per_user_limit');
            }
            if (!Schema::hasColumn('promo_codes', 'active')) {
                $table->boolean('active')->default(true)->after('usage_limit');
            }
            if (!Schema::hasColumn('promo_codes', 'meta')) {
                $table->json('meta')->nullable()->after('active');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('promo_codes')) return;

        Schema::table('promo_codes', function (Blueprint $table) {
            foreach (['meta', 'active', 'usage_limit', 'per_user_limit', 'expires_at', 'starts_at', 'value', 'type'] as $col) {
                if (Schema::hasColumn('promo_codes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
