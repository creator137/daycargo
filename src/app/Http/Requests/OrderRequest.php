<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Политика на контроллере + тут базовая проверка
        return $this->user()?->can('orders.create') || $this->user()?->can('orders.update');
    }

    public function rules(): array
    {
        return [
            'city_id'            => ['nullable', 'integer', 'exists:cities,id'],
            'city'               => ['required', 'string', 'max:100'],

            'type'               => ['required', 'in:courier,now,schedule,cargo,move,intercity'],
            'source'             => ['nullable', 'in:web,app,phone,partner,admin'],
            'priority'           => ['nullable', 'integer', 'between:0,10'],
            'status'             => ['required', 'in:new,assigning,accepted,arrived,loading,driving,waiting,completed,cancelled,failed,refund'],

            'client_id'          => ['nullable', 'exists:clients,id'],
            'organization_id'    => ['nullable', 'exists:organizations,id'],
            'payer_type'         => ['required', 'in:client,organization,cashless,other'],

            'contact_name'       => ['nullable', 'string', 'max:255'],
            'contact_phone'      => ['nullable', 'string', 'max:32'],
            'blacklist_check'    => ['nullable', 'boolean'],

            'from_address'       => ['required', 'string', 'max:500'],
            'to_address'         => ['nullable', 'string', 'max:500'],
            'from_comment'       => ['nullable', 'string', 'max:1000'],
            'to_comment'         => ['nullable', 'string', 'max:1000'],
            'arrival_window_from' => ['nullable', 'date'],
            'arrival_window_to'  => ['nullable', 'date'],

            'via_points'         => ['nullable', 'array'],
            'via_points.*'       => ['string', 'max:500'],

            'tariff_id'          => ['nullable', 'exists:tariffs,id'],
            'vehicle_type_id'    => ['nullable', 'exists:vehicle_types,id'],
            'driver_group_id'    => ['nullable', 'exists:driver_groups,id'],

            'options'            => ['nullable', 'array'],
            'options.child_seat' => ['sometimes', 'boolean'],
            'options.wagon'      => ['sometimes', 'boolean'],
            'options.refrigerator' => ['sometimes', 'boolean'],

            'distance_km_est'    => ['nullable', 'numeric', 'min:0'],
            'duration_min_est'   => ['nullable', 'integer', 'min:0'],

            'driver_id'          => ['nullable', 'exists:drivers,id'],
            'vehicle_id'         => ['nullable', 'exists:vehicles,id'],

            'assign_strategy'    => ['nullable', 'in:manual,broadcast,nearest,group'],
            'broadcast_radius_km' => ['nullable', 'numeric', 'min:0'],

            'calc_schema'        => ['nullable', 'in:by_tariff,fixed,manual'],

            'price_base'         => ['nullable', 'numeric', 'min:0'],
            'price_surge'        => ['nullable', 'numeric', 'min:0'],
            'price_options'      => ['nullable', 'numeric', 'min:0'],
            'price_waiting'      => ['nullable', 'numeric', 'min:0'],
            'price_loading'      => ['nullable', 'numeric', 'min:0'],
            'price_other'        => ['nullable', 'numeric', 'min:0'],
            'price_discount'     => ['nullable', 'numeric', 'min:0'],
            'promo_discount'     => ['nullable', 'numeric', 'min:0'],
            'bonus_spent'        => ['nullable', 'numeric', 'min:0'],
            'price_total'        => ['nullable', 'numeric', 'min:0'],
            'currency'           => ['nullable', 'string', 'size:3'],

            'payment_method'     => ['required', 'in:cash,card,cashless,client_balance,org_balance'],
            'prepaid_amount'     => ['nullable', 'numeric', 'min:0'],
            'paid_amount'        => ['nullable', 'numeric', 'min:0'],
            'debt_amount'        => ['nullable', 'numeric', 'min:0'],

            'need_terminal'      => ['nullable', 'boolean'],
            'need_docs'          => ['nullable', 'boolean'],
            'fragile'            => ['nullable', 'boolean'],
            'lift_required'      => ['nullable', 'boolean'],
            'helper_count'       => ['nullable', 'integer', 'min:0', 'max:6'],
            'is_return_trip'     => ['nullable', 'boolean'],

            'comment'            => ['nullable', 'string', 'max:3000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $b = fn($k) => $this->boolean($k);

        $this->merge([
            'blacklist_check' => $b('blacklist_check'),
            'need_terminal'   => $b('need_terminal'),
            'need_docs'       => $b('need_docs'),
            'fragile'         => $b('fragile'),
            'lift_required'   => $b('lift_required'),
            'is_return_trip'  => $b('is_return_trip'),
        ]);
    }
}
