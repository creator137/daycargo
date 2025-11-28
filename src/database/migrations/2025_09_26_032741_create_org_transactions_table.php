<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('org_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['topup', 'debit']);
            $t->decimal('amount', 12, 2);
            $t->string('comment', 1000)->nullable();
            $t->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->index(['organization_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_transactions');
    }
};
