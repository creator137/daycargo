<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusRegisterRequest;
use App\Http\Requests\BusProfileRequest;
use App\Models\Client;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BusAuthController extends Controller
{
    /**
     * Шаг 1: регистрация (минимум данных) + выдача токена
     * Создаём только User и роль (physical|legal).
     */
    public function register(BusRegisterRequest $req)
    {
        $data = $req->validated();

        $email = mb_strtolower(trim($data['reg-email']));
        $pass  = $data['reg-password'];
        $type  = $data['reg-user-type']; // physical|legal

        /** @var User $user */
        $user = DB::transaction(function () use ($email, $pass, $type) {
            $roleName = $type === 'legal' ? 'legal' : 'physical';

            $user = User::create([
                'name' => $email,
                'email' => $email,
                'password' => Hash::make($pass),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole($roleName);
            }

            return $user;
        });

        $token = $user->createToken('bus')->plainTextToken;

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'role' => $type,
            'token' => $token,
        ], 201);
    }

    /**
     * Шаг 2: дозаполнение профиля
     * - physical: upsert Client по user_id
     * - legal: upsert Client + upsert Organization (по inn если есть) + привязка client к org
     */
    public function profile(BusProfileRequest $req)
    {
        /** @var User $user */
        $user = $req->user(); // sanctum
        $data = $req->validated();

        $role = method_exists($user, 'getRoleNames') ? ($user->getRoleNames()->first() ?: null) : null;
        if (!$role) {
            return response()->json([
                'ok' => false,
                'message' => 'User role not found.',
            ], 422);
        }

        [$client, $orgId] = DB::transaction(function () use ($user, $role, $data) {

            // client by user_id (важно: user_id не в fillable, поэтому только руками)
            $client = Client::where('user_id', $user->id)->first() ?: new Client();
            $client->user_id = $user->id;
            $client->email   = $user->email;

            if (!$client->exists) {
                $client->lang = $client->lang ?: 'ru';
                $client->allow_push = true;
                $client->send_trip_report = false;
                $client->news_notifications = false;
                $client->blacklisted = false;
                $client->credit_limit = 0;
                $client->balance = 0;
            }

            if (array_key_exists('phone', $data)) {
                $client->phone = $data['phone'] ?: null;
            }

            // -------- physical --------
            if ($role === 'physical') {
                $client->client_type = 'person';

                if (array_key_exists('person_full_name', $data)) {
                    $client->full_name = $data['person_full_name'] ?: null;
                }

                if (array_key_exists('person_birth_date', $data)) {
                    $client->birth_date = $data['person_birth_date'] ?: null;
                }

                $client->save();

                return [$client, null];
            }

            // -------- legal --------
            if ($role !== 'legal') {
                // на случай каких-то других ролей
                $client->save();
                return [$client, null];
            }

            $client->client_type = 'org';

            if (array_key_exists('legal_comment', $data)) {
                $client->comment = $data['legal_comment'] ?: null;
            }

            // опционально обновляем user.name
            if (!empty($data['legal_company_name'])) {
                $user->name = $data['legal_company_name'];
                $user->save();
            }

            $client->save();

            // org upsert (по inn если есть)
            $org = null;
            if (!empty($data['legal_inn'])) {
                $org = Organization::where('inn', $data['legal_inn'])->first();
            }

            if (!$org) {
                $org = new Organization();
                $org->billing_period_months = 1;
                $org->credit_limit = 0;
                $org->balance = 0;
                $org->active = true;
            }

            if (!empty($data['legal_company_name'])) {
                $org->full_name = $data['legal_company_name'];
            } elseif (!$org->full_name) {
                $org->full_name = $user->email;
            }

            if (!empty($data['legal_inn'])) {
                $org->inn = $data['legal_inn'];
            }

            // поддержка legal_kpp и legacy legal_cpp
            $kpp = $data['legal_kpp'] ?? ($data['legal_cpp'] ?? null);
            if (!empty($kpp)) {
                $org->kpp = $kpp;
            }

            if (!empty($data['legal_company_address'])) {
                $org->legal_address = $data['legal_company_address'];
            }

            if (!empty($data['legal_contact_name'])) {
                $client->full_name = $data['legal_contact_name'];
            }

            if (array_key_exists('legal_contact_position', $data)) {
                $org->contact_position = $data['legal_contact_position'] ?: null;
            }

            $org->contact_email = $user->email;

            if (array_key_exists('legal_comment', $data)) {
                $org->comment = $data['legal_comment'] ?: null;
            }

            $org->save();

            // привязка client к org как админ
            $org->employees()->syncWithoutDetaching([
                $client->id => [
                    'is_admin' => true,
                    'active' => true,
                    'personal_limit' => null,
                ]
            ]);

            return [$client, $org->id];
        });

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'role' => $role,
            'client_id' => $client->id,
            'organization_id' => $orgId,
        ]);
    }
}
