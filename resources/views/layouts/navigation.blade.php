@auth
<nav x-data="{ 
    open: false, 
    userMenuOpen: false, 
    notifications: false,
    searchOpen: false,
    searchQuery: '',
    searchResults: [],
    searchLoading: false,
    performSearch() {
        if (this.searchQuery.length < 2) {
            this.searchResults = [];
            return;
        }
        
        this.searchLoading = true;
        
        fetch(`/search?q=${this.searchQuery}`)
            .then(response => response.json())
            .then(data => {
                this.searchResults = data;
                this.searchLoading = false;
            })
            .catch(() => {
                this.searchLoading = false;
            });
    }
}" class="bg-gradient-to-b from-gray-900 to-gray-950 border-b border-gray-800 sticky top-0 z-50">
    
    {{-- Subtle accent line --}}
    <div class="h-px bg-gradient-to-r from-gray-700 via-gray-600 to-gray-700"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left side - Logo and Navigation --}}
            <div class="flex items-center space-x-2">
                {{-- Logo with grey theme --}}
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group py-2">
                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-200 border border-gray-700">
                        <span class="text-lg font-bold text-gray-200">⚡</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base font-bold text-gray-300 group-hover:text-white transition-colors duration-200 hidden sm:block leading-tight">
                            EMMS
                        </span>
                        <span class="text-[8px] font-medium text-gray-600 hidden sm:block tracking-wider">ELECTRICAL</span>
                    </div>
                </a>

                {{-- Vertical Divider --}}
                <div class="h-6 w-px bg-gray-800 hidden lg:block ml-1"></div>

                {{-- Main Navigation - Grey theme --}}
                <div class="hidden lg:flex lg:items-center lg:space-x-1 ml-1">
                    <x-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Assets</span>
                    </x-nav-link>

                    <x-nav-link :href="route('work-orders.index')" :active="request()->routeIs('work-orders.*')" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Work Orders</span>
                    </x-nav-link>

                    <x-nav-link :href="route('faults.index')" :active="request()->routeIs('faults.*')" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Faults</span>
                    </x-nav-link>

                    <x-nav-link :href="route('maintenance-schedules.index')" :active="request()->routeIs('maintenance-schedules.*')" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Maintenance</span>
                    </x-nav-link>

                    {{-- Admin Users Link - Only visible to admins --}}
                    @can('viewAny', App\Models\User::class)
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" 
                        class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Users</span>
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            {{-- Right side - Grey theme --}}
            <div class="flex items-center space-x-2">
                {{-- Compact Search Button --}}
                <div class="relative">
                    <button @click="searchOpen = !searchOpen" 
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    
                    {{-- Compact Search Dropdown (keep white for contrast) --}}
                    <div x-show="searchOpen" @click.away="searchOpen = false" x-cloak
                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50 overflow-hidden border-t-2 border-gray-600">
                        {{-- Search input (unchanged) --}}
                        <div class="relative border-b border-gray-200">
                            <input type="text" 
                                x-ref="searchInput"
                                x-model="searchQuery"
                                @input.debounce.300ms="performSearch"
                                placeholder="Search assets, work orders..." 
                                class="w-full px-4 py-3 pl-10 text-sm border-0 focus:ring-0"
                                autocomplete="off">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            {{-- Loading Spinner --}}
                            <div x-show="searchLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Search Results (unchanged) --}}
                        <div x-show="searchQuery.length >= 2" class="max-h-80 overflow-y-auto">
                            <template x-if="searchResults.length === 0 && !searchLoading">
                                <div class="p-6 text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-xs text-gray-500">No results found for "<span x-text="searchQuery"></span>"</p>
                                </div>
                            </template>
                            
                            <template x-for="result in searchResults" :key="result.id">
                                <a :href="result.url" class="block px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 mr-2">
                                            <span :class="{
                                                'bg-blue-100 text-blue-700': result.type === 'asset',
                                                'bg-yellow-100 text-yellow-700': result.type === 'work-order',
                                                'bg-red-100 text-red-700': result.type === 'fault',
                                                'bg-purple-100 text-purple-700': result.type === 'schedule',
                                                'bg-green-100 text-green-700': result.type === 'user'
                                            }" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium">
                                                <span x-text="result.type_label"></span>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate" x-text="result.title"></p>
                                            <p class="text-[10px] text-gray-500 truncate" x-text="result.subtitle"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                        
                        {{-- Search Tips --}}
                        <div x-show="searchQuery.length < 2" class="px-4 py-2 bg-gray-50 text-[10px] text-gray-500">
                            <p class="flex items-center">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Type at least 2 characters to search
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Notifications - Grey theme --}}
                <div x-data="{ notificationsOpen: false }" class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" 
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150 relative">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(isset($pendingVerification) && $pendingVerification > 0)
                        <span class="absolute -top-0.5 -right-0.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-600 text-[8px] font-bold text-white ring-1 ring-gray-800">
                            {{ $pendingVerification > 9 ? '9+' : $pendingVerification }}
                        </span>
                        @endif
                    </button>
                    <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-cloak 
                        class="absolute right-0 mt-2 w-72 rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 z-50 border-t-2 border-gray-600">
                        <div class="py-1">
                            <div class="px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-xs font-semibold text-gray-700">Notifications</h3>
                            </div>
                            @if(isset($pendingVerification) && $pendingVerification > 0)
                            <a href="{{ route('work-orders.index', ['status' => 'completed']) }}" class="block px-4 py-2.5 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-3 w-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-xs text-gray-900"><span class="font-semibold">{{ $pendingVerification }}</span> pending verification</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">Click to review</p>
                                    </div>
                                </div>
                            </a>
                            @endif
                            <div class="px-4 py-2.5 text-center text-xs text-gray-500">
                                <p>No new notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Menu - Grey theme --}}
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" 
                        class="flex items-center space-x-2 pl-2 pr-1.5 py-1 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors duration-150 group border border-gray-700">
                        <div class="flex items-center space-x-1.5">
                            <div class="h-7 w-7 rounded-full bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center text-gray-200 font-semibold text-xs shadow-sm ring-1 ring-gray-600">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                
                            </div>
                            <div class="hidden lg:block text-left">
                                <p class="text-xs font-medium text-gray-300 group-hover:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-gray-500 group-hover:text-gray-400 flex items-center">
                                   {{-- <span class="w-1 h-1 rounded-full bg-gray-500 mr-1 group-hover:bg-gray-400"></span>
                                  {{ Auth::user()->role->name ?? 'User' }} --}}
                                </p>
                            </div>
                        </div>
                        <svg class="h-3 w-3 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak 
                        class="absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 z-50 border-t-2 border-gray-600">
                        <div class="py-1">
                            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                                <p class="text-xs font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-gray-600 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-3 w-3 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </div>
                            </a>

                            {{-- Admin User Management Link in Dropdown --}}
                            @can('viewAny', App\Models\User::class)
                            <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <svg class="h-3 w-3 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    User Management
                                </div>
                            </a>
                            @endcan
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Log Out
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="flex items-center lg:hidden">
                <button @click="open = !open" 
                    class="inline-flex items-center justify-center p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors duration-150">
                    <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu with grey theme --}}
    <div x-show="open" x-cloak class="lg:hidden border-t border-gray-800 bg-gray-900">
        <div class="px-3 pt-2 pb-3 space-y-0.5">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Assets
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('work-orders.index')" :active="request()->routeIs('work-orders.*')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Work Orders
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('faults.index')" :active="request()->routeIs('faults.*')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Faults
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('maintenance-schedules.index')" :active="request()->routeIs('maintenance-schedules.*')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Maintenance
            </x-responsive-nav-link>
            
            {{-- Mobile Admin Link --}}
            @can('viewAny', App\Models\User::class)
            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" 
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                User Management
            </x-responsive-nav-link>
            @endcan
        </div>
    </div>
</nav>

@else
<nav class="bg-gradient-to-b from-gray-900 to-gray-950 border-b border-gray-800 sticky top-0 z-50">
    <div class="h-px bg-gradient-to-r from-gray-700 via-gray-600 to-gray-700"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center shadow-md border border-gray-700">
                    <span class="text-base font-bold text-gray-200">⚡</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-300">
                        EMMS
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    Login
                </a>
                <a href="{{ route('register') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-gray-800 hover:bg-gray-700 rounded-lg shadow-md transition-colors duration-150 border border-gray-700">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</nav>
@endauth