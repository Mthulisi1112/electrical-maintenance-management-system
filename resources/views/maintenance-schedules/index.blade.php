@extends('layouts.app')

@section('title', 'Maintenance Schedules - EMMS')

@section('content')
<div x-data="{ 
    showFilters: false,
    deleteModal: false,
    scheduleToDelete: null,
    confirmDelete(scheduleId) {
        this.scheduleToDelete = scheduleId;
        this.deleteModal = true;
    }
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with stats summary --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Maintenance Schedules</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage preventive maintenance schedules for all assets</p>
                </div>
                
                {{-- Create Schedule Button - Using Policy --}}
                @can('create', App\Models\MaintenanceSchedule::class)
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('maintenance-schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Schedule
                    </a>
                </div>
                @endcan
            </div>

            {{-- Quick stats cards --}}
            @php
                $total = $schedules->total();
                $active = $schedules->where('is_active', true)->count();
                $inactive = $schedules->where('is_active', false)->count();
                $overdue = $schedules->where('is_active', true)->filter(function($s) {
                    return $s->next_due_date < now();
                })->count();
                $critical = $schedules->where('priority', 'critical')->count();
            @endphp
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Total Schedules</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Active</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $active }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Overdue</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $overdue }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Critical Priority</p>
                    <p class="mt-1 text-2xl font-semibold text-orange-600">{{ $critical }}</p>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Filter Schedules</h3>
                </div>
                <button @click="showFilters = !showFilters" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <span class="mr-2" x-text="showFilters ? 'Hide filters' : 'Show filters'"></span>
                    <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            {{-- Filter form --}}
            <div x-show="showFilters" x-cloak class="px-6 pb-6">
                <form method="GET" action="{{ route('maintenance-schedules.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">Asset</label>
                        <select name="asset_id" id="asset_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Assets</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" @selected(request('asset_id') == $asset->id)>{{ $asset->name }} ({{ $asset->asset_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                        <select name="frequency" id="frequency" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Frequencies</option>
                            <option value="daily" @selected(request('frequency') == 'daily')>Daily</option>
                            <option value="weekly" @selected(request('frequency') == 'weekly')>Weekly</option>
                            <option value="monthly" @selected(request('frequency') == 'monthly')>Monthly</option>
                            <option value="quarterly" @selected(request('frequency') == 'quarterly')>Quarterly</option>
                            <option value="semi_annual" @selected(request('frequency') == 'semi_annual')>Semi-Annual</option>
                            <option value="annual" @selected(request('frequency') == 'annual')>Annual</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" id="priority" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Priorities</option>
                            <option value="low" @selected(request('priority') == 'low')>Low</option>
                            <option value="medium" @selected(request('priority') == 'medium')>Medium</option>
                            <option value="high" @selected(request('priority') == 'high')>High</option>
                            <option value="critical" @selected(request('priority') == 'critical')>Critical</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" id="is_active" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="yes" @selected(request('is_active') == 'yes')>Active</option>
                            <option value="no" @selected(request('is_active') == 'no')>Inactive</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('maintenance-schedules.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Schedules Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Table header with result count --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Showing {{ $schedules->firstItem() ?? 0 }} - {{ $schedules->lastItem() ?? 0 }} of {{ $schedules->total() }} schedules</span>
                </div>
                @if(request()->hasAny(['asset_id', 'frequency', 'priority', 'is_active']))
                <span class="text-xs text-gray-500">Filtered results</span>
                @endif
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Due</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">SCH</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $schedule->title }}</span>
                                        <p class="text-xs text-gray-500">ID: {{ $schedule->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $schedule->asset->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $schedule->asset->asset_code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $schedule->frequency)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->next_due_date)->format('M d, Y') }}</div>
                                @if($schedule->is_active && $schedule->next_due_date < now())
                                    <span class="text-xs text-red-600">Overdue</span>
                                @elseif($schedule->next_due_date->isToday())
                                    <span class="text-xs text-green-600">Today</span>
                                @elseif($schedule->next_due_date->isTomorrow())
                                    <span class="text-xs text-yellow-600">Tomorrow</span>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- View button - visible to all --}}
                                    <a href="{{ route('maintenance-schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="View details">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Edit button - Admin or Supervisor --}}
                                    @can('update', $schedule)
                                    <a href="{{ route('maintenance-schedules.edit', $schedule) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    
                                    {{-- Toggle Active/Inactive - Admin or Supervisor --}}
                                    @can('update', $schedule)
                                    <form method="POST" action="{{ route('maintenance-schedules.toggle-active', $schedule) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-{{ $schedule->is_active ? 'red' : 'green' }}-600 hover:text-{{ $schedule->is_active ? 'red' : 'green' }}-900 bg-{{ $schedule->is_active ? 'red' : 'green' }}-50 hover:bg-{{ $schedule->is_active ? 'red' : 'green' }}-100 p-2 rounded-lg transition" title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($schedule->is_active)
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    @endcan
                                    
                                    {{-- Delete button - Admin only --}}
                                    @can('delete', $schedule)
                                    <button @click="confirmDelete({{ $schedule->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No maintenance schedules found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new schedule.</p>
                                @can('create', App\Models\MaintenanceSchedule::class)
                                <div class="mt-6">
                                    <a href="{{ route('maintenance-schedules.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create Schedule
                                    </a>
                                </div>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$schedules" />
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="deleteModal = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Delete Maintenance Schedule</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this maintenance schedule? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form method="POST" :action="`/maintenance-schedules/${scheduleToDelete}`" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Delete
                        </button>
                    </form>
                    <button type="button" @click="deleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection