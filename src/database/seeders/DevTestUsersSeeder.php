<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // не пускать на продакшн случайно
        if (! app()->environment(['local', 'development'])) {
            $this->command->warn('DevTestUsersSeeder skipped (env != local/development)');
            return;
        }

        $pwd = env('DEV_TEST_PASSWORD', 'password'); // можно переопределить в .env

        $rows = [
            // владельца обычно уже создали — просто убедимся в роли
            ['name' => 'Owner',      'email' => 'owner@example.com',      'role' => 'owner',      'reset' => false],

            ['name' => 'Admin',      'email' => 'admin@example.com',      'role' => 'admin',      'reset' => true],
            ['name' => 'Viewer',     'email' => 'viewer@example.com',     'role' => 'viewer',     'reset' => true],
            ['name' => 'Accountant', 'email' => 'accountant@example.com', 'role' => 'accountant', 'reset' => true],
            ['name' => 'Driver',     'email' => 'driver@example.com',     'role' => 'driver',     'reset' => true],
        ];

        foreach ($rows as $r) {
            $user = User::firstOrCreate(
                ['email' => $r['email']],
                [
                    'name'              => $r['name'],
                    'password'          => Hash::make($pwd),
                    'email_verified_at' => now(),
                ]
            );

            // обновим имя/верификацию на всякий случай
            $user->forceFill([
                'name'              => $r['name'],
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            // пароль: для owner по умолчанию не трогаем
            if ($r['reset'] ?? false) {
                $user->forceFill(['password' => Hash::make($pwd)])->save();
            }

            // назначим роль (заменим все роли на данную)
            $user->syncRoles([$r['role']]);
        }

        $this->command->info('Dev test users seeded. Default password: ' . $pwd);
    }
}
