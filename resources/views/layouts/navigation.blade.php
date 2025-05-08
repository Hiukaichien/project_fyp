<nav x-data="{ open: false }" class="bg-gray-800 text-gray-100">
    <!-- Desktop Sidebar (Visible on sm screens and up) -->
    <div class="hidden sm:flex sm:flex-col sm:fixed sm:left-0 sm:top-0 sm:h-full sm:w-64 sm:bg-gray-800 sm:border-r sm:border-gray-700 sm:z-30 sm:overflow-y-auto">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 shrink-0 px-4 border-b border-gray-700">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-100" />
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex flex-col space-y-1 mt-4 px-2 flex-grow">
            <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                {{ __('Dashboard') }}
            </x-nav-link-sidebar>

            <x-nav-link-sidebar :href="route('kertas_siasatan.index')" :active="request()->routeIs('kertas_siasatan.*')">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                {{ __('Kertas Siasatan') }}
            </x-nav-link-sidebar>
            {{-- Add more general navigation links here as needed --}}
        </div>

        <!-- User Settings Dropdown (at the bottom of sidebar) -->
        <div class="mt-auto p-2 border-t border-gray-700">
            <x-dropdown align="top" width="48" contentClasses="bg-gray-700 py-1 rounded-md shadow-lg">
                <x-slot name="trigger">
                    <button class="flex items-center w-full px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none focus:bg-gray-600 transition ease-in-out duration-150">
                        {{-- Consider adding an avatar image if available --}}
                        {{-- <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="{{ Auth::user()->name }}" /> --}}
                        <div class="truncate">{{ Auth::user()->name }}</div>
                        <div class="ms-auto">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link class="text-gray-300 hover:bg-gray-600 hover:text-white" :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link class="text-gray-300 hover:bg-gray-600 hover:text-white" :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    <!-- Mobile Top Bar (only for hamburger and logo) -->
    <div class="sm:hidden flex justify-between items-center h-16 px-4 bg-gray-800 border-b border-gray-700 fixed top-0 left-0 right-0 z-40">
        <!-- Logo (Mobile) -->
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-100" />
        </a>
        <!-- Hamburger -->
        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-100 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-gray-100 transition duration-150 ease-in-out">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Navigation Menu (Overlay) -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform -translate-x-full"
         @click.away="open = false"
         class="sm:hidden fixed inset-0 z-30 flex flex-col h-full w-64 bg-gray-800 border-r border-gray-700 overflow-y-auto pt-16" {{-- pt-16 to offset mobile top bar --}}
         x-cloak>

        <!-- Mobile Nav Links -->
        <div class="flex flex-col space-y-1 mt-4 px-2 flex-grow">
            <x-responsive-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link-sidebar>

            <x-responsive-nav-link-sidebar :href="route('kertas_siasatan.index')" :active="request()->routeIs('kertas_siasatan.*')">
                 <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                {{ __('Kertas Siasatan') }}
            </x-responsive-nav-link-sidebar>
            {{-- Add more general responsive navigation links here as needed --}}
        </div>

        <!-- Responsive User Settings Options -->
        <div class="p-2 border-t border-gray-700">
            <div class="flex items-center px-3 py-2">
                {{-- Consider adding an avatar image if available --}}
                {{-- <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="{{ Auth::user()->name }}" /> --}}
                <div>
                    <div class="font-medium text-base text-gray-100">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link-sidebar :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link-sidebar>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link-sidebar :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link-sidebar>
                </form>
            </div>
        </div>
    </div>
</nav>