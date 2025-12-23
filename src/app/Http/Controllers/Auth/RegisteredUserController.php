<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reg-email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'reg-user-type' => ['required', Rule::in(['physical', 'legal'])],
            'reg-password' => ['required', 'string', Rules\Password::defaults()],
            'reg-password-confirm' => ['required', 'same:reg-password'],
        ]);

        $email = $data['reg-email'];
        $role  = $data['reg-user-type']; // physical|legal

        $user = DB::transaction(function () use ($email, $role, $data) {

            $user = User::create([
                'name' => $email, // пока так, т.к. из формы имени нет
                'email' => $email,
                'password' => Hash::make($data['reg-password']),
            ]);

            // роли должны существовать (seed)
            $user->assignRole($role);

            // client_type в твоей БД: person|company
            $clientType = $role === 'legal' ? 'company' : 'person';

            Client::create([
                'user_id' => $user->id,
                'client_type' => $clientType,
                'lang' => 'ru',
                'email' => $email,
                'full_name' => null,
                'city' => null,
                'is_agent' => false,
                'allow_push' => true,
                'send_trip_report' => false,
                'news_notifications' => false,
                'blacklisted' => false,
                'credit_limit' => 0,
                'balance' => 0,
                // phone НЕ трогаем — должен быть nullable, иначе регистрация упадёт
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        // клиентские роли — в профиль
        return redirect()->route('profile.edit');
    }
}
