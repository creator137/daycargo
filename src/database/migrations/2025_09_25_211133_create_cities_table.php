<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('slug')->unique();
            $t->unsignedSmallInteger('sort')->default(100);
            $t->boolean('active')->default(true);
            $t->timestamps();

            $t->index(['active', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
