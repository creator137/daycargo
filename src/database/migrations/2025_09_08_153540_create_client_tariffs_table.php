<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_tariffs', function (Blueprint $t) {
            $t->id();
            $t->string('name', 255);

            // Связи
            $t->foreignId('tariff_group_id')->nullable()->constrained('tariff_groups')->nullOnDelete();
            $t->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();

            // Применимость
            $t->string('city', 100)->nullable();        // пока как строка; позже можно вынести в справочник городов
            $t->text('description')->nullable();

            // Доступность по каналам
            $t->boolean('available_site')->default(true);
            $t->boolean('available_app')->default(true);
            $t->boolean('available_dispatcher')->default(true);
            $t->boolean('available_driver')->default(false);
            $t->boolean('available_cabinet')->default(true);

            // Поведение
            $t->boolean('require_prepayment')->default(false);
            $t->unsignedTinyInteger('addresses_min')->default(1); // мин. число адресов в заказе

            // Прочее
            $t->unsignedInteger('sort')->default(100);
            $t->boolean('active')->default(true);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_tariffs');
    }
};
