<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();

            // Идентификация
            $t->string('number')->unique();                 // ORD-2025-000123
            $t->string('city');                             // дублирование названия
            $t->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            // Тип/источник/приоритет
            $t->enum('type', ['now', 'preorder', 'offer'])->default('now')->index();
            $t->enum('source', ['operator', 'client_app', 'site', 'api', 'partner'])->default('operator')->index();
            $t->tinyInteger('priority')->default(0)->index(); // 0 обычный, 1 внимание, 2 срочно

            // Статус и временные точки
            $t->enum('status', [
                'new',
                'search',
                'assigned',
                'en_route',
                'loading',
                'in_progress',
                'waiting',
                'paused',
                'completed',
                'canceled',
                'failed'
            ])->default('new')->index();
            $t->timestamp('assigned_at')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('finished_at')->nullable();
            $t->timestamp('canceled_at')->nullable();

            // Клиент/плательщик
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('payer_type', ['client', 'organization'])->default('client');
            $t->string('contact_name')->nullable();
            $t->string('contact_phone', 32)->nullable();
            $t->boolean('blacklist_check')->default(false);

            // Адреса и гео (from/to) + окно предзаказа
            $t->string('from_address');
            $t->decimal('from_lat', 10, 7)->nullable();
            $t->decimal('from_lng', 10, 7)->nullable();
            $t->string('from_floor', 16)->nullable();
            $t->string('from_entrance', 16)->nullable();
            $t->string('from_comment', 255)->nullable();

            $t->string('to_address')->nullable();
            $t->decimal('to_lat', 10, 7)->nullable();
            $t->decimal('to_lng', 10, 7)->nullable();
            $t->string('to_floor', 16)->nullable();
            $t->string('to_entrance', 16)->nullable();
            $t->string('to_comment', 255)->nullable();

            $t->timestamp('arrival_window_from')->nullable();
            $t->timestamp('arrival_window_to')->nullable();

            $t->json('via_points')->nullable(); // [{address,lat,lng,comment,floor}]

            // Тариф/класс/опции/оценки маршрута
            $t->foreignId('tariff_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('vehicle_type_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('driver_group_id')->nullable()->constrained()->nullOnDelete();

            $t->json('options')->nullable(); // child_seat, wagon, refrigerator...
            $t->decimal('distance_km_est', 8, 2)->nullable();
            $t->integer('duration_min_est')->nullable();

            // Назначение/исполнитель
            $t->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('assign_strategy', ['manual', 'auto_broadcast', 'direct_offer'])->default('manual');
            $t->decimal('broadcast_radius_km', 5, 1)->nullable();
            $t->timestamp('broadcast_sent_at')->nullable();

            // Расчёт
            $t->enum('calc_schema', ['by_tariff', 'fixed_price', 'hourly', 'per_km', 'mixed'])->default('by_tariff');
            $t->decimal('price_base', 12, 2)->default(0);
            $t->decimal('price_surge', 12, 2)->default(0);
            $t->decimal('price_options', 12, 2)->default(0);
            $t->decimal('price_waiting', 12, 2)->default(0);
            $t->decimal('price_loading', 12, 2)->default(0);
            $t->decimal('price_other', 12, 2)->default(0);
            $t->decimal('price_discount', 12, 2)->default(0);
            $t->decimal('promo_discount', 12, 2)->default(0);
            $t->decimal('bonus_spent', 12, 2)->default(0);
            $t->decimal('price_total', 12, 2)->default(0);
            $t->string('currency', 3)->default('RUB');

            // Оплата
            $t->enum('payment_method', ['cash', 'cashless', 'card', 'org_balance', 'mixed'])->default('cash');
            $t->decimal('prepaid_amount', 12, 2)->default(0);
            $t->decimal('paid_amount', 12, 2)->default(0);
            $t->decimal('debt_amount', 12, 2)->default(0);
            $t->string('receipt_number')->nullable();

            $t->unsignedBigInteger('promo_code_id')->nullable(); // для последних применённых (без FK на случай очисток)

            // Флаги/прочее
            $t->boolean('need_terminal')->default(false);
            $t->boolean('need_docs')->default(false);
            $t->boolean('fragile')->default(false);
            $t->boolean('lift_required')->default(false);
            $t->tinyInteger('helper_count')->default(0);
            $t->boolean('is_return_trip')->default(false);

            $t->text('comment')->nullable();

            $t->timestamps();

            // Индексы для поиска
            $t->index(['status', 'created_at']);
            $t->index(['client_id', 'created_at']);
            $t->index(['driver_id', 'created_at']);
            $t->index(['organization_id', 'created_at']);
            $t->index(['city', 'created_at']);
            $t->index('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
