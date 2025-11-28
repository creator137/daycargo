<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\City;
use App\Models\Client;
use App\Models\ClientDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ClientWalletOperationRequest;
use App\Http\Requests\ClientBonusGrantRequest;
use App\Models\WalletTransaction;
use App\Models\BonusEntry;
use App\Http\Requests\ClientPromoApplyRequest;
use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    private function citiesOptions(): array
    {
        return City::where('active', true)->orderBy('sort')->orderBy('name')->pluck('name', 'name')->toArray();
    }

    public function index(Request $req)
    {
        $tab = $req->string('tab')->toString() ?: 'registered'; // registered|unregistered
        $citiesOptions = $this->citiesOptions();

        // быстрые пресеты периода: today,7d,30d (если даты не заданы явно)
        $period = $req->string('period')->toString();
        $from = $req->date('created_from');
        $to   = $req->date('created_to');

        if (!$from && !$to && in_array($period, ['today', '7d', '30d'])) {
            if ($period === 'today') {
                $from = now()->startOfDay();
                $to = now()->endOfDay();
            }
            if ($period === '7d') {
                $from = now()->subDays(7)->startOfDay();
                $to = now()->endOfDay();
            }
            if ($period === '30d') {
                $from = now()->subDays(30)->startOfDay();
                $to = now()->endOfDay();
            }
        }

        if ($tab === 'unregistered') {
            $q = ClientDevice::query()->whereNull('client_id');
            if ($req->filled('city'))  $q->where('city', $req->string('city'));
            if ($req->filled('search')) {
                $s = '%' . $req->string('search') . '%';
                $q->where(function ($w) use ($s) {
                    $w->where('device_model', 'like', $s)
                        ->orWhere('platform', 'like', $s)
                        ->orWhere('push_token', 'like', $s);
                });
            }
            if ($from) $q->where('created_at', '>=', $from);
            if ($to)   $q->where('created_at', '<=', $to);

            $items = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

            $counts = [
                'registered'   => Client::count(),
                'unregistered' => ClientDevice::whereNull('client_id')->count(),
            ];

            return view('admin.clients.index', compact('tab', 'items', 'counts', 'citiesOptions', 'period'));
        }

        // registered
        $role = $req->string('role')->toString();           // all|users|agents
        $black = $req->string('black')->toString();         // all|black|white
        $type = $req->string('client_type')->toString();    // ''|'person'|'company'

        $q = Client::query();

        if ($role === 'agents')  $q->where('is_agent', true);
        if ($role === 'users')   $q->where('is_agent', false);
        if ($req->filled('city')) $q->where('city', $req->string('city'));
        if (in_array($type, ['person', 'company'])) $q->where('client_type', $type);

        if ($black === 'black') $q->where('blacklisted', true);
        elseif ($black === 'white') $q->where('blacklisted', false);

        if ($req->filled('search')) {
            $s = '%' . $req->string('search') . '%';
            $q->where(function ($w) use ($s) {
                $w->where('full_name', 'like', $s)->orWhere('phone', 'like', $s)->orWhere('email', 'like', $s);
            });
        }
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);

        $items = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        $counts = [
            'registered'   => Client::count(),
            'unregistered' => ClientDevice::whereNull('client_id')->count(),
        ];

        return view('admin.clients.index', compact('tab', 'items', 'counts', 'citiesOptions', 'role', 'black', 'type', 'period'));
    }

    public function create()
    {
        $client = new Client([
            'client_type' => 'person',
            'lang' => 'ru',
            'allow_push' => true,
            'send_trip_report' => false,
            'news_notifications' => false,
            'blacklisted' => false,
            'credit_limit' => 0,
        ]);

        $citiesOptions = $this->citiesOptions();
        return view('admin.clients.form', compact('client', 'citiesOptions'));
    }

    public function store(ClientRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('photo')) {
            $data['photo_path'] = $req->file('photo')->store('clients', 'public');
        }
        Client::create($data);
        return redirect()->route('admin.clients.index', ['tab' => 'registered'])->with('success', 'Клиент создан.');
    }

    public function edit(Client $client)
    {
        $citiesOptions = $this->citiesOptions();
        return view('admin.clients.form', compact('client', 'citiesOptions'));
    }

    public function update(ClientRequest $req, Client $client)
    {
        $data = $req->validated();
        if ($req->hasFile('photo')) {
            $new = $req->file('photo')->store('clients', 'public');
            if ($client->photo_path) Storage::disk('public')->delete($client->photo_path);
            $data['photo_path'] = $new;
        }
        $client->update($data);
        return redirect()->route('admin.clients.index', ['tab' => 'registered'])->with('success', 'Сохранено.');
    }

    public function destroy(Client $client)
    {
        if ($client->photo_path) Storage::disk('public')->delete($client->photo_path);
        $client->delete();
        return back()->with('success', 'Удалён.');
    }

    public function toggleBlacklist(Client $client)
    {
        $this->authorize('toggle', $client); // policy + 'clients.toggle'
        $client->blacklisted = ! $client->blacklisted;
        $client->save();

        return back()->with('success', $client->blacklisted ? 'Клиент добавлен в ЧС.' : 'Клиент убран из ЧС.');
    }

    /** POST /admin/clients/{client}/wallet */
    public function walletOperation(ClientWalletOperationRequest $req, Client $client)
    {
        $data = $req->validated();
        $isDebit = $data['operation'] === 'debit';
        $amount  = (float) $data['amount'];

        DB::transaction(function () use ($client, $data, $isDebit, $amount) {
            // Изменяем агрегаты на клиенте
            if ($data['wallet'] === 'money') {
                $delta = $isDebit ? -$amount : $amount;
                $client->increment('balance', $delta);
            } else { // bonus
                $delta = $isDebit ? -$amount : $amount;
                $client->increment('bonus_balance', $delta);

                // лог в подробную таблицу бонусов
                BonusEntry::create([
                    'client_id'    => $client->id,
                    'type'         => $isDebit ? 'spend' : 'earn',
                    'points'       => $amount,
                    'source'       => 'manual',
                    'comment'      => $data['comment'] ?? null,
                    'performed_by' => Auth::id(),
                ]);
            }

            // универсальная запись в журнал кошельков
            WalletTransaction::create([
                'owner_type'   => $client::class,
                'owner_id'     => $client->id,
                'wallet'       => $data['wallet'],            // money|bonus
                'operation'    => $data['operation'],         // topup|debit
                'amount'       => $amount,
                'currency'     => 'RUB',
                'comment'      => $data['comment'] ?? null,
                'performed_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Операция выполнена.');
    }

    /** POST /admin/clients/{client}/bonus/grant */
    public function bonusGrant(ClientBonusGrantRequest $req, Client $client)
    {
        $data   = $req->validated();
        $points = (float) $data['points'];

        DB::transaction(function () use ($client, $data, $points) {
            $client->increment('bonus_balance', $points);

            BonusEntry::create([
                'client_id'    => $client->id,
                'type'         => 'earn',
                'points'       => $points,
                'source'       => $data['source'] ?? 'manual',
                'expires_at'   => $data['expires_at'] ?? null,
                'comment'      => $data['comment'] ?? null,
                'performed_by' => Auth::id(),
            ]);

            WalletTransaction::create([
                'owner_type'   => $client::class,
                'owner_id'     => $client->id,
                'wallet'       => 'bonus',
                'operation'    => 'topup',
                'amount'       => $points,
                'currency'     => 'RUB',
                'comment'      => $data['comment'] ?? null,
                'performed_by' => Auth::id(),
                'meta'         => ['source' => $data['source'] ?? 'manual'],
            ]);
        });

        return back()->with('success', 'Бонусы начислены.');
    }

    public function promoApply(ClientPromoApplyRequest $req, Client $client)
    {
        $code = mb_strtoupper(trim($req->input('code')));

        $promo = PromoCode::whereRaw('upper(code) = ?', [$code])->first();
        if (!$promo) {
            return back()->with('error', 'Промокод не найден.');
        }
        if (!$promo->active) {
            return back()->with('error', 'Промокод выключен.');
        }
        if ($promo->starts_at && $promo->starts_at->isFuture()) {
            return back()->with('error', 'Промокод ещё не активен.');
        }
        if ($promo->ends_at && $promo->ends_at->isPast()) {
            return back()->with('error', 'Срок действия промокода истёк.');
        }

        // Лимиты
        $totalUsed = $promo->redemptions()->where('status', 'applied')->count();
        if ($promo->usage_limit && $totalUsed >= $promo->usage_limit) {
            return back()->with('error', 'Достигнут общий лимит промокода.');
        }
        $usedByClient = $promo->redemptions()->where('client_id', $client->id)->where('status', 'applied')->count();
        if ($promo->per_client_limit && $usedByClient >= $promo->per_client_limit) {
            return back()->with('error', 'Лимит на клиента исчерпан.');
        }

        // Рассчёт начисления (пока работаем только с бонусами)
        $granted = 0.0;
        if ($promo->type === 'bonus_fixed') {
            $granted = (float)($promo->value ?? 0);
        } elseif ($promo->type === 'bonus_percent') {
            // проценты будем трактовать как «процент от условной базы»
            // пока базы нет — безопасно отклоняем:
            return back()->with('error', 'Промокод процентного типа можно применить на этапе расчёта заказа.');
        } elseif ($promo->type === 'free_delivery') {
            // пока нет заказов — отмечаем применённым без начисления
            $granted = 0.0;
        }

        DB::transaction(function () use ($promo, $client, $granted, $req) {
            // Начисляем бонусы (если есть)
            if ($granted > 0) {
                $client->increment('bonus_balance', $granted);

                BonusEntry::create([
                    'client_id'    => $client->id,
                    'type'         => 'earn',
                    'points'       => $granted,
                    'source'       => 'promo',
                    'comment'      => 'Промокод ' . $promo->code,
                    'performed_by' => Auth::id(),
                ]);

                WalletTransaction::create([
                    'owner_type'   => $client::class,
                    'owner_id'     => $client->id,
                    'wallet'       => 'bonus',
                    'operation'    => 'topup',
                    'amount'       => $granted,
                    'currency'     => 'RUB',
                    'comment'      => 'Промокод ' . $promo->code . '; ' . $req->input('comment'),
                    'performed_by' => Auth::id(),
                    'meta'         => ['promo_code_id' => $promo->id],
                ]);
            }

            PromoCodeRedemption::create([
                'promo_code_id' => $promo->id,
                'client_id'     => $client->id,
                'status'        => 'applied',
                'amount'        => $granted,
                'performed_by'  => Auth::id(),
                'meta'          => ['comment' => $req->input('comment')],
            ]);
        });

        return back()->with('success', $granted > 0 ? "Начислено {$granted} бонусов." : 'Промокод применён.');
    }
}
