<nav x-data="{ open: false, notifOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Products') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        {{ __('Transactions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('adjustments.create')" :active="request()->routeIs('adjustments.*')">
                        {{ __('Adjust Stock') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side: Notification Bell & User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                @php
                    $unreadNotifications = \Illuminate\Support\Facades\Schema::hasTable('notifications')
                        ? Auth::user()->unreadNotifications
                        : collect();
                @endphp

                <!-- Notification Bell Dropdown -->
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if($unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="notifOpen" @click.away="notifOpen = false" style="display: none;" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50 border border-gray-200">
                        <div class="py-2 px-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Low Stock Alerts</span>
                            @if($unreadNotifications->count() > 0 && Route::has('notifications.markAllRead'))
                                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-emerald-600 hover:underline">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($unreadNotifications as $notification)
                                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 text-sm">
                                    <p class="font-semibold text-gray-800">{{ $notification->data['product_name'] ?? 'Product Alert' }} ({{ $notification->data['sku'] ?? '' }})</p>
                                    <p class="text-gray-600 text-xs mt-1">{{ $notification->data['message'] ?? 'Stock is low.' }}</p>
                                    <div class="mt-2 flex justify-between items-center text-xs">
                                        @if(isset($notification->data['product_id']))
                                            <a href="{{ route('products.show', $notification->data['product_id']) }}" class="text-emerald-600 font-medium hover:underline">View Product</a>
                                        @endif
                                        <span class="text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-500">
                                    No unread low stock alerts.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>