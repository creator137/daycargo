<?php

namespace App\Http\Controllers\Admin\Acl;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acl\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $q = Permission::where('guard_name', 'web')
            ->when($request->filled('search'), fn($qq) =>
            $qq->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('name');

        $items = $q->paginate(50)->withQueryString();

        return view('admin.acl.permissions.index', compact('items'));
    }

    public function create()
    {
        $perm = new Permission(['guard_name' => 'web']);
        return view('admin.acl.permissions.form', compact('perm'));
    }

    public function store(PermissionRequest $request)
    {
        Permission::create($request->validated() + ['guard_name' => 'web']);
        return redirect()->route('admin.acl.permissions.index')->with('success', 'Право создано.');
    }

    public function edit(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);
        return view('admin.acl.permissions.form', ['perm' => $permission]);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);
        $permission->update($request->validated());
        return redirect()->route('admin.acl.permissions.index')->with('success', 'Право сохранено.');
    }

    public function destroy(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);
        $permission->delete();
        return back()->with('success', 'Удалено.');
    }
}
