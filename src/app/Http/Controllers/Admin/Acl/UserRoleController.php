<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acl\UserRoleUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim((string) $request->get('search', ''));
        $roleId     = $request->get('role_id');          // id роли
        $roleName   = $request->get('role');             // name роли (admin/owner/...)
        $clientType = $request->get('client_type');      // person/org/guest
        $hasClient  = $request->boolean('has_client', false);
        $blacklisted = $request->boolean('blacklisted', false);

        $sortBy  = (string) $request->get('sort_by', 'id');
        $sortDir = strtolower((string) $request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSort = ['id', 'name', 'email', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) $sortBy = 'id';

        $q = User::query()
            ->with([
                'roles:id,name,display_name',
                'clientByUserId:id,user_id,full_name,phone,client_type,blacklisted,email,is_agent',
                'clientByClientId:id,full_name,phone,client_type,blacklisted,email,is_agent',
            ]);

        // Поиск
        if ($search !== '') {
            $s = "%{$search}%";
            $q->where(function ($w) use ($s) {
                $w->where('users.name', 'like', $s)
                    ->orWhere('users.email', 'like', $s)
                    ->orWhereHas('clientByUserId', function ($c) use ($s) {
                        $c->where('full_name', 'like', $s)->orWhere('phone', 'like', $s);
                    })
                    ->orWhereHas('clientByClientId', function ($c) use ($s) {
                        $c->where('full_name', 'like', $s)->orWhere('phone', 'like', $s);
                    });
            });
        }

        // Фильтр по роли (id)
        if (!empty($roleId)) {
            $q->whereHas('roles', fn($r) => $r->where('roles.id', $roleId));
        }

        // Фильтр по роли (name) + поддержка "none"
        if (!empty($roleName) && $roleName !== 'any') {
            if ($roleName === 'none') {
                $q->whereDoesntHave('roles');
            } else {
                $q->whereHas('roles', fn($r) => $r->where('roles.name', $roleName));
            }
        }

        // Только с клиентом
        if ($hasClient) {
            $q->where(function ($w) {
                $w->whereHas('clientByUserId')
                    ->orWhereHas('clientByClientId');
            });
        }

        // Тип клиента (person/org/guest)
        if (!empty($clientType) && $clientType !== 'any') {
            $q->where(function ($w) use ($clientType) {
                $w->whereHas('clientByUserId', fn($c) => $c->where('client_type', $clientType))
                    ->orWhereHas('clientByClientId', fn($c) => $c->where('client_type', $clientType));
            });
        }

        // Чёрный список
        if ($blacklisted) {
            $q->where(function ($w) {
                $w->whereHas('clientByUserId', fn($c) => $c->where('blacklisted', 1))
                    ->orWhereHas('clientByClientId', fn($c) => $c->where('blacklisted', 1));
            });
        }

        $q->orderBy($sortBy, $sortDir);

        $users = $q->paginate(20)->withQueryString();

        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name', 'display_name']);

        // Статистика (группируем OR!)
        $stats = [
            'total' => User::count(),
            'with_roles' => User::has('roles')->count(),
            'without_roles' => User::doesntHave('roles')->count(),
            'with_client' => User::where(function ($w) {
                $w->whereHas('clientByUserId')
                    ->orWhereHas('clientByClientId');
            })->count(),
            'blacklisted' => User::where(function ($w) {
                $w->whereHas('clientByUserId', fn($q) => $q->where('blacklisted', 1))
                    ->orWhereHas('clientByClientId', fn($q) => $q->where('blacklisted', 1));
            })->count(),
        ];

        return view('admin.acl.users.index', compact('users', 'roles', 'stats'));
    }

    public function show(User $user)
    {
        $user->load([
            'roles:id,name,display_name',
            'clientByUserId:id,user_id,full_name,phone,client_type,blacklisted,email,is_agent',
            'clientByClientId:id,full_name,phone,client_type,blacklisted,email,is_agent',
        ]);

        return view('admin.acl.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load([
            'roles:id,name,display_name',
            'clientByUserId:id,user_id,full_name,phone,client_type,blacklisted,email,is_agent',
            'clientByClientId:id,full_name,phone,client_type,blacklisted,email,is_agent',
        ]);

        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name', 'display_name']);

        $selected = $user->roles()->pluck('roles.id')->all();

        return view('admin.acl.users.edit', compact('user', 'roles', 'selected'));
    }

    public function update(UserRoleUpdateRequest $request, User $user)
    {
        $roleIds = $request->validated()['roles'] ?? [];

        $roles = Role::where('guard_name', 'web')
            ->whereIn('id', $roleIds)
            ->get();

        $user->syncRoles($roles);

        return redirect()
            ->route('admin.acl.users.index')
            ->with('success', 'Роли пользователя обновлены.');
    }
}
