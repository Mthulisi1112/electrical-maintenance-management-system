@extends('layouts.app')

@section('title', 'Work Orders - EMMS')

@section('content')
<div x-data="{ 
    showFilters: false,
    deleteModal: false,
    workOrderToDelete: null,
    confirmDelete(workOrderId) {
        this.workOrderToDelete = workOrderId;
        this.deleteModal = true;
    }
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with stats summary --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Work Orders</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage and track all maintenance work orders</p>
                </div>
                
                {{-- Create Work Order Button - Visible to Admin and Maintenance Supervisor --}}
                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('maintenance-supervisor'))
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('work-orders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Work Order
                    </a>
                </div>
                @endif
            </div>

            {{-- Quick stats cards --}}
            @php
                $total = $workOrders->total();
                $pending = $workOrders->where('status', 'pending')->count();
                $inProgress = $workOrders->where('status', 'in_progress')->count();
                $completed = $workOrders->where('status', 'completed')->count();
                $verified = $workOrders->where('status', 'verified')->count();
            @endphp
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $pending }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">In Progress</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $inProgress }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $completed }}</p>
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
                    <h3 class="text-sm font-semibold text-gray-900">Filter Work Orders</h3>
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
                <form method="GET" action="{{ route('work-orders.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                            <option value="in_progress" @selected(request('status') == 'in_progress')>In Progress</option>
                            <option value="completed" @selected(request('status') == 'completed')>Completed</option>
                            <option value="verified" @selected(request('status') == 'verified')>Verified</option>
                            <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
                        </select>
                    </div>

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
                        <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">Technician</label>
                        <select name="technician_id" id="technician_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Technicians</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" @selected(request('technician_id') == $technician->id)>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-end space-x-3">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('work-orders.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition text-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Work Orders Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Table header with result count --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Showing {{ $workOrders->firstItem() ?? 0 }} - {{ $workOrders->lastItem() ?? 0 }} of {{ $workOrders->total() }} work orders</span>
                </div>
                @if(request()->hasAny(['status', 'asset_id', 'technician_id', 'date_from', 'date_to']))
                <span class="text-xs text-gray-500">Filtered results</span>
                @endif
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($workOrders as $workOrder)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">WO</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $workOrder->work_order_number }}</span>
                                        <p class="text-xs text-gray-500">{{ Str::limit($workOrder->title, 30) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $workOrder->asset->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $workOrder->asset->asset_code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($workOrder->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $workOrder->technician->name ?? 'Unassigned' }}</div>
                                <div class="text-xs text-gray-500">{{ $workOrder->technician->employee_id ?? 'No ID' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($workOrder->scheduled_date)->format('M d, Y') }}</div>
                                @if(\Carbon\Carbon::parse($workOrder->scheduled_date)->isToday())
                                    <span class="text-xs text-green-600">Today</span>
                                @elseif(\Carbon\Carbon::parse($workOrder->scheduled_date)->isPast() && !in_array($workOrder->status, ['completed', 'verified']))
                                    <span class="text-xs text-red-600">Overdue</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($workOrder->status === 'completed') bg-green-100 text-green-800
                                    @elseif($workOrder->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($workOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($workOrder->status === 'verified') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($workOrder->status === 'completed') bg-green-500
                                        @elseif($workOrder->status === 'in_progress') bg-blue-500
                                        @elseif($workOrder->status === 'pending') bg-yellow-500
                                        @elseif($workOrder->status === 'verified') bg-purple-500
                                        @else bg-gray-500
                                        @endif">
                                    </span>
                                    {{ str_replace('_', ' ', ucfirst($workOrder->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- View button - visible to all authenticated users --}}
                                    <a href="{{ route('work-orders.show', $workOrder) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="View details">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    {{-- Edit button - visible to Admin, Supervisor, and assigned Technician (based on status) --}}
                                    @if(Auth::user()->hasRole('admin') || 
                                         (Auth::user()->hasRole('maintenance-supervisor') && in_array($workOrder->status, ['pending', 'completed'])) ||
                                         (Auth::user()->hasRole('technician') && $workOrder->technician_id === Auth::id() && in_array($workOrder->status, ['pending', 'in_progress'])))
                                    <a href="{{ route('work-orders.edit', $workOrder) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    
                                    {{-- Delete button - visible to Admin only --}}
                                    @if(Auth::user()->hasRole('admin'))
                                    <button @click="confirmDelete({{ $workOrder->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No work orders found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new work order.</p>
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('maintenance-supervisor'))
                                <div class="mt-6">
                                    <a href="{{ route('work-orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create Work Order
                                    </a>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <x-pagination :paginator="$workOrders" /> 
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
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Delete Work Order</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this work order? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form method="POST" :action="`/work-orders/${workOrderToDelete}`" class="inline">
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