<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->constrained()->cascadeOnDelete();

            $table->string('city');                     // город работы (обяз.)
            $table->string('owner_type')->default('private'); // company|private|rent
            $table->boolean('is_rent')->default(false); // флаг "арендный"

            $table->string('brand');                    // марка
            $table->string('model');                    // модель
            $table->unsignedSmallInteger('year')->nullable();

            $table->string('color')->nullable();
            $table->string('license_plate')->unique();  // госномер
            $table->string('vin')->nullable();

            $table->string('photo_path')->nullable();   // главное фото (опц.)
            $table->json('options')->nullable();        // любые флаги: рефрижератор/тент/борт...

            // статус: active / blocked / pending (неактивирован)
            $table->enum('status', ['active', 'blocked', 'pending'])->default('pending');

            $table->text('comment')->nullable();

            $table->timestamps();

            // Индексы под фильтры
            $table->index(['city', 'status']);
            $table->index(['vehicle_type_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index('owner_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
