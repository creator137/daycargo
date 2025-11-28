<?php

namespace App\Services;

use App\Models\BonusEntry;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    /** Универсальная корректировка баланса (с записью транзакции). */
    public function adjust(Wallet $wallet, float $amount, string $kind, array $meta = []): WalletTransaction
    {
        if ($amount == 0.0) {
            throw new InvalidArgumentException('Amount must be non-zero.');
        }

        return DB::transaction(function () use ($wallet, $amount, $kind, $meta) {
            // Обновляем баланс атомарно
            $wallet->refresh(); // чтобы не было гонок
            $wallet->balance = bcadd($wallet->balance, $amount, 2);
            $wallet->save();

            return $wallet->transactions()->create([
                'kind'         => $kind,
                'amount'       => $amount, // со знаком
                'performed_by' => Auth::id(),
                'meta'         => $meta,
            ]);
        });
    }

    public function topup(Wallet $wallet, float $amount, array $meta = []): WalletTransaction
    {
        return $this->adjust($wallet, abs($amount), 'topup', $meta);
    }

    public function debit(Wallet $wallet, float $amount, array $meta = []): WalletTransaction
    {
        return $this->adjust($wallet, -abs($amount), 'debit', $meta);
    }

    /** Начисление бонусов с опциональным сроком жизни (создаёт BonusEntry + транзакцию). */
    public function grantBonus(Wallet $bonusWallet, float $amount, ?int $lifetimeDays = null, string $reason = 'bonus'): WalletTransaction
    {
        return DB::transaction(function () use ($bonusWallet, $amount, $lifetimeDays, $reason) {
            $tx = $this->adjust($bonusWallet, abs($amount), 'bonus_accrual', ['reason' => $reason]);

            $bonusWallet->bonusEntries()->create([
                'granted'    => abs($amount),
                'remaining'  => abs($amount),
                'reason'     => $reason,
                'expires_at' => $lifetimeDays ? now()->addDays($lifetimeDays) : null,
            ]);

            return $tx;
        });
    }

    /** Списание бонусов FIFO по невышедшим entry (возвращает фактическую сумму списания). */
    public function spendBonusFIFO(Wallet $bonusWallet, float $amount, array $meta = []): float
    {
        $need = abs($amount);
        $spent = 0.0;

        DB::transaction(function () use ($bonusWallet, $need, &$spent, $meta) {
            $entries = $bonusWallet->bonusEntries()
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderBy('expires_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($entries as $e) {
                if ($spent >= $need) break;
                $take = min($e->remaining, $need - $spent);
                if ($take <= 0) continue;

                $e->remaining = bcsub($e->remaining, $take, 2);
                $e->save();
                $spent = bcadd($spent, $take, 2);
            }

            if ($spent > 0) {
                $this->adjust($bonusWallet, -$spent, 'bonus_spend', $meta);
            }
        });

        return (float)$spent;
    }
}
