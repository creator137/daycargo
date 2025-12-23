<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('email');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('second_name', 100)->nullable()->after('last_name');

            $table->string('citizenship', 100)->nullable()->after('second_name');
            $table->string('employment_type', 50)->nullable()->after('citizenship');

            $table->unsignedBigInteger('city_id')->nullable()->after('employment_type');
            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'second_name',
                'citizenship',
                'employment_type',
                'city_id',
            ]);
        });
    }
};
