<nav x-data="{ open: false }" class="bg-gray-200 border-b border-gray-300">
    <div class="w-full px-4">
        <div class="flex justify-between items-center h-16">
            {{-- Лево: кнопка сжатия + логотип --}}
            <div class="flex items-center gap-2">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-300"
                    aria-label="Toggle sidebar" onclick="toggleSidebar()">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h12M4 18h8" />
                    </svg>
                </button>


                <a href="{{ route('admin.dashboard') }}" class="shrink-0 flex items-center">
                    @props(['class' => 'h-7 w-auto'])

                    <img src="{{ asset('images/logo_daycargo_hl.svg') }}" alt="Daycargo"
                        {{ $attributes->merge(['class' => $class]) }} />
                </a>
            </div>

            {{-- Право: профиль --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-transparent hover:text-gray-900 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @can('acl.manage')
                            <x-dropdown-link :href="route('admin.acl.roles.index')">
                                Роли и права
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.acl.users.index')">
                                Пользователи
                            </x-dropdown-link>
                        @endcan

                        <x-dropdown-link :href="route('profile.edit')">Профиль</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Выйти
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Моб.дропдаун профиля (оставили) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-700 hover:bg-gray-300 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-4 pb-1 border-t border-gray-300">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-600">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
