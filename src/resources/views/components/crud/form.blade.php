@props([
    'title',
    'action',
    'method' => 'POST', // 'POST' | 'PUT' | 'PATCH' | 'DELETE'
    'backUrl' => null,
    'description' => null,
    'hasFiles' => false, // <-- добавили
])

<section class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>

        @if ($backUrl)
            <a href="{{ $backUrl }}"
                class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm
                      bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                Назад
            </a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
        <form action="{{ $action }}" method="POST"
            @if ($hasFiles) enctype="multipart/form-data" @endif class="p-6 space-y-6">
            @csrf
            @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            {{-- Серверные ошибки (локализация ru/validation.php) --}}
            @if ($errors->any())
                <x-ui.alert variant="danger">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            {{ $slot }}

            <div class="pt-4 border-t border-slate-200">
                {{ $actions ?? '' }}
            </div>
        </form>
    </div>
</section>
