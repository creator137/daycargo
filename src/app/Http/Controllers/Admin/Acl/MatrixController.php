<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MatrixController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $perms = Permission::where('guard_name', 'web')->orderBy('name')->get();

        // сгруппируем права по префиксу до первой точки
        $grouped = $perms->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'misc';
        })->sortKeys();

        return view('admin.acl.matrix.index', compact('roles', 'perms', 'grouped'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'matrix' => ['nullable', 'array'],
        ]);

        $roles = Role::where('guard_name', 'web')->get()->keyBy('id');
        $perms = Permission::where('guard_name', 'web')->get()->keyBy('id');

        // matrix[permission_id][role_id] = "on"
        foreach ($perms as $permId => $perm) {
            $activeForPerm = array_keys($data['matrix'][$permId] ?? []);
            foreach ($roles as $roleId => $role) {
                if (in_array($roleId, $activeForPerm)) {
                    $role->givePermissionTo($perm);
                } else {
                    $role->revokePermissionTo($perm);
                }
            }
        }

        return back()->with('success', 'Матрица обновлена.');
    }
}
