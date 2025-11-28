@extends('layouts.admin')
@section('page_title', 'Главная')
@section('content')
    <section class="space-y-4">
        <h1 class="text-lg font-semibold text-slate-900">Главная</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm text-slate-500">Быстрая сводка</div>
                <div class="mt-1 text-2xl font-semibold text-slate-900">—</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm text-slate-500">Заказы сегодня</div>
                <div class="mt-1 text-2xl font-semibold text-slate-900">—</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm text-slate-500">Выручка сегодня</div>
                <div class="mt-1 text-2xl font-semibold text-slate-900">—</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <div class="text-sm text-slate-600">
                Здесь можно вывести быстрые виджеты или мини-график. Подробная аналитика — в «Отчёты → Сводка».
            </div>
        </div>
    </section>
@endsection
