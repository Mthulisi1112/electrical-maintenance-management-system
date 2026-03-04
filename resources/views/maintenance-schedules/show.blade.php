@extends('layouts.app')

@section('title', $maintenanceSchedule->title . ' - Maintenance Schedule')

@section('content')
@php
    $checklistItems = is_string($maintenanceSchedule->checklist_items) ? json_decode($maintenanceSchedule->checklist_items, true) : $maintenanceSchedule->checklist_items;
    $requiredTools = is_string($maintenanceSchedule->required_tools) ? json_decode($maintenanceSchedule->required_tools, true) : $maintenanceSchedule->required_tools;
@endphp

    <div x-data="{ activeTab: 'details' }" class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">            
            {{-- Header with breadcrumbs and actions --}}
            <div class="mb-6">
                <nav class="flex items-center text-sm text-gray-500 mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                        <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                        <li><a href="{{ route('maintenance-schedules.index') }}" class="hover:text-gray-700">Schedules</a></li>
                        <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                        <li class="text-gray-900 font-medium truncate max-w-xs">{{ $maintenanceSchedule->title }}</li>
                    </ol>
                </nav>
                
                {{-- Title and badges row --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 break-words">{{ $maintenanceSchedule->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                @if($maintenanceSchedule->priority === 'critical') bg-red-100 text-red-800
                                @elseif($maintenanceSchedule->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($maintenanceSchedule->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($maintenanceSchedule->priority) }} Priority
                            </span>
                            @if($maintenanceSchedule->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Action buttons - properly spaced and wrapped --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @can('create', App\Models\WorkOrder::class)
                        <form method="POST" action="{{ route('maintenance-schedules.generate-work-order', $maintenanceSchedule) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-green-700 transition whitespace-nowrap">
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Generate Work Order
                            </button>
                        </form>
                        @endcan
                        
                        @can('update', $maintenanceSchedule)
                        <a href="{{ route('maintenance-schedules.edit', $maintenanceSchedule) }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-yellow-600 transition whitespace-nowrap">
                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Edit
                        </a>
                        @endcan
                        
                        <a href="{{ route('maintenance-schedules.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 transition whitespace-nowrap">
                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Asset</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $maintenanceSchedule->asset->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $maintenanceSchedule->asset->asset_code ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Frequency</p>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $maintenanceSchedule->frequency) }}</p>
                        <p class="text-xs text-gray-500">Every {{ $maintenanceSchedule->frequency }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Next Due</p>
                        <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($maintenanceSchedule->next_due_date)->format('M d, Y') }}</p>
                        @if($maintenanceSchedule->is_active && $maintenanceSchedule->next_due_date < now())
                            <p class="text-xs text-red-600">Overdue</p>
                        @elseif($maintenanceSchedule->next_due_date->isToday())
                            <p class="text-xs text-green-600">Today</p>
                        @elseif($maintenanceSchedule->next_due_date->isTomorrow())
                            <p class="text-xs text-yellow-600">Tomorrow</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Duration</p>
                        <p class="text-sm font-semibold text-gray-900">
                            @if($maintenanceSchedule->estimated_duration_minutes >= 60)
                                {{ floor($maintenanceSchedule->estimated_duration_minutes / 60) }}h {{ $maintenanceSchedule->estimated_duration_minutes % 60 }}m
                            @else
                                {{ $maintenanceSchedule->estimated_duration_minutes }} minutes
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">Estimated time</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Tabs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Tab Headers --}}
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px space-x-6 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'details'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'details', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'details' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Schedule Details
                    </button>
                    <button @click="activeTab = 'checklist'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'checklist', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'checklist' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Checklist
                    </button>
                    @if($requiredTools)
                    <button @click="activeTab = 'tools'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'tools', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tools' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Required Tools
                    </button>
                    @endif
                    @if($maintenanceSchedule->workOrders && $maintenanceSchedule->workOrders->count() > 0)
                    <button @click="activeTab = 'workorders'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'workorders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'workorders' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Work Orders
                    </button>
                    @endif
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="p-6">
                {{-- Details Tab --}}
                <div x-show="activeTab === 'details'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Information</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceSchedule->title }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Frequency</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $maintenanceSchedule->frequency) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($maintenanceSchedule->start_date)->format('M d, Y') }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Last Completed</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($maintenanceSchedule->last_completed_date)
                                                {{ \Carbon\Carbon::parse($maintenanceSchedule->last_completed_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Estimated Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($maintenanceSchedule->estimated_duration_minutes >= 60)
                                                {{ floor($maintenanceSchedule->estimated_duration_minutes / 60) }} hours {{ $maintenanceSchedule->estimated_duration_minutes % 60 }} minutes
                                            @else
                                                {{ $maintenanceSchedule->estimated_duration_minutes }} minutes
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceSchedule->creator->name ?? 'System' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $maintenanceSchedule->description }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Schedule Timeline</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full bg-green-500 block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Started</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($maintenanceSchedule->start_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($maintenanceSchedule->last_completed_date)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full bg-blue-500 block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Last Completed</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($maintenanceSchedule->last_completed_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full {{ $maintenanceSchedule->next_due_date < now() ? 'bg-red-500' : 'bg-yellow-500' }} block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Next Due</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($maintenanceSchedule->next_due_date)->format('M d, Y') }}</p>
                                            @if($maintenanceSchedule->next_due_date < now())
                                                <p class="text-xs text-red-600 mt-1">Overdue</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Schedule Status</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Current Status</span>
                                        @if($maintenanceSchedule->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Priority</span>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($maintenanceSchedule->priority === 'critical') bg-red-100 text-red-800
                                            @elseif($maintenanceSchedule->priority === 'high') bg-orange-100 text-orange-800
                                            @elseif($maintenanceSchedule->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($maintenanceSchedule->priority) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Last Updated</span>
                                        <span class="text-gray-900">{{ $maintenanceSchedule->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Checklist Tab --}}
                <div x-show="activeTab === 'checklist'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Checklist</h3>
                    @if($checklistItems && count($checklistItems) > 0)
                        <div class="space-y-3">
                            @foreach($checklistItems as $item)
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0 mr-3">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-100">
                                            <svg class="h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $item }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No checklist items for this schedule.</p>
                        </div>
                    @endif
                </div>

                {{-- Required Tools Tab --}}
                @if($requiredTools)
                <div x-show="activeTab === 'tools'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Tools & Equipment</h3>
                    @if(count($requiredTools) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($requiredTools as $tool)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0 mr-3">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-purple-100">
                                            <svg class="h-3 w-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $tool }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No tools specified for this schedule.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Work Orders Tab --}}
                @if($maintenanceSchedule->workOrders && $maintenanceSchedule->workOrders->count() > 0)
                <div x-show="activeTab === 'workorders'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Generated Work Orders</h3>
                    <div class="space-y-4">
                        @foreach($maintenanceSchedule->workOrders as $workOrder)
                        <a href="{{ route('work-orders.show', $workOrder) }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $workOrder->work_order_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $workOrder->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Scheduled: {{ \Carbon\Carbon::parse($workOrder->scheduled_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                        @if($workOrder->status === 'completed') bg-green-100 text-green-800
                                        @elseif($workOrder->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($workOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($workOrder->status === 'verified') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($workOrder->status) }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">Assigned to: {{ $workOrder->technician->name ?? 'Unassigned' }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection