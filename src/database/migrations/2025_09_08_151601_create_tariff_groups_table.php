<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tariff_groups', function (Blueprint $t) {
            $t->id();
            $t->string('name', 255);
            $t->unsignedInteger('sort')->default(100);
            $t->text('description')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_groups');
    }
};
