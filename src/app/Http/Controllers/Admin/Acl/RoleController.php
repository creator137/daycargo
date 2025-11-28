<?php

// app/Http/Controllers/Admin/Acl/RoleController.php
namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()->orderBy('name')->get();
        return view('admin.acl.roles.index', compact('roles'));
    }

    public function create()
    {
        $role = new Role();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        [$groups, $ruGroups] = $this->groupPermissions($permissions);

        return view('admin.acl.roles.form', [
            'role'      => $role,
            'action'    => route('admin.acl.roles.store'),
            'method'    => 'POST',
            'groups'    => $groups,
            'ruGroups'  => $ruGroups,
            'selected'  => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $role = Role::query()->create([
            'name'         => $data['name'],           // тех. код (латиницей)
            'guard_name'   => 'web',
            'display_name' => $data['display_name'] ?? null, // русское имя
            'description'  => $data['description'] ?? null,
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.acl.roles.index')->with('success', 'Роль создана.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        [$groups, $ruGroups] = $this->groupPermissions($permissions);

        $selected = $role->permissions()->pluck('name')->all();

        return view('admin.acl.roles.form', [
            'role'      => $role,
            'action'    => route('admin.acl.roles.update', $role),
            'method'    => 'PUT',
            'groups'    => $groups,
            'ruGroups'  => $ruGroups,
            'selected'  => $selected,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validated($request, $role);

        $role->fill([
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? null,
            'description'  => $data['description'] ?? null,
        ])->save();

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.acl.roles.index')->with('success', 'Сохранено.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Удалено.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $id = $role?->id;

        return $request->validate([
            'name'        => [
                'required',
                'string',
                'max:64',
                Rule::unique('roles', 'name')->ignore($id),
                'regex:/^[a-z0-9_\-\.]+$/'
            ],
            'display_name' => ['nullable', 'string', 'max:255'], // русское
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);
    }

    /**
     * Группируем permission по префиксу до точки.
     * Возвращаем [ groups: ['drivers'=>Collection<Permission>, ...], ruGroups: ['drivers'=>'Исполнители (водители)', ...] ]
     * Для неизвестных префиксов — человекочитаемый title-case.
     */
    private function groupPermissions($permissions): array
    {
        $groups = [];
        foreach ($permissions as $p) {
            $key = $p->section ?: (
                str_starts_with($p->name, 'dicts.')
                ? implode('.', array_slice(explode('.', $p->name), 0, 2))
                : explode('.', $p->name)[0]
            );
            $groups[$key] ??= collect();
            $groups[$key]->push($p);
        }

        // Заголовки групп просто равны section (он уже на русском)
        $ruGroups = [];
        foreach ($groups as $k => $_) {
            $ruGroups[$k] = $k;
        }

        return [$groups, $ruGroups];
    }
}
