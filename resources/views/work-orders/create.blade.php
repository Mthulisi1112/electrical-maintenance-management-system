@extends('layouts.app')

@section('title', 'Create Work Order - EMMS')

@section('content')
<div x-data="{ 
    checklist: [],
    priority: 'medium'
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
                    <li class="text-gray-900 font-medium">Create New Work Order</li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Create New Work Order</h1>
                <a href="{{ route('work-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Work Orders
                </a>
            </div>
            <p class="mt-2 text-sm text-gray-600">Create a new maintenance work order. All fields marked with <span class="text-red-500">*</span> are required.</p>
        </div>

        {{-- Create Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('work-orders.store') }}" class="divide-y divide-gray-200">
                @csrf

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Order Title <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </div>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                    placeholder="e.g., Monthly Motor Inspection" required>
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Asset --}}
                        <div>
                            <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Asset <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <select name="asset_id" id="asset_id" class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('asset_id') border-red-500 @enderror" required>
                                    <option value="">Select Asset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                            {{ $asset->name }} ({{ $asset->asset_code }}) - {{ $asset->location }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('asset_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Technician --}}
                        <div>
                            <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Assigned Technician <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <select name="technician_id" id="technician_id" class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('technician_id') border-red-500 @enderror" required>
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" @selected(old('technician_id') == $technician->id)>
                                            {{ $technician->name }} ({{ $technician->employee_id ?? 'No ID' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('technician_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Work Order Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Work Order Type <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <select name="type" id="type" class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="preventive" @selected(old('type') == 'preventive')>Preventive Maintenance</option>
                                    <option value="corrective" @selected(old('type') == 'corrective')>Corrective Maintenance</option>
                                    <option value="emergency" @selected(old('type') == 'emergency')>Emergency</option>
                                    <option value="inspection" @selected(old('type') == 'inspection')>Inspection</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition"
                                       :class="{ 'border-green-500 bg-green-50': priority === 'low', 'border-gray-200 hover:bg-gray-50': priority !== 'low' }">
                                    <input type="radio" name="priority" value="low" class="sr-only" x-model="priority">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                        <span class="text-xs font-medium">Low</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition"
                                       :class="{ 'border-yellow-500 bg-yellow-50': priority === 'medium', 'border-gray-200 hover:bg-gray-50': priority !== 'medium' }">
                                    <input type="radio" name="priority" value="medium" class="sr-only" x-model="priority">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                        <span class="text-xs font-medium">Medium</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition"
                                       :class="{ 'border-orange-500 bg-orange-50': priority === 'high', 'border-gray-200 hover:bg-gray-50': priority !== 'high' }">
                                    <input type="radio" name="priority" value="high" class="sr-only" x-model="priority">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span>
                                        <span class="text-xs font-medium">High</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition"
                                       :class="{ 'border-red-500 bg-red-50': priority === 'critical', 'border-gray-200 hover:bg-gray-50': priority !== 'critical' }">
                                    <input type="radio" name="priority" value="critical" class="sr-only" x-model="priority">
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
                            <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', now()->format('Y-m-d')) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('scheduled_date') border-red-500 @enderror"
                                required>
                            @error('scheduled_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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
                                placeholder="Provide detailed description of the work to be performed..." required>{{ old('description') }}</textarea>
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
                    
                    <p class="text-sm text-gray-500 mb-4">Add tasks that need to be completed during this maintenance work.</p>

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
                        <p class="mt-2 text-sm text-gray-500">No checklist items added. Click "Add Checklist Item" to create tasks.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('work-orders.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Create Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection