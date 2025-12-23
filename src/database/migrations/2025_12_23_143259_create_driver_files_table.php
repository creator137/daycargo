<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();

            $table->string('type', 50);
            $table->string('path', 2048);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime', 100)->nullable();

            $table->timestamps();

            $table->index(['driver_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_files');
    }
};
