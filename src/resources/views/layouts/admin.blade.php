<x-app-layout>
    @stack('scripts')

    {{-- без header-слота --}}
    <div x-data="{
        collapsed: JSON.parse(localStorage.getItem('adminSidebarCollapsed') ?? 'false'),
        open: JSON.parse(localStorage.getItem('adminMenuOpen') ?? '{}'),
        toggleSection(k) {
            this.open[k] = !this.open[k];
            localStorage.setItem('adminMenuOpen', JSON.stringify(this.open));
        }
    }"
        @sidebar-toggle.window="
            collapsed = !collapsed;
            localStorage.setItem('adminSidebarCollapsed', JSON.stringify(collapsed));
        ">
        <div class="flex bg-gray-100 min-h-screen">
            {{-- Sidebar --}}
            <aside id="adminSidebar"
                class="bg-slate-900 text-slate-100 border-r border-slate-800
               transition-all duration-200 ease-in-out flex-shrink-0 overflow-y-auto"
                :class="collapsed ? 'w-16' : 'w-64'">

                @php
                    $menu = config('admin_menu');
                @endphp


                <nav class="py-3">
                    @foreach ($menu as $i => $item)
                        @php
                            $hasChildren = isset($item['children']);
                            $parentActive = false;

                            if ($hasChildren) {
                                foreach ($item['children'] as $child) {
                                    if (!empty($child['route']) && request()->routeIs($child['route'])) {
                                        $parentActive = true;
                                        break;
                                    }
                                }
                            } else {
                                $parentActive = !empty($item['route']) && request()->routeIs($item['route']);
                            }

                            $buttonBase =
                                'w-full flex items-center gap-3 px-3 py-2 rounded text-left hover:bg-slate-800 transition font-medium';
                            $buttonActive = $parentActive ? ' bg-slate-800' : '';
                        @endphp

                        {{-- Кнопка родителя --}}
                        <div class="px-2">
                            @if ($hasChildren)
                                <button @click="toggleSection('s{{ $i }}')"
                                    class="{{ $buttonBase }}{{ $buttonActive }}" title="{{ $item['label'] }}">
                                    <span class="text-lg">{{ $item['icon'] ?? '•' }}</span>
                                    <span x-show="!collapsed" x-transition.opacity class="flex-1 truncate">
                                        {{ $item['label'] }}
                                    </span>
                                    <svg x-show="!collapsed" class="h-4 w-4 opacity-70"
                                        :class="open['s{{ $i }}'] ? 'rotate-90' : ''" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd" d="M7 5l6 5-6 5V5z" />
                                    </svg>
                                </button>
                            @else
                                <a href="{{ route($item['route']) }}" class="{{ $buttonBase }}{{ $buttonActive }}"
                                    title="{{ $item['label'] }}">
                                    <span class="text-lg">{{ $item['icon'] ?? '•' }}</span>
                                    <span x-show="!collapsed" x-transition.opacity class="flex-1 truncate">
                                        {{ $item['label'] }}
                                    </span>
                                </a>
                            @endif
                        </div>

                        {{-- Подпункты (стилизовано) --}}
                        @if ($hasChildren)
                            <div x-show="open['s{{ $i }}'] || {{ $parentActive ? 'true' : 'false' }}"
                                x-cloak>
                                <ul x-show="!collapsed" x-transition.opacity class="mt-1 pl-9 pr-2 space-y-0.5">
                                    @foreach ($item['children'] as $child)
                                        @php($active = request()->routeIs($child['route']))
                                        <li>
                                            <a href="{{ route($child['route']) }}"
                                                class="group flex items-center gap-2 rounded-md px-3 py-2 text-[13px] leading-5
                                                      text-slate-300 hover:text-white hover:bg-slate-800/60
                                                      {{ $active ? 'bg-slate-800/70 text-white border-l-2 border-indigo-400 -ml-px' : '' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-slate-500 group-hover:bg-white"></span>
                                                <span class="truncate">{{ $child['label'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach

                    {{-- Пользователь --}}
                    <div class="mt-4 px-3 pb-3 text-xs text-slate-400" x-show="!collapsed" x-transition.opacity>
                        {{ auth()->user()->name }}
                        ({{ auth()->user()->getRoleNames()->implode(', ') }})
                    </div>
                </nav>
            </aside>

            {{-- Контент --}}
            <main class="flex-1 p-6 flex flex-col">
                <div class="bg-white rounded-lg shadow p-6 flex-1">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
