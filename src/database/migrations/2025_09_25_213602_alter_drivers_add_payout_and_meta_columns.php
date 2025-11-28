<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $t) {
            if (!Schema::hasColumn('drivers', 'partner_name')) {
                $t->string('partner_name')->nullable()->after('cities');
            }
            if (!Schema::hasColumn('drivers', 'payout_card')) {
                $t->string('payout_card', 32)->nullable()->after('partner_name');
            }
            if (!Schema::hasColumn('drivers', 'payout_first_name_en')) {
                $t->string('payout_first_name_en', 100)->nullable()->after('payout_card');
            }
            if (!Schema::hasColumn('drivers', 'payout_last_name_en')) {
                $t->string('payout_last_name_en', 100)->nullable()->after('payout_first_name_en');
            }
            if (!Schema::hasColumn('drivers', 'yandex_wallet')) {
                $t->string('yandex_wallet', 50)->nullable()->after('payout_last_name_en');
            }
            if (!Schema::hasColumn('drivers', 'sms_fixed_code')) {
                $t->string('sms_fixed_code', 10)->nullable()->after('yandex_wallet');
            }
            if (!Schema::hasColumn('drivers', 'sort')) {
                $t->unsignedInteger('sort')->default(100)->after('sms_fixed_code');
            }
            if (!Schema::hasColumn('drivers', 'comment')) {
                $t->text('comment')->nullable()->after('sort');
            }
            if (!Schema::hasColumn('drivers', 'app_password')) {
                $t->string('app_password')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('drivers', 'avatar_path')) {
                $t->string('avatar_path')->nullable()->after('app_password');
            }
            if (!Schema::hasColumn('drivers', 'updated_by')) {
                $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('avatar_path');
            }
            if (!Schema::hasColumn('drivers', 'balance')) {
                $t->decimal('balance', 10, 2)->default(0)->after('updated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $t) {
            // порядок важен: сначала FK
            if (Schema::hasColumn('drivers', 'updated_by')) {
                $t->dropConstrainedForeignId('updated_by');
            }
            foreach (
                [
                    'partner_name',
                    'payout_card',
                    'payout_first_name_en',
                    'payout_last_name_en',
                    'yandex_wallet',
                    'sms_fixed_code',
                    'sort',
                    'comment',
                    'app_password',
                    'avatar_path',
                    'balance',
                ] as $col
            ) {
                if (Schema::hasColumn('drivers', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
