<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $t) {
            $t->id();
            $t->string('city')->nullable()->index();
            $t->enum('client_type', ['person', 'company'])->default('person')->index(); // физ/юр
            $t->boolean('is_agent')->default(false)->index(); // фильтр пользователи/агенты
            $t->string('lang', 5)->default('ru');

            $t->string('full_name')->nullable();
            $t->date('birth_date')->nullable();

            $t->string('phone', 32)->unique();
            $t->string('email')->nullable()->unique();

            $t->string('passport_series', 16)->nullable();
            $t->string('passport_number', 32)->nullable();

            $t->string('photo_path')->nullable();
            $t->text('comment')->nullable();

            // опции
            $t->boolean('send_trip_report')->default(false);
            $t->boolean('news_notifications')->default(false);
            $t->boolean('allow_push')->default(true);

            $t->boolean('blacklisted')->default(false)->index();
            $t->decimal('credit_limit', 12, 2)->default(0);

            // на будущее, пока простое поле (потом переведём на проводки)
            $t->decimal('balance', 12, 2)->default(0);

            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
