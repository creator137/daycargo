<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $t) {
            $t->id();

            // Основные
            $t->string('full_name')->nullable();          // ФИО
            $t->string('callsign')->nullable();           // позывной (уникальность не навязана, пусть будет опционально)
            $t->date('birth_date')->nullable();

            // Контакты
            $t->string('phone')->unique();                // обязателен
            $t->string('email')->nullable();

            // Города
            $t->json('cities')->nullable();               // массив строк
            $t->string('main_city')->nullable();          // главный город

            // Связи/классы
            $t->foreignId('vehicle_type_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('driver_group_id')->nullable()->constrained()->nullOnDelete();

            // Партнёр (пока без отдельной таблицы; поле для отображения + future FK)
            $t->unsignedBigInteger('partner_id')->nullable();
            $t->string('partner_name')->nullable();

            // Реквизиты/выплаты
            $t->decimal('balance', 12, 2)->default(0);
            $t->string('card_number')->nullable();
            $t->string('card_holder_latin')->nullable();
            $t->string('yandex_wallet')->nullable();

            // Прочее
            $t->enum('cooperation_type', ['ip', 'self_employed', 'company'])->nullable(); // ИП / Самозанятый / Компания
            $t->boolean('supports_terminal')->default(false);
            $t->string('sms_code')->nullable();            // фиксированный SMS-код (если используем)
            $t->string('app_password')->nullable();        // хеш пароля для приложения (не admin)
            $t->json('options')->nullable();               // опции исполнителя (чекбоксы на будущее)
            $t->string('color')->nullable();               // для фильтров/каталога

            $t->enum('status', ['pending', 'active', 'blocked'])->default('pending');
            $t->unsignedInteger('sort')->default(100);

            // аудит
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
