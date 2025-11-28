<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('type', 64); // created, broadcast_sent, driver_accepted, timeout...
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['order_id', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_events');
    }
};
