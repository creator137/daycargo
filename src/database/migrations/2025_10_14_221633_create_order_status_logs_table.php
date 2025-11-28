<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('status_from')->nullable();
            $t->string('status_to');
            $t->enum('actor_type', ['user', 'system', 'driver', 'client'])->default('user');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('comment', 1000)->nullable();
            $t->timestamps();
            $t->index(['order_id', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
