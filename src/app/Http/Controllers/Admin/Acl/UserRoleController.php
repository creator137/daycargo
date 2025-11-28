<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acl\UserRoleUpdateRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()
            ->when($request->filled('search'), function ($qq) use ($request) {
                $s = '%' . $request->string('search') . '%';
                $qq->where(fn($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s));
            })
            ->orderBy('name');

        $users = $q->paginate(20)->withQueryString();
        $roles = Role::where('guard_name', 'web')->orderBy('name')->pluck('name', 'id');

        return view('admin.acl.users.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->pluck('name', 'id');
        $selected = $user->roles()->pluck('roles.id')->all();
        return view('admin.acl.users.edit', compact('user', 'roles', 'selected'));
    }

    public function update(UserRoleUpdateRequest $request, User $user)
    {
        $roleIds = $request->validated()['roles'] ?? [];
        $roles = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);
        return redirect()->route('admin.acl.users.index')->with('success', 'Роли пользователя обновлены.');
    }
}
