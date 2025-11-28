<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');      // morph: App\Models\Client / Organization
            $table->unsignedBigInteger('owner_id');
            $table->enum('type', ['cash', 'bonus'])->default('cash');
            $table->string('currency', 3)->default('RUB');
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['owner_type', 'owner_id', 'type'], 'wallets_owner_unique');
            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
