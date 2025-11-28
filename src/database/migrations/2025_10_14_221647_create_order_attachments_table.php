<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_attachments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->string('type', 64)->nullable(); // photo_before, photo_after, signature...
            $t->integer('size')->nullable();
            $t->unsignedBigInteger('created_by')->nullable(); // user id
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_attachments');
    }
};
