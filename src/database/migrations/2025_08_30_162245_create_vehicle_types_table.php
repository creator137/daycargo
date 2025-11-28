<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();        // S,M,L,XL,XXL
            $table->string('name');                      // Человекочитаемое имя
            $table->unsignedInteger('length_cm');       // длина, см
            $table->unsignedInteger('width_cm');        // ширина, см
            $table->unsignedInteger('height_cm');       // высота, см
            $table->unsignedInteger('capacity_kg');     // грузоподъёмность, кг
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehicle_types');
    }
};
