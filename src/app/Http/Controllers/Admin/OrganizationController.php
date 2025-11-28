<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Http\Requests\OrgTopupRequest;
use App\Http\Requests\OrgEmployeeAttachRequest;
use App\Models\Organization;
use App\Models\Client;
use App\Models\City;
use App\Models\OrgTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Organization::class, 'organization');
    }

    private function cityOptions(): array
    {
        return City::where('active', true)->orderBy('sort')->orderBy('name')
            ->pluck('name', 'name')->toArray();
    }

    public function index(Request $req)
    {
        $status = $req->string('status')->toString() ?: 'active';

        $q = Organization::query()
            ->when($status === 'active',  fn($qq) => $qq->where('active', true))
            ->when($status === 'blocked', fn($qq) => $qq->where('active', false))
            ->when($req->filled('city'), fn($qq) => $qq->where('city', $req->string('city')))
            ->when($req->filled('name'), fn($qq) => $qq->where(function ($w) use ($req) {
                $s = '%' . $req->string('name') . '%';
                $w->where('full_name', 'like', $s)->orWhere('short_name', 'like', $s);
            }))
            ->orderBy('full_name');

        $items = $q->paginate(20)->withQueryString();

        $tabs = [
            'active'  => Organization::where('active', true)->count(),
            'blocked' => Organization::where('active', false)->count(),
        ];

        $cities = $this->cityOptions();

        return view('admin.organizations.index', compact('items', 'tabs', 'status', 'cities'));
    }

    public function create()
    {
        $org = new Organization([
            'active' => true,
            'billing_period_months' => 1,
            'credit_limit' => 0,
            'balance' => 0,
        ]);

        return view('admin.organizations.form', [
            'org'    => $org,
            'cities' => $this->cityOptions(),
        ]);
    }

    public function store(OrganizationRequest $request)
    {
        Organization::create($request->validated());
        return redirect()->route('admin.organizations.index')->with('success', 'Организация создана.');
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.form', [
            'org'    => $organization,
            'cities' => $this->cityOptions(),
        ]);
    }

    public function update(OrganizationRequest $request, Organization $organization)
    {
        $organization->update($request->validated());
        return redirect()->route('admin.organizations.index')->with('success', 'Сохранено.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return back()->with('success', 'Удалено.');
    }

    public function toggle(Organization $organization)
    {
        $this->authorize('toggle', $organization);
        $organization->active = ! $organization->active;
        $organization->save();

        return back()->with('success', 'Статус изменён.');
    }

    public function topup(OrgTopupRequest $request, Organization $organization)
    {
        $this->authorize('balance', $organization);

        $data = $request->validated();
        $amount = (float)$data['amount'];
        if ($data['type'] === 'debit') {
            $amount = -$amount;
        }

        DB::transaction(function () use ($organization, $amount, $data) {
            $organization->increment('balance', $amount);

            OrgTransaction::create([
                'organization_id' => $organization->id,
                'type'            => $amount >= 0 ? 'topup' : 'debit',
                'amount'          => abs($amount),
                'comment'         => $data['comment'] ?? null,
                'performed_by'    => Auth::id(),
            ]);
        });

        return back()->with('success', 'Операция выполнена.');
    }

    /** список/форма сотрудников */
    public function employees(Organization $organization)
    {
        $this->authorize('employees', $organization);

        $employees = $organization->employees()->withCount(['devices'])->paginate(20);
        return view('admin.organizations.employees', compact('organization', 'employees'));
    }

    public function employeesAttach(OrgEmployeeAttachRequest $request, Organization $organization)
    {
        $this->authorize('employees', $organization);
        $data = $request->validated();

        $client = null;
        if (!empty($data['client_id'])) {
            $client = Client::find($data['client_id']);
        } elseif (!empty($data['phone'])) {
            $client = Client::where('phone', $data['phone'])->first();
        }

        if (!$client) {
            return back()->with('error', 'Клиент не найден.');
        }

        $organization->employees()->syncWithoutDetaching([
            $client->id => [
                'is_admin'       => (bool)($data['is_admin'] ?? false),
                'active'         => (bool)($data['active'] ?? true),
                'personal_limit' => $data['personal_limit'] ?? null,
            ]
        ]);

        return back()->with('success', 'Сотрудник добавлен.');
    }

    public function employeesDetach(Organization $organization, Client $client)
    {
        $this->authorize('employees', $organization);
        $organization->employees()->detach($client->id);
        return back()->with('success', 'Сотрудник удалён.');
    }
}
