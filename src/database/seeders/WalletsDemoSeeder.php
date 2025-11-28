<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Organization;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Database\Seeder;

class WalletsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $svc = app(WalletService::class);

        // Клиенты
        Client::query()->take(5)->get()->each(function ($c) use ($svc) {
            $cash  = $c->wallets()->firstOrCreate(['type' => 'cash'],  ['currency' => 'RUB', 'balance' => 0]);
            $bonus = $c->wallets()->firstOrCreate(['type' => 'bonus'], ['currency' => 'RUB', 'balance' => 0]);

            $svc->topup($cash, 500, ['seed' => true, 'note' => 'Демо пополнение']);
            $svc->grantBonus($bonus, 150, 90, 'welcome_bonus');
        });

        // Организации (если есть)
        Organization::query()->take(3)->get()->each(function ($o) use ($svc) {
            $cash = $o->wallets()->firstOrCreate(['type' => 'cash'], ['currency' => 'RUB', 'balance' => 0]);
            $svc->topup($cash, 10000, ['seed' => true, 'note' => 'Стартовый баланс']);
        });
    }
}
