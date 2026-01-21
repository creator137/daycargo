<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {

            // Паспорт
            $table->string('passport_series', 20)->nullable()->after('employment_type');
            $table->string('passport_number', 20)->nullable()->after('passport_series');
            $table->string('passport_issued_by', 255)->nullable()->after('passport_number');
            $table->date('passport_issued_at')->nullable()->after('passport_issued_by');
            $table->string('passport_reg_address', 500)->nullable()->after('passport_issued_at');
            $table->string('passport_fact_address', 500)->nullable()->after('passport_reg_address');

            // ИНН / ОГРНИП
            $table->string('inn', 20)->nullable()->after('passport_fact_address');
            $table->string('ogrnip', 20)->nullable()->after('inn');

            // Водительское удостоверение
            $table->string('driver_license_series', 20)->nullable()->after('ogrnip');
            $table->string('driver_license_number', 20)->nullable()->after('driver_license_series');
            $table->string('driver_license_category', 10)->nullable()->after('driver_license_number');
            $table->date('driver_license_experience_from')->nullable()->after('driver_license_category');
            $table->date('driver_license_expires_at')->nullable()->after('driver_license_experience_from');

            $table->index('inn');
            $table->index('ogrnip');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['inn']);
            $table->dropIndex(['ogrnip']);

            $table->dropColumn([
                'passport_series',
                'passport_number',
                'passport_issued_by',
                'passport_issued_at',
                'passport_reg_address',
                'passport_fact_address',
                'inn',
                'ogrnip',
                'driver_license_series',
                'driver_license_number',
                'driver_license_category',
                'driver_license_experience_from',
                'driver_license_expires_at',
            ]);
        });
    }
};
