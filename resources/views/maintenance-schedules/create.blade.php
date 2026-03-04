@extends('layouts.app')

@section('title', 'Create Maintenance Schedule - EMMS')

@section('content')
<div x-data="{ 
    checklist: [],
    tools: [],
    estimatedHours: 0,
    estimatedMinutes: 0
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
                    <li class="text-gray-900 font-medium">Create New Schedule</li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Maintenance Schedule</h1>
                    <p class="mt-2 text-sm text-gray-600">Create a new preventive maintenance schedule. All fields marked with <span class="text-red-500">*</span> are required.</p>
                </div>
                <a href="{{ route('maintenance-schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Schedules
                </a>
            </div>
        </div>

        {{-- Create Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('maintenance-schedules.store') }}" class="divide-y divide-gray-200">
                @csrf

                {{-- Basic Information Section --}}
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Schedule Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                                placeholder="e.g., Monthly Motor Inspection" required>
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
                                    <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
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
                                <option value="daily" @selected(old('frequency') == 'daily')>Daily</option>
                                <option value="weekly" @selected(old('frequency') == 'weekly')>Weekly</option>
                                <option value="monthly" @selected(old('frequency') == 'monthly')>Monthly</option>
                                <option value="quarterly" @selected(old('frequency') == 'quarterly')>Quarterly</option>
                                <option value="semi_annual" @selected(old('frequency') == 'semi_annual')>Semi-Annual</option>
                                <option value="annual" @selected(old('frequency') == 'annual')>Annual</option>
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
                                    @if(old('priority', 'medium') == 'low') border-green-500 bg-green-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="low" class="sr-only" @checked(old('priority', 'medium') == 'low')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                        <span class="text-xs font-medium">Low</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority', 'medium') == 'medium') border-yellow-500 bg-yellow-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="medium" class="sr-only" @checked(old('priority', 'medium') == 'medium')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                        <span class="text-xs font-medium">Medium</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority') == 'high') border-orange-500 bg-orange-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="high" class="sr-only" @checked(old('priority') == 'high')>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 mr-1"></span>
                                        <span class="text-xs font-medium">High</span>
                                    </span>
                                </label>
                                <label class="relative flex items-center justify-center p-2 border rounded-lg cursor-pointer transition
                                    @if(old('priority') == 'critical') border-red-500 bg-red-50 @else border-gray-200 hover:bg-gray-50 @endif">
                                    <input type="radio" name="priority" value="critical" class="sr-only" @checked(old('priority') == 'critical')>
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
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror"
                                required>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            <p class="mt-2 text-xs text-gray-500" x-show="estimatedHours > 0 || estimatedMinutes > 0">
                                Total: <span x-text="(parseInt(estimatedHours) * 60) + parseInt(estimatedMinutes)"></span> minutes
                            </p>
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
                                placeholder="Describe the maintenance tasks to be performed..." required>{{ old('description') }}</textarea>
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
                    
                    <p class="text-sm text-gray-500 mb-4">List the tasks that need to be completed during maintenance.</p>

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

                    <div x-show="checklist.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No checklist items added. Click "Add Checklist Item" to create tasks.</p>
                    </div>
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
                    
                    <p class="text-sm text-gray-500 mb-4">Specify any special tools or equipment needed.</p>

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

                    <div x-show="tools.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No tools added. Click "Add Tool" to specify required equipment.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('maintenance-schedules.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection