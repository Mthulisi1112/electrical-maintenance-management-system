@extends('layouts.app')

@section('title', 'Faults - EMMS')

@section('content')
<div x-data="{ 
    showFilters: false,
    deleteModal: false,
    faultToDelete: null,
    confirmDelete(faultId) {
        this.faultToDelete = faultId;
        this.deleteModal = true;
    }
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with stats summary --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Fault Reports</h1>
                    <p class="mt-2 text-sm text-gray-600">Track and manage electrical faults and issues</p>
                </div>
                
                {{-- Create Fault Button - Using Policy --}}
                @can('create', App\Models\Fault::class)
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('faults.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Report Fault
                        </a>
                    </div>
                @else
                    {{-- Debug info for when policy fails --}}
                    <div class="bg-red-100 p-2 mb-2 text-xs rounded">
                        Debug: Cannot create fault. User role = {{ Auth::user()->role->name ?? 'No role' }}
                    </div>
                @endcan
            </div>

            {{-- Quick stats cards --}}
            @php
                $total = $faults->total();
                $critical = $faults->where('severity', 'critical')->count();
                $high = $faults->where('severity', 'high')->count();
                $open = $faults->whereNotIn('status', ['resolved', 'closed'])->count();
                $resolved = $faults->whereIn('status', ['resolved', 'closed'])->count();
            @endphp
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Total Faults</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Critical</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $critical }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Open Issues</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $open }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Resolved</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $resolved }}</p>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Filter Faults</h3>
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
                <form method="GET" action="{{ route('faults.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            <option value="">All Status</option>
                            <option value="reported" @selected(request('status') == 'reported')>Reported</option>
                            <option value="investigating" @selected(request('status') == 'investigating')>Investigating</option>
                            <option value="in_progress" @selected(request('status') == 'in_progress')>In Progress</option>
                            <option value="resolved" @selected(request('status') == 'resolved')>Resolved</option>
                            <option value="closed" @selected(request('status') == 'closed')>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                        <select name="severity" id="severity" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            <option value="">All Severities</option>
                            <option value="low" @selected(request('severity') == 'low')>Low</option>
                            <option value="medium" @selected(request('severity') == 'medium')>Medium</option>
                            <option value="high" @selected(request('severity') == 'high')>High</option>
                            <option value="critical" @selected(request('severity') == 'critical')>Critical</option>
                        </select>
                    </div>

                    <div>
                        <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">Asset</label>
                        <select name="asset_id" id="asset_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            <option value="">All Assets</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" @selected(request('asset_id') == $asset->id)>{{ $asset->name }} ({{ $asset->asset_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                        <select name="assigned_to" id="assigned_to" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            <option value="">All Technicians</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" @selected(request('assigned_to') == $technician->id)>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('faults.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Faults Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Table header with result count --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Showing {{ $faults->firstItem() ?? 0 }} - {{ $faults->lastItem() ?? 0 }} of {{ $faults->total() }} faults</span>
                </div>
                @if(request()->hasAny(['status', 'severity', 'asset_id', 'assigned_to']))
                <span class="text-xs text-gray-500">Filtered results</span>
                @endif
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fault #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($faults as $fault)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">FLT</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $fault->fault_number }}</span>
                                        <p class="text-xs text-gray-500">{{ Str::limit($fault->description, 30) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $fault->asset->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $fault->asset->asset_code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $fault->fault_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($fault->severity === 'critical') bg-red-100 text-red-800
                                    @elseif($fault->severity === 'high') bg-orange-100 text-orange-800
                                    @elseif($fault->severity === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($fault->severity === 'critical') bg-red-500
                                        @elseif($fault->severity === 'high') bg-orange-500
                                        @elseif($fault->severity === 'medium') bg-yellow-500
                                        @else bg-green-500
                                        @endif">
                                    </span>
                                    {{ ucfirst($fault->severity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($fault->status === 'resolved') bg-green-100 text-green-800
                                    @elseif($fault->status === 'closed') bg-gray-100 text-gray-800
                                    @elseif($fault->status === 'investigating') bg-blue-100 text-blue-800
                                    @elseif($fault->status === 'in_progress') bg-purple-100 text-purple-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $fault->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $fault->reportedBy->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($fault->assignedTo)
                                    <span class="inline-flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        {{ $fault->assignedTo->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- View button - using policy --}}
                                    @can('view', $fault)
                                    <a href="{{ route('faults.show', $fault) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="View details">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    
                                    {{-- Edit button - using policy --}}
                                    @can('update', $fault)
                                    <a href="{{ route('faults.edit', $fault) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    
                                    {{-- Delete button - using policy --}}
                                    @can('delete', $fault)
                                    <button @click="confirmDelete({{ $fault->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Delete">
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
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No faults found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by reporting a new fault.</p>
                                @can('create', App\Models\Fault::class)
                                <div class="mt-6">
                                    <a href="{{ route('faults.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Report Fault
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
             <x-pagination :paginator="$faults" />
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
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Delete Fault Report</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this fault report? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form method="POST" :action="`/faults/${faultToDelete}`" class="inline">
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