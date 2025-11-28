<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cancel_reasons', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();                 // системный код (slug)
            $t->string('title');                          // отображаемое имя
            $t->enum('initiator', ['customer', 'driver', 'dispatcher', 'system', 'integration'])->default('customer');
            $t->unsignedSmallInteger('window_minutes')->default(10);   // окно применения штрафа
            // Штрафы со стороны клиента
            $t->decimal('client_fee_fixed', 10, 2)->nullable();        // фикс сумма (если задана)
            $t->decimal('client_fee_percent', 5, 2)->nullable();       // % от стоимости (если задан)
            // Штрафы со стороны водителя
            $t->decimal('driver_fee_fixed', 10, 2)->nullable();
            $t->decimal('driver_fee_percent', 5, 2)->nullable();
            $t->decimal('driver_fee_min', 10, 2)->nullable();          // минималка к проценту
            $t->boolean('active')->default(true);
            $t->unsignedSmallInteger('sort')->default(100);
            $t->text('comment')->nullable();                           // пояснения/заметки
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cancel_reasons');
    }
};
