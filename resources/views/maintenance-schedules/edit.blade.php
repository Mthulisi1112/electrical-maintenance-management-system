@extends('layouts.app')

@section('title', 'Edit Maintenance Schedule - EMMS')

@section('content')
@php
    $checklistItems = is_string($maintenanceSchedule->checklist_items) ? json_decode($maintenanceSchedule->checklist_items, true) : $maintenanceSchedule->checklist_items;
    $requiredTools = is_string($maintenanceSchedule->required_tools) ? json_decode($maintenanceSchedule->required_tools, true) : $maintenanceSchedule->required_tools;
    $estimatedHours = floor($maintenanceSchedule->estimated_duration_minutes / 60);
    $estimatedMinutes = $maintenanceSchedule->estimated_duration_minutes % 60;
@endphp

<div x-data="{ 
    checklist: {{ json_encode($checklistItems ?? []) }},
    tools: {{ json_encode($requiredTools ?? []) }},
    estimatedHours: {{ $estimatedHours }},
    estimatedMinutes: {{ $estimatedMinutes }}
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
                    <li><a href="{{ route('maintenance-schedules.index') }}" class="hover:text-gray-700">Schedules</a></li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li><a href="{{ route('maintenance-schedules.show', $maintenanceSchedule) }}" class="hover:text-gray-700">{{ $maintenanceSchedule->title }}</a></li>
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Maintenance Schedule</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $maintenanceSchedule->title }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($maintenanceSchedule->priority === 'critical') bg-red-100 text-red-800
                        @elseif($maintenanceSchedule->priority === 'high') bg-orange-100 text-orange-800
                        @elseif($maintenanceSchedule->priority === 'medium') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($maintenanceSchedule->priority) }} Priority
                    </span>
                    <a href="{{ route('maintenance-schedules.show', $maintenanceSchedule) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Schedule
                    </a>
                </div>
            </div>
        </div>

        {{-- Edit Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('maintenance-schedules.update', $maintenanceSchedule) }}" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Schedule Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $maintenanceSchedule->title) }}" 
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
                                    <option value="{{ $asset->id }}" @selected(old('asset_id', $maintenanceSchedule->asset_id) == $asset->id)>
                                        {{ $asset->name }} ({{ $asset->asset_code }}) - {{ $asset->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Frequency --}}
                        <div>
                            <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">
                                Frequency <span class="text-red-500">*</span>
                            </label>
                            <select name="frequency" id="frequency" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('frequency') border-red-500 @enderror" required>
                                <option value="">Select Frequency</option>
                                <option value="daily" @selected(old('frequency', $maintenanceSchedule->frequency) == 'daily')>Daily</option>
                                <option value="weekly" @selected(old('frequency', $maintenanceSchedule->frequency) == 'weekly')>Weekly</option>
                                <option value="monthly" @selected(old('frequency', $maintenanceSchedule->frequency) == 'monthly')>Monthly</option>
                                <option value="quarterly" @selected(old('frequency', $maintenanceSchedule->frequency) == 'quarterly')>Quarterly</option>
                                <option value="semi_annual" @selected(old('frequency', $maintenanceSchedule->frequency) == 'semi_annual')>Semi-Annual</option>
                                <option value="annual" @selected(old('frequency', $maintenanceSchedule->frequency) == 'annual')>Annual</option>
                            </select>
                            @error('frequency')
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
                                    @if(old('priority', $maintenanceSchedule->priority) == 'low') border-green-500 bg-green-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="low" class="sr-only" @checked(old('priority', $maintenanceSchedule->priority) == 'low')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                        <span class="text-xs font-medium">Low</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $maintenanceSchedule->priority) == 'medium') border-yellow-500 bg-yellow-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="medium" class="sr-only" @checked(old('priority', $maintenanceSchedule->priority) == 'medium')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                        <span class="text-xs font-medium">Medium</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $maintenanceSchedule->priority) == 'high') border-orange-500 bg-orange-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="high" class="sr-only" @checked(old('priority', $maintenanceSchedule->priority) == 'high')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span>
                                        <span class="text-xs font-medium">High</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', $maintenanceSchedule->priority) == 'critical') border-red-500 bg-red-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="critical" class="sr-only" @checked(old('priority', $maintenanceSchedule->priority) == 'critical')>
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

                        {{-- Start Date --}}
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $maintenanceSchedule->start_date->format('Y-m-d')) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror"
                                required>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Is Active --}}
                        <div class="flex items-center">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $maintenanceSchedule->is_active)) 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Schedule is active</span>
                            </label>
                        </div>

                        {{-- Estimated Duration --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Estimated Duration <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="estimated_hours" class="block text-xs text-gray-500 mb-1">Hours</label>
                                    <input type="number" x-model="estimatedHours" id="estimated_hours" min="0" max="24" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="estimated_minutes" class="block text-xs text-gray-500 mb-1">Minutes</label>
                                    <input type="number" x-model="estimatedMinutes" id="estimated_minutes" min="0" max="59" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <input type="hidden" name="estimated_duration_minutes" :value="(parseInt(estimatedHours) * 60) + parseInt(estimatedMinutes)">
                            @error('estimated_duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Description Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                required>{{ old('description', $maintenanceSchedule->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Checklist Section --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Checklist Items</h2>
                        <button type="button" @click="checklist.push('')" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-sm rounded-lg hover:bg-blue-100 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Checklist Item
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">Update the tasks that need to be completed during maintenance.</p>

                    <template x-for="(item, index) in checklist" :key="index">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-1">
                                <input type="text" x-model="item" :name="`checklist_items[${index}]`" placeholder="e.g., Check oil levels, Inspect belts, etc." 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="button" @click="checklist.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Required Tools Section --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Required Tools</h2>
                        <button type="button" @click="tools.push('')" class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 text-sm rounded-lg hover:bg-purple-100 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Tool
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4">Update any special tools or equipment needed.</p>

                    <template x-for="(tool, index) in tools" :key="index">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-1">
                                <input type="text" x-model="tool" :name="`required_tools[${index}]`" placeholder="e.g., Multimeter, Thermal Camera, etc." 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="button" @click="tools.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('maintenance-schedules.show', $maintenanceSchedule) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection