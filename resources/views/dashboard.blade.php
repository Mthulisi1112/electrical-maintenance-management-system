@extends('layouts.app')

@section('title', 'Dashboard - EMMS')

@section('content')
<div x-data="{ activeTab: 'work-orders' }" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Welcome Section --}}
        <div class="mb-8 relative overflow-hidden rounded-2xl">
            {{-- Background image with grey overlay --}}
            <div class="absolute inset-0">
                <img src="{{ asset('images/industrial/electrical-panel.jpg') }}" 
                     class="w-full h-full object-cover"
                     alt="Industrial background">
                {{-- Grey gradient overlay --}}
                <div class="absolute inset-0 bg-gradient-to-r from-gray-900/70 to-gray-800/50"></div>
            </div>
            
            {{-- Content --}}
            <div class="relative px-8 py-8 sm:px-10 sm:py-10">
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1">
                            {{-- Greeting with time-based icon --}}
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-white/20 backdrop-blur border border-white/30">
                                    @php
                                        $hour = now()->format('H');
                                        if ($hour < 12) {
                                            $icon = 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
                                            $greeting = 'Good Morning';
                                        } elseif ($hour < 17) {
                                            $icon = 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
                                            $greeting = 'Good Afternoon';
                                        } else {
                                            $icon = 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z';
                                            $greeting = 'Good Evening';
                                        }
                                    @endphp
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-white/90 drop-shadow-lg">{{ $greeting }},</span>
                                    <h1 class="text-2xl font-bold text-white drop-shadow-lg">{{ Auth::user()->name }}</h1>
                                </div>
                            </div>
                            
                            {{-- Date and role badge --}}
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center space-x-2 bg-black/40 backdrop-blur rounded-xl px-4 py-2 border border-white/20">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm text-white">{{ now()->format('l, F j, Y') }}</span>
                                </div>
                                
                                <div class="flex items-center space-x-2 bg-black/40 backdrop-blur rounded-xl px-4 py-2 border border-white/20">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-sm text-white">{{ Auth::user()->role->name ?? 'User' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Quick stats for today --}}
                        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
                            @if(isset($stats['active_work_orders']) && $stats['active_work_orders'] > 0)
                            <div class="bg-black/40 backdrop-blur rounded-xl px-5 py-3 border border-white/20">
                                <p class="text-xs text-white/80">Active Tasks</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['active_work_orders'] }}</p>
                            </div>
                            @endif
                            
                            @if(isset($stats['critical_faults']) && $stats['critical_faults'] > 0)
                            <div class="bg-black/40 backdrop-blur rounded-xl px-5 py-3 border border-white/20">
                                <p class="text-xs text-white/80">Critical</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['critical_faults'] }}</p>
                            </div>
                            @endif
                            
                            <div class="hidden lg:block bg-black/40 backdrop-blur rounded-xl px-5 py-3 border border-white/20">
                                <p class="text-xs text-white/80">Productivity</p>
                                <p class="text-sm font-medium text-white">⚡ Let's make today great!</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Motivational message based on role --}}
                    <div class="mt-4 text-sm text-white/90 border-t border-white/20 pt-4">
                        @if(Auth::user()->hasRole('admin'))
                            <span class="font-semibold">⚡ Full system access.</span> You have complete control over all operations.
                        @elseif(Auth::user()->hasRole('maintenance-supervisor'))
                            <span class="font-semibold">🔧 Managing operations.</span> {{ $stats['pendingVerification'] ?? 0 }} work orders pending verification.
                        @elseif(Auth::user()->hasRole('technician'))
                            <span class="font-semibold">🛠️ Your tasks await.</span> You have {{ $stats['active_work_orders'] ?? 0 }} active assignments.
                        @elseif(Auth::user()->hasRole('auditor'))
                            <span class="font-semibold">📊 Review mode.</span> You have read-only access to system data.
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Subtle decorative elements (single set) --}}
            <div class="absolute bottom-0 right-0 w-40 h-40 opacity-10 pointer-events-none">
                <svg viewBox="0 0 200 200" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M56.6,-69.8C71.2,-60.4,81.1,-42.9,85.6,-24.5C90.1,-6.1,89.3,13.3,80.8,28.9C72.3,44.5,56.1,56.3,38.7,64.3C21.3,72.3,2.7,76.5,-15.2,74.3C-33.1,72.1,-50.3,63.5,-63.6,50.3C-76.9,37.1,-86.3,19.2,-85.9,1.4C-85.5,-16.4,-75.3,-33.9,-61.7,-45.6C-48.1,-57.3,-31.1,-63.2,-13.2,-67.1C4.7,-71,23.7,-73,39.5,-68.9C55.3,-64.7,67.9,-54.4,56.6,-69.8Z" transform="translate(100 100)" />
                </svg>
            </div>
            <div class="absolute top-0 left-0 w-32 h-32 opacity-10 pointer-events-none transform -rotate-45">
                <svg viewBox="0 0 200 200" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M45.3,-56.2C58.1,-46.9,67.8,-33.2,72.9,-17.6C78,-2,78.5,15.6,71.8,30.4C65.1,45.2,51.3,57.3,35.7,64.3C20.1,71.3,2.7,73.2,-14.1,70.2C-30.9,67.2,-47.1,59.3,-58.7,46.7C-70.3,34.1,-77.3,16.9,-76.9,0.2C-76.5,-16.5,-68.7,-32.5,-56.4,-43.7C-44.1,-54.9,-27.3,-61.3,-9.8,-63.1C7.8,-64.9,27.5,-62.1,45.3,-56.2Z" transform="translate(100 100)" />
                </svg>
            </div>
        </div>  

        {{-- Stats Cards --}}
        @if(!empty($stats))
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            {{-- Total Assets Card --}}
            @if(isset($stats['total_assets']))
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Assets</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_assets'] }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-green-600">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        <span>+{{ rand(2, 8) }}% from last month</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Active Work Orders Card --}}
            @if(isset($stats['active_work_orders']))
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                                @if(Auth::user()->hasRole('technician')) Active @else Active @endif Work Orders
                            </p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_work_orders'] }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-yellow-50 group-hover:bg-yellow-100 transition-colors flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-yellow-600">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ rand(1, 5) }} due today</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Critical Faults Card --}}
            @if(isset($stats['critical_faults']))
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Critical Faults</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['critical_faults'] }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-red-600">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Requires immediate attention</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Overdue Maintenance Card --}}
            @if(isset($stats['overdue_maintenance']))
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Overdue Maintenance</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['overdue_maintenance'] }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-purple-600">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Schedule now</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Charts Section --}}
        @if((isset($assetsByStatus) && count($assetsByStatus) > 0) || (isset($workOrdersByStatus) && count($workOrdersByStatus) > 0))
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
            @if(isset($assetsByStatus) && count($assetsByStatus) > 0)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Assets by Status</h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Live</span>
                </div>
                <div class="space-y-4">
                    @foreach($assetsByStatus as $status)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $status->status }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $status->total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $total = array_sum($assetsByStatus->pluck('total')->toArray());
                                $percentage = $total > 0 ? ($status->total / $total) * 100 : 0;
                            @endphp
                            <div class="h-2.5 rounded-full transition-all duration-500 
                                @if($status->status === 'operational') bg-blue-600
                                @elseif($status->status === 'maintenance') bg-yellow-500
                                @elseif($status->status === 'faulty') bg-red-500
                                @else bg-gray-500
                                @endif" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($workOrdersByStatus) && count($workOrdersByStatus) > 0)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Work Orders by Status</h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Live</span>
                </div>
                <div class="space-y-4">
                    @foreach($workOrdersByStatus as $status)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $status->status) }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $status->total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $total = array_sum($workOrdersByStatus->pluck('total')->toArray());
                                $percentage = $total > 0 ? ($status->total / $total) * 100 : 0;
                            @endphp
                            <div class="h-2.5 rounded-full transition-all duration-500 
                                @if($status->status === 'completed') bg-green-500
                                @elseif($status->status === 'in_progress') bg-blue-500
                                @elseif($status->status === 'pending') bg-yellow-500
                                @elseif($status->status === 'verified') bg-purple-500
                                @else bg-gray-500
                                @endif" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Pending Verification Card --}}
        @if(isset($pendingVerification) && $pendingVerification > 0)
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="px-6 py-5 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Pending Verification</h3>
                            <p class="text-sm text-white/90">You have <span class="font-bold">{{ $pendingVerification }}</span> completed work orders awaiting your verification.</p>
                        </div>
                    </div>
                    <a href="{{ route('work-orders.index', ['status' => 'completed']) }}" class="inline-flex items-center px-5 py-2 bg-white text-yellow-600 rounded-lg font-medium text-sm hover:bg-gray-50 transition shadow-md">
                        Review Now
                        <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Recent Activity Tabs --}}
        <div class="mb-8">
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'work-orders'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'work-orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'work-orders' }" class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Recent Work Orders
                    </button>
                    <button @click="activeTab = 'faults'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'faults', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'faults' }" class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Recent Faults
                    </button>
                </nav>
            </div>

            {{-- Work Orders Tab --}}
            <div x-show="activeTab === 'work-orders'" x-cloak class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if(Auth::user()->hasRole('technician')) My Assigned Work Orders @else Recent Work Orders @endif
                    </h3>
                    @can('viewAny', App\Models\WorkOrder::class)
                    <a href="{{ route('work-orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center">
                        View all
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @endcan
                </div>
                <div class="divide-y divide-gray-100">
                    @if(isset($recentWorkOrders) && count($recentWorkOrders) > 0)
                        @foreach($recentWorkOrders as $workOrder)
                        <a href="{{ route('work-orders.show', $workOrder) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-600">WO</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $workOrder->work_order_number }}</span>
                                        <p class="text-xs text-gray-500">{{ Str::limit($workOrder->title, 40) }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($workOrder->scheduled_date)->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $workOrder->asset->name ?? 'N/A' }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $workOrder->technician->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                    @if($workOrder->status === 'completed') bg-green-100 text-green-800
                                    @elseif($workOrder->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($workOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($workOrder->status === 'verified') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($workOrder->status)) }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500">No recent work orders</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Faults Tab --}}
            <div x-show="activeTab === 'faults'" x-cloak class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if(Auth::user()->hasRole('technician')) My Reported Faults @else Recent Faults @endif
                    </h3>
                    @can('viewAny', App\Models\Fault::class)
                    <a href="{{ route('faults.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center">
                        View all
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @endcan
                </div>
                <div class="divide-y divide-gray-100">
                    @if(isset($recentFaults) && count($recentFaults) > 0)
                        @foreach($recentFaults as $fault)
                        <a href="{{ route('faults.show', $fault) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-600">FLT</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $fault->fault_number }}</span>
                                        <p class="text-xs text-gray-500">{{ Str::limit($fault->description, 40) }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($fault->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $fault->asset->name ?? 'N/A' }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $fault->reportedBy->name ?? 'Unknown' }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                        @if($fault->severity === 'critical') bg-red-100 text-red-800
                                        @elseif($fault->severity === 'high') bg-orange-100 text-orange-800
                                        @elseif($fault->severity === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($fault->severity) }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                        @if($fault->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($fault->status === 'investigating') bg-blue-100 text-blue-800
                                        @elseif($fault->status === 'reported') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($fault->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-gray-500">No recent faults</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Upcoming Maintenance --}}
        @if(isset($upcomingMaintenance) && count($upcomingMaintenance) > 0)
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Upcoming Maintenance (Next 7 Days)</h3>
                <a href="{{ route('maintenance-schedules.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center">
                    View all schedules
                    <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($upcomingMaintenance as $schedule)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">{{ substr($schedule->asset->asset_code ?? 'AST', 0, 3) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $schedule->asset->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $schedule->asset->asset_code ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $schedule->title }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($schedule->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->next_due_date)->format('M d, Y') }}</div>
                                @if(\Carbon\Carbon::parse($schedule->next_due_date)->isToday())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Today</span>
                                @elseif(\Carbon\Carbon::parse($schedule->next_due_date)->isTomorrow())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Tomorrow</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($schedule->priority === 'critical') bg-red-100 text-red-800
                                    @elseif($schedule->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($schedule->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($schedule->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('maintenance-schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-800 font-medium">View →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @can('create', App\Models\WorkOrder::class)
            <a href="{{ route('work-orders.create') }}" class="relative block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Create Work Order</span>
            </a>
            @endcan

            @can('create', App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="relative block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Register Asset</span>
            </a>
            @endcan

            @can('create', App\Models\Fault::class)
            <a href="{{ route('faults.create') }}" class="relative block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Report Fault</span>
            </a>
            @endcan

            @can('create', App\Models\MaintenanceSchedule::class)
            <a href="{{ route('maintenance-schedules.create') }}" class="relative block rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Schedule Maintenance</span>
            </a>
            @endcan
        </div>
    </div>
</div>
@endsection