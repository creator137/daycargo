<?php

namespace Database\Seeders;

use App\Models\CancelReason;
use Illuminate\Database\Seeder;

class CancelReasonSeeder extends Seeder
{
    public function run(): void
    {
        // Клиент отменил менее чем за 10 минут — фикс спишем (вводится админом)
        CancelReason::updateOrCreate(
            ['code' => 'customer_false_call'],
            [
                'title' => 'Отмена клиентом в окне (ложный вызов)',
                'initiator' => 'customer',
                'window_minutes' => 10,
                'client_fee_fixed' => 0, // админ затем задаст реальную сумму
                'active' => true,
                'sort' => 10,
                'comment' => 'Списание по эквайрингу за ложный вызов при отмене в окне.'
            ]
        );

        // Водитель отменил — 10% от заказа, минимум 800 ₽
        CancelReason::updateOrCreate(
            ['code' => 'driver_cancel_penalty'],
            [
                'title' => 'Отмена водителем',
                'initiator' => 'driver',
                'window_minutes' => 999, // всегда
                'driver_fee_percent' => 10,
                'driver_fee_min' => 800,
                'active' => true,
                'sort' => 20,
                'comment' => 'Штраф 10% от стоимости, но не менее 800 ₽.'
            ]
        );
    }
}
