<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Создать driver_groups только если отсутствует
        if (! Schema::hasTable('driver_groups')) {
            Schema::create('driver_groups', function (Blueprint $t) {
                $t->id();
                $t->string('name', 255);
                $t->string('city', 100)->nullable();
                $t->string('profession', 100)->nullable();
                $t->foreignId('vehicle_type_id')
                    ->constrained('vehicle_types')->cascadeOnDelete();
                $t->unsignedSmallInteger('priority')->default(10);
                $t->unsignedInteger('sort')->default(100);
                $t->text('description')->nullable();
                $t->boolean('active')->default(true);
                $t->timestamps();
            });
        }

        // Пивот-таблица и уникальный индекс
        if (! Schema::hasTable('driver_group_client_tariff')) {
            Schema::create('driver_group_client_tariff', function (Blueprint $t) {
                $t->id();
                $t->foreignId('driver_group_id')
                    ->constrained('driver_groups')->cascadeOnDelete();
                $t->foreignId('client_tariff_id')
                    ->constrained('client_tariffs')->cascadeOnDelete();

                // короткое имя индекса, чтобы не упираться в лимит 64 символа
                $t->unique(
                    ['driver_group_id', 'client_tariff_id'],
                    'dg_ct_unique'
                );

                $t->timestamps();
            });
        } else {
            // Таблица уже есть (как на stage) — добавляем/гарантируем индекс
            Schema::table('driver_group_client_tariff', function (Blueprint $t) {
                $t->unique(
                    ['driver_group_id', 'client_tariff_id'],
                    'dg_ct_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('driver_group_client_tariff')) {
            Schema::drop('driver_group_client_tariff');
        }
        if (Schema::hasTable('driver_groups')) {
            Schema::drop('driver_groups');
        }
    }
};
