<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $t) {
            $t->id();
            $t->string('city', 100);

            $t->string('full_name');
            $t->string('short_name')->nullable();

            $t->string('legal_address')->nullable();
            $t->string('postal_address')->nullable();

            $t->string('director_name')->nullable();
            $t->string('director_position')->nullable();
            $t->string('chief_accountant')->nullable();

            $t->string('inn', 20)->nullable();
            $t->string('kpp', 20)->nullable();
            $t->string('ogrn', 20)->nullable();

            $t->string('bank_name')->nullable();
            $t->string('bank_account', 34)->nullable();
            $t->string('bank_corr', 34)->nullable();
            $t->string('bank_bik', 16)->nullable();

            $t->string('phone', 50)->nullable();
            $t->string('email')->nullable();
            $t->string('site')->nullable();

            $t->string('contact_person')->nullable();
            $t->string('contact_phone', 50)->nullable();
            $t->string('contact_email')->nullable();

            $t->string('contract_number')->nullable();
            $t->date('contract_from')->nullable();
            $t->date('contract_to')->nullable();
            $t->unsignedTinyInteger('billing_period_months')->nullable();

            $t->decimal('credit_limit', 12, 2)->default(0);
            $t->boolean('active')->default(true);
            $t->decimal('balance', 12, 2)->default(0);

            $t->text('comment')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
