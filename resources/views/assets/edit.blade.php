@extends('layouts.app')

@section('title', 'Edit ' . $asset->name . ' - EMMS')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with breadcrumbs and status --}}
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('assets.index') }}" class="hover:text-gray-700">Assets</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('assets.show', $asset) }}" class="hover:text-gray-700">{{ $asset->asset_code }}</a></li>
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Asset</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $asset->name }} ({{ $asset->asset_code }})</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($asset->status === 'operational') bg-green-100 text-green-800
                        @elseif($asset->status === 'maintenance') bg-yellow-100 text-yellow-800
                        @elseif($asset->status === 'faulty') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5
                            @if($asset->status === 'operational') bg-green-500
                            @elseif($asset->status === 'maintenance') bg-yellow-500
                            @elseif($asset->status === 'faulty') bg-red-500
                            @else bg-gray-500
                            @endif">
                        </span>
                        {{ ucfirst($asset->status) }}
                    </span>
                    <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Asset
                    </a>
                </div>
            </div>
        </div>

        {{-- Edit Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('assets.update', $asset) }}" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Asset Code (readonly for editing) --}}
                        <div>
                            <label for="asset_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Asset Code <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16 4-16m-6-4h6"></path>
                                    </svg>
                                </div>
                                <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50 @error('asset_code') border-red-500 @enderror"
                                    readonly>
                            </div>
                            @error('asset_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Asset code cannot be changed after creation</p>
                        </div>

                        {{-- Asset Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Asset Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                    required>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Asset Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Asset Type <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <select name="type" id="type" class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="motor" @selected(old('type', $asset->type) == 'motor')>Motor</option>
                                    <option value="transformer" @selected(old('type', $asset->type) == 'transformer')>Transformer</option>
                                    <option value="mcc" @selected(old('type', $asset->type) == 'mcc')>MCC</option>
                                    <option value="distribution_board" @selected(old('type', $asset->type) == 'distribution_board')>Distribution Board</option>
                                    <option value="vfd" @selected(old('type', $asset->type) == 'vfd')>Variable Speed Drive</option>
                                    <option value="switchgear" @selected(old('type', $asset->type) == 'switchgear')>Switchgear</option>
                                    <option value="cable" @selected(old('type', $asset->type) == 'cable')>Cable</option>
                                    <option value="other" @selected(old('type', $asset->type) == 'other')>Other</option>
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

                        {{-- Location --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="location" id="location" value="{{ old('location', $asset->location) }}" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                                    required>
                            </div>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Manufacturer --}}
                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $asset->manufacturer) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Model --}}
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" name="model" id="model" value="{{ old('model', $asset->model) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Serial Number --}}
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Installation Date --}}
                        <div>
                            <label for="installation_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Installation Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="installation_date" id="installation_date" value="{{ old('installation_date', $asset->installation_date->format('Y-m-d')) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('installation_date') border-red-500 @enderror"
                                required>
                            @error('installation_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Asset Status</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition
                                    @if(old('status', $asset->status) == 'operational') border-green-500 bg-green-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="status" value="operational" class="sr-only" 
                                        @checked(old('status', $asset->status) == 'operational')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                        <span class="text-sm font-medium">Operational</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition
                                    @if(old('status', $asset->status) == 'maintenance') border-yellow-500 bg-yellow-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="status" value="maintenance" class="sr-only"
                                        @checked(old('status', $asset->status) == 'maintenance')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                                        <span class="text-sm font-medium">Maintenance</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition
                                    @if(old('status', $asset->status) == 'faulty') border-red-500 bg-red-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="status" value="faulty" class="sr-only"
                                        @checked(old('status', $asset->status) == 'faulty')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                        <span class="text-sm font-medium">Faulty</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition
                                    @if(old('status', $asset->status) == 'decommissioned') border-gray-500 bg-gray-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="status" value="decommissioned" class="sr-only"
                                        @checked(old('status', $asset->status) == 'decommissioned')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-gray-500 mr-2"></span>
                                        <span class="text-sm font-medium">Decommissioned</span>
                                    </span>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Electrical Specifications Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Electrical Specifications</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Voltage Rating --}}
                        <div>
                            <label for="voltage_rating" class="block text-sm font-medium text-gray-700 mb-1">Voltage Rating (V)</label>
                            <input type="number" step="0.01" name="voltage_rating" id="voltage_rating" value="{{ old('voltage_rating', $asset->voltage_rating) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Current Rating --}}
                        <div>
                            <label for="current_rating" class="block text-sm font-medium text-gray-700 mb-1">Current Rating (A)</label>
                            <input type="number" step="0.01" name="current_rating" id="current_rating" value="{{ old('current_rating', $asset->current_rating) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Power Rating --}}
                        <div>
                            <label for="power_rating" class="block text-sm font-medium text-gray-700 mb-1">Power Rating (kW)</label>
                            <input type="number" step="0.01" name="power_rating" id="power_rating" value="{{ old('power_rating', $asset->power_rating) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Technical Specifications (JSON) Section --}}
                @php
                    $specs = json_decode($asset->technical_specs, true) ?? [];
                    $formattedSpecs = [];
                    foreach ($specs as $key => $value) {
                        $formattedSpecs[] = ['key' => $key, 'value' => $value];
                    }
                @endphp
                <div x-data="{ specs: {{ json_encode($formattedSpecs) }} }" class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Technical Specifications</h2>
                        <button type="button" @click="specs.push({ key: '', value: '' })" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-sm rounded-lg hover:bg-blue-100 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Specification
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">Add or update technical specifications as key-value pairs.</p>

                    <template x-for="(spec, index) in specs" :key="index">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-1">
                                <input type="text" x-model="spec.key" :name="`technical_specs[${index}][key]`" placeholder="Specification name (e.g., IP Rating)" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex-1">
                                <input type="text" x-model="spec.value" :name="`technical_specs[${index}][value]`" placeholder="Value (e.g., IP54)" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="button" @click="specs.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="specs.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No specifications added yet. Click "Add Specification" to add custom specs.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('assets.show', $asset) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection