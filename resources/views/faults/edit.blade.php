@extends('layouts.app')

@section('title', 'Edit Fault - EMMS')

@section('content')
@php
    $symptoms = is_string($fault->symptoms) ? json_decode($fault->symptoms, true) : $fault->symptoms;
@endphp

<div x-data="{ 
    symptoms: {{ json_encode($symptoms ?? []) }}
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
                    <li><a href="{{ route('faults.index') }}" class="hover:text-gray-700">Faults</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('faults.show', $fault) }}" class="hover:text-gray-700">{{ $fault->fault_number }}</a></li>
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Fault Report</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $fault->fault_number }}</p>
                </div>
                <div class="flex items-center space-x-3">
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
                    <a href="{{ route('faults.show', $fault) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Fault
                    </a>
                </div>
            </div>
        </div>

        {{-- Edit Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('faults.update', $fault) }}" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Fault Number (readonly) --}}
                        <div>
                            <label for="fault_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Fault Number
                            </label>
                            <input type="text" id="fault_number" value="{{ $fault->fault_number }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                                readonly disabled>
                        </div>

                        {{-- Asset --}}
                        <div>
                            <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Affected Asset <span class="text-red-500">*</span>
                            </label>
                            <select name="asset_id" id="asset_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('asset_id') border-red-500 @enderror" required>
                                <option value="">Select Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" @selected(old('asset_id', $fault->asset_id) == $asset->id)>
                                        {{ $asset->name }} ({{ $asset->asset_code }}) - {{ $asset->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fault Type --}}
                        <div>
                            <label for="fault_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Fault Type <span class="text-red-500">*</span>
                            </label>
                            <select name="fault_type" id="fault_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('fault_type') border-red-500 @enderror" required>
                                <option value="">Select Fault Type</option>
                                <option value="trip" @selected(old('fault_type', $fault->fault_type) == 'trip')>Trip</option>
                                <option value="overload" @selected(old('fault_type', $fault->fault_type) == 'overload')>Overload</option>
                                <option value="short_circuit" @selected(old('fault_type', $fault->fault_type) == 'short_circuit')>Short Circuit</option>
                                <option value="earth_fault" @selected(old('fault_type', $fault->fault_type) == 'earth_fault')>Earth Fault</option>
                                <option value="overheating" @selected(old('fault_type', $fault->fault_type) == 'overheating')>Overheating</option>
                                <option value="mechanical" @selected(old('fault_type', $fault->fault_type) == 'mechanical')>Mechanical</option>
                                <option value="other" @selected(old('fault_type', $fault->fault_type) == 'other')>Other</option>
                            </select>
                            @error('fault_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Severity --}}
                        <div>
                            <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">
                                Severity <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('severity', $fault->severity) == 'low') border-green-500 bg-green-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="severity" value="low" class="sr-only" @checked(old('severity', $fault->severity) == 'low')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                        <span class="text-xs font-medium">Low</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('severity', $fault->severity) == 'medium') border-yellow-500 bg-yellow-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="severity" value="medium" class="sr-only" @checked(old('severity', $fault->severity) == 'medium')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                        <span class="text-xs font-medium">Medium</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('severity', $fault->severity) == 'high') border-orange-500 bg-orange-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="severity" value="high" class="sr-only" @checked(old('severity', $fault->severity) == 'high')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span>
                                        <span class="text-xs font-medium">High</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('severity', $fault->severity) == 'critical') border-red-500 bg-red-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="severity" value="critical" class="sr-only" @checked(old('severity', $fault->severity) == 'critical')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                        <span class="text-xs font-medium">Critical</span>
                                    </span>
                                </label>
                            </div>
                            @error('severity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status (for admins/supervisors) --}}
                        @can('update', $fault)
                        @if(Auth::user()->hasRole(['admin', 'maintenance-supervisor']))
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="reported" @selected(old('status', $fault->status) == 'reported')>Reported</option>
                                <option value="investigating" @selected(old('status', $fault->status) == 'investigating')>Investigating</option>
                                <option value="in_progress" @selected(old('status', $fault->status) == 'in_progress')>In Progress</option>
                                <option value="resolved" @selected(old('status', $fault->status) == 'resolved')>Resolved</option>
                                <option value="closed" @selected(old('status', $fault->status) == 'closed')>Closed</option>
                            </select>
                        </div>
                        @endif
                        @endcan

                        {{-- Requires Follow-up --}}
                        <div class="flex items-center">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="requires_followup" value="1" @checked(old('requires_followup', $fault->requires_followup)) 
                                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700">Requires Follow-up</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Description Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Fault Description</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror"
                                required>{{ old('description', $fault->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Symptoms Section --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Observed Symptoms</h2>
                        <button type="button" @click="symptoms.push('')" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 text-sm rounded-lg hover:bg-red-100 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Symptom
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">Update the list of observed symptoms.</p>

                    <template x-for="(symptom, index) in symptoms" :key="index">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-1">
                                <input type="text" x-model="symptom" :name="`symptoms[${index}]`" placeholder="e.g., Unusual noise, overheating, etc." 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                            <button type="button" @click="symptoms.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="symptoms.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No symptoms added. Click "Add Symptom" to list observed symptoms.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('faults.show', $fault) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Fault
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection