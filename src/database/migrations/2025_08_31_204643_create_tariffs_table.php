<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $t->enum('scope_type', ['global', 'customer', 'integration'])->default('global');
            $t->unsignedBigInteger('scope_id')->nullable(); // id клиента или интеграции (для global = null)
            $t->string('city')->nullable();                 // пока строкой; потом заменим на city_id
            $t->decimal('base_price', 10, 2)->default(0);
            $t->decimal('per_km',     10, 2)->default(0);
            $t->decimal('per_min',    10, 2)->default(0);
            $t->decimal('min_price',  10, 2)->default(0);
            $t->unsignedSmallInteger('wait_free_min')->default(0);
            $t->decimal('wait_per_min', 10, 2)->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->index(['vehicle_type_id', 'scope_type', 'scope_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
