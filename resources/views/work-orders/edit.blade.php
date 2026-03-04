@extends('layouts.app')

@section('title', 'Edit Work Order - EMMS')

@section('content')
@php
    $checklist = is_string($workOrder->checklist) ? json_decode($workOrder->checklist, true) : $workOrder->checklist;
@endphp

<div x-data="{ 
    checklist: {{ json_encode($checklist ?? []) }}
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with breadcrumbs --}}
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('work-orders.index') }}" class="hover:text-gray-700">Work Orders</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('work-orders.show', $workOrder) }}" class="hover:text-gray-700">{{ $workOrder->work_order_number }}</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li class="text-gray-900 font-medium">Edit</li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Work Order</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $workOrder->work_order_number }}</p>
                </div>
                <div class="flex items-center space-x-3">
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
                    <a href="{{ route('work-orders.show', $workOrder) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Work Order
                    </a>
                </div>
            </div>
        </div>

        {{-- Edit Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('work-orders.update', $workOrder) }}" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Work Order Number (readonly) --}}
                        <div>
                            <label for="work_order_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Order Number
                            </label>
                            <input type="text" id="work_order_number" value="{{ $workOrder->work_order_number }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                                readonly disabled>
                        </div>

                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Order Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $workOrder->title) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Asset --}}
                        <div>
                            <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Asset <span class="text-red-500">*</span>
                            </label>
                            <select name="asset_id" id="asset_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('asset_id') border-red-500 @enderror" required>
                                <option value="">Select Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" @selected(old('asset_id', $workOrder->asset_id) == $asset->id)>
                                        {{ $asset->name }} ({{ $asset->asset_code }}) - {{ $asset->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Technician --}}
                        <div>
                            <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Assigned Technician <span class="text-red-500">*</span>
                            </label>
                            <select name="technician_id" id="technician_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('technician_id') border-red-500 @enderror" required>
                                <option value="">Select Technician</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" @selected(old('technician_id', $workOrder->technician_id) == $technician->id)>
                                        {{ $technician->name }} ({{ $technician->employee_id ?? 'No ID' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('technician_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Work Order Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Order Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror" required>
                                <option value="">Select Type</option>
                                <option value="preventive" @selected(old('type', $workOrder->type) == 'preventive')>Preventive Maintenance</option>
                                <option value="corrective" @selected(old('type', $workOrder->type) == 'corrective')>Corrective Maintenance</option>
                                <option value="emergency" @selected(old('type', $workOrder->type) == 'emergency')>Emergency</option>
                                <option value="inspection" @selected(old('type', $workOrder->type) == 'inspection')>Inspection</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $workOrder->priority ?? 'medium') == 'low') border-green-500 bg-green-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="low" class="sr-only" @checked(old('priority', $workOrder->priority ?? 'medium') == 'low')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                        <span class="text-xs font-medium">Low</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $workOrder->priority ?? 'medium') == 'medium') border-yellow-500 bg-yellow-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="medium" class="sr-only" @checked(old('priority', $workOrder->priority ?? 'medium') == 'medium')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                        <span class="text-xs font-medium">Medium</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $workOrder->priority ?? 'medium') == 'high') border-orange-500 bg-orange-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="high" class="sr-only" @checked(old('priority', $workOrder->priority ?? 'medium') == 'high')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span>
                                        <span class="text-xs font-medium">High</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $workOrder->priority ?? 'medium') == 'critical') border-red-500 bg-red-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="critical" class="sr-only" @checked(old('priority', $workOrder->priority ?? 'medium') == 'critical')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                        <span class="text-xs font-medium">Critical</span>
                                    </span>
                                </label>
                            </div>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Scheduled Date --}}
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Scheduled Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $workOrder->scheduled_date->format('Y-m-d')) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('scheduled_date') border-red-500 @enderror"
                                required>
                            @error('scheduled_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status (only for supervisors/admins) --}}
                        @can('update', $workOrder)
                        @if(Auth::user()->hasRole(['admin', 'maintenance-supervisor']))
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" @selected(old('status', $workOrder->status) == 'pending')>Pending</option>
                                <option value="in_progress" @selected(old('status', $workOrder->status) == 'in_progress')>In Progress</option>
                                <option value="completed" @selected(old('status', $workOrder->status) == 'completed')>Completed</option>
                                <option value="verified" @selected(old('status', $workOrder->status) == 'verified')>Verified</option>
                                <option value="cancelled" @selected(old('status', $workOrder->status) == 'cancelled')>Cancelled</option>
                            </select>
                        </div>
                        @endif
                        @endcan
                    </div>
                </div>

                {{-- Description Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Order Details</h2>
                    <div class="space-y-4">
                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                required>{{ old('description', $workOrder->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Checklist Section --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Maintenance Checklist</h2>
                        <button type="button" @click="checklist.push({ task: '', required: false })" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-sm rounded-lg hover:bg-blue-100 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Checklist Item
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">Update the tasks that need to be completed during this maintenance work.</p>

                    <template x-for="(item, index) in checklist" :key="index">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-1">
                                <input type="text" x-model="item.task" :name="`checklist[${index}][task]`" placeholder="Task description" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="flex items-center space-x-1">
                                    <input type="checkbox" x-model="item.required" :name="`checklist[${index}][required]`" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-600">Required</span>
                                </label>
                                <button type="button" @click="checklist.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="checklist.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No checklist items. Click "Add Checklist Item" to create tasks.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('work-orders.show', $workOrder) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection