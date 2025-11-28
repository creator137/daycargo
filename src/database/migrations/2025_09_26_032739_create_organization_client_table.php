<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organization_client', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->boolean('is_admin')->default(false);
            $t->boolean('active')->default(true);
            $t->decimal('personal_limit', 12, 2)->nullable();
            $t->timestamps();

            $t->unique(['organization_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_client');
    }
};
