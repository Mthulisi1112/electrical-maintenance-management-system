@extends('layouts.app')

@section('title', 'Work Order ' . $workOrder->work_order_number)

@section('content')
@php
    $checklist = is_string($workOrder->checklist) ? json_decode($workOrder->checklist, true) : $workOrder->checklist;
    $checklistResponses = is_string($workOrder->checklist_responses) ? json_decode($workOrder->checklist_responses, true) : $workOrder->checklist_responses;
    $partsUsed = is_string($workOrder->parts_used) ? json_decode($workOrder->parts_used, true) : $workOrder->parts_used;
    $measurements = $workOrder->maintenanceLog ? 
        (is_string($workOrder->maintenanceLog->measurements) ? 
            json_decode($workOrder->maintenanceLog->measurements, true) : 
            $workOrder->maintenanceLog->measurements) : [];
@endphp

<div x-data="{ 
    activeTab: 'details',
    showStartModal: false,
    showCompleteModal: false,
    showVerifyModal: false,
    parts: {{ json_encode($partsUsed ?? []) }},
    measurements: {{ json_encode($measurements ?? []) }},
    checklist: {{ json_encode($checklist ?? []) }}
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with breadcrumbs and actions --}}
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-500 mb-3" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li><a href="{{ route('work-orders.index') }}" class="hover:text-gray-700">Work Orders</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li class="text-gray-900 font-medium truncate max-w-xs">{{ $workOrder->work_order_number }}</li>
                </ol>
            </nav>
            
            {{-- Title section with status badge inline --}}
            <div class="mb-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 break-words">{{ $workOrder->title }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
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
                </div>
            </div>
            
            {{-- Action buttons - on their own line with proper spacing --}}
            <div class="flex flex-wrap items-center gap-3">
                @can('update', $workOrder)
                    @if($workOrder->status === 'pending')
                        <button @click="showStartModal = true" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-green-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start Work
                        </button>
                    @elseif($workOrder->status === 'in_progress')
                        <button @click="showCompleteModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Complete Work
                        </button>
                    @elseif($workOrder->status === 'completed' && Auth::user()->can('verify', $workOrder))
                        <button @click="showVerifyModal = true" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-purple-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verify Work
                        </button>
                    @endif
                @endcan
                
                <a href="{{ route('work-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Asset</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $workOrder->asset->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $workOrder->asset->asset_code ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Technician</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $workOrder->technician->name ?? 'Unassigned' }}</p>
                        <p class="text-xs text-gray-500">{{ $workOrder->technician->employee_id ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Schedule</p>
                        <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($workOrder->scheduled_date)->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">Type: {{ ucfirst($workOrder->type) }}</p>
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
                        <p class="text-sm font-medium text-gray-500">Timeline</p>
                        @if($workOrder->started_at)
                            <p class="text-sm font-semibold text-gray-900">Started: {{ \Carbon\Carbon::parse($workOrder->started_at)->format('M d') }}</p>
                        @endif
                        @if($workOrder->completed_date)
                            <p class="text-xs text-gray-500">Completed: {{ \Carbon\Carbon::parse($workOrder->completed_date)->format('M d') }}</p>
                        @endif
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
                        Work Order Details
                    </button>
                    @if($checklist)
                    <button @click="activeTab = 'checklist'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'checklist', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'checklist' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Checklist
                    </button>
                    @endif
                    @if($partsUsed)
                    <button @click="activeTab = 'parts'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'parts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'parts' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Parts Used
                    </button>
                    @endif
                    @if($workOrder->maintenanceLog)
                    <button @click="activeTab = 'log'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'log', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'log' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Maintenance Log
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Order Information</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Work Order Number</dt>
                                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $workOrder->work_order_number }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $workOrder->type }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if(($workOrder->priority ?? 'medium') === 'critical') bg-red-100 text-red-800
                                                @elseif(($workOrder->priority ?? 'medium') === 'high') bg-orange-100 text-orange-800
                                                @elseif(($workOrder->priority ?? 'medium') === 'medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst($workOrder->priority ?? 'medium') }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Time Spent</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($workOrder->time_spent_minutes)
                                                {{ floor($workOrder->time_spent_minutes / 60) }}h {{ $workOrder->time_spent_minutes % 60 }}m
                                            @else
                                                N/A
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $workOrder->description }}</p>
                                </div>
                            </div>

                            @if($workOrder->technician_remarks)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Technician Remarks</h3>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                    <p class="text-sm text-gray-700">{{ $workOrder->technician_remarks }}</p>
                                </div>
                            </div>
                            @endif

                            @if($workOrder->supervisor_remarks)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Supervisor Remarks</h3>
                                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                                    <p class="text-sm text-gray-700">{{ $workOrder->supervisor_remarks }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Assignment Details</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Supervisor</span>
                                        <span class="font-medium text-gray-900">{{ $workOrder->supervisor->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Created At</span>
                                        <span class="text-gray-900">{{ $workOrder->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Last Updated</span>
                                        <span class="text-gray-900">{{ $workOrder->updated_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    {{-- Maintenance Schedule Link - Fixed alignment --}}
                                    @if($workOrder->maintenanceSchedule)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">Maintenance Schedule</span>
                                            <a href="{{ route('maintenance-schedules.show', $workOrder->maintenanceSchedule) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                                <span class="truncate max-w-[150px]">{{ $workOrder->maintenanceSchedule->title }}</span>
                                                <svg class="h-4 w-4 ml-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="flex items-center justify-between text-xs text-gray-500 mt-1">
                                            <span>Frequency</span>
                                            <span class="capitalize">{{ str_replace('_', ' ', $workOrder->maintenanceSchedule->frequency) }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Checklist Tab --}}
                @if($checklist)
                <div x-show="activeTab === 'checklist'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Checklist</h3>
                    <div class="space-y-3">
                        @foreach($checklist as $index => $item)
                            @php
                                $task = is_array($item) ? ($item['task'] ?? $item) : $item;
                                $required = is_array($item) ? ($item['required'] ?? false) : false;
                                $completed = isset($checklistResponses[$index]['completed']) ? $checklistResponses[$index]['completed'] : false;
                            @endphp
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-shrink-0 mr-3">
                                    @if($completed)
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-green-100">
                                            <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-gray-200">
                                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $task }}</p>
                                    @if($required)
                                        <span class="text-xs text-red-500">Required</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Parts Used Tab --}}
                @if($partsUsed)
                <div x-show="activeTab === 'parts'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Parts Used</h3>
                    @if(count($partsUsed) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Part Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Part Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($partsUsed as $part)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $part['name'] ?? '' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $part['part_number'] ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $part['quantity'] ?? 1 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No parts were used for this work order.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- Maintenance Log Tab --}}
                @if($workOrder->maintenanceLog)
                <div x-show="activeTab === 'log'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Log</h3>
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Performed By</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $workOrder->maintenanceLog->performedBy->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $workOrder->maintenanceLog->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Result</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($workOrder->maintenanceLog->result === 'successful') bg-green-100 text-green-800
                                            @elseif($workOrder->maintenanceLog->result === 'partial') bg-yellow-100 text-yellow-800
                                            @elseif($workOrder->maintenanceLog->result === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($workOrder->maintenanceLog->result) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Actions Taken</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-700">{{ $workOrder->maintenanceLog->actions_taken }}</p>
                            </div>
                        </div>

                        @if($workOrder->maintenanceLog->measurements)
                            @php
                                $measurements = is_string($workOrder->maintenanceLog->measurements) 
                                    ? json_decode($workOrder->maintenanceLog->measurements, true) 
                                    : $workOrder->maintenanceLog->measurements;
                            @endphp
                            @if(is_array($measurements) && count($measurements) > 0)
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-3">Measurements</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($measurements as $measurement)
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm font-medium text-gray-700">{{ $measurement['name'] ?? '' }}</p>
                                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                                {{ $measurement['value'] ?? '' }} 
                                                <span class="text-sm font-normal text-gray-500">{{ $measurement['unit'] ?? '' }}</span>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Start Work Modal --}}
    <div x-show="showStartModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showStartModal = false"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <form method="POST" action="{{ route('work-orders.start', $workOrder) }}" class="p-6">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Start Work Order</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you ready to start working on this work order? The status will be updated to "In Progress".
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto">
                            Start Work
                        </button>
                        <button type="button" @click="showStartModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Complete Work Modal --}}
    <div x-show="showCompleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCompleteModal = false"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <div class="bg-white px-6 py-5 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Complete Work Order</h3>
                    <p class="mt-1 text-sm text-gray-500">Record the work performed, measurements, and parts used.</p>
                </div>
                
                <form method="POST" action="{{ route('work-orders.complete', $workOrder) }}" class="p-6 max-h-[70vh] overflow-y-auto">
                    @csrf
                    
                    <div class="space-y-6">
                        {{-- Time Spent --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="time_spent_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                                Time Spent (minutes) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="time_spent_minutes" id="time_spent_minutes" required
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., 120" min="1">
                            <p class="mt-1 text-xs text-gray-500">Enter the total time spent on this work order in minutes.</p>
                        </div>

                        {{-- Actions Taken --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="actions_taken" class="block text-sm font-medium text-gray-700 mb-1">
                                Actions Taken <span class="text-red-500">*</span>
                            </label>
                            <textarea name="actions_taken" id="actions_taken" rows="4" required
                                      class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe the work performed in detail..."></textarea>
                        </div>

                        {{-- Measurements Section --}}
                        <div x-data="{ measurements: [{ name: '', value: '', unit: '' }] }" class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">Measurements</h4>
                                    <p class="text-xs text-gray-500">Add any readings or measurements taken</p>
                                </div>
                                <button type="button" @click="measurements.push({ name: '', value: '', unit: '' })" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-xs rounded-lg hover:bg-blue-100 transition">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Measurement
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-for="(measurement, index) in measurements" :key="index">
                                    <div class="flex items-start space-x-2 bg-white p-3 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <input type="text" x-model="measurement.name" :name="`measurements[${index}][name]`" 
                                                   placeholder="Name (e.g., Temperature)" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-24">
                                            <input type="number" step="0.01" x-model="measurement.value" :name="`measurements[${index}][value]`" 
                                                   placeholder="Value" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-20">
                                            <input type="text" x-model="measurement.unit" :name="`measurements[${index}][unit]`" 
                                                   placeholder="Unit" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <button type="button" @click="measurements.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div x-show="measurements.length === 0" class="text-center py-4 bg-white rounded-lg border border-gray-200 mt-2">
                                <p class="text-xs text-gray-500">No measurements added. Click "Add Measurement" to add readings.</p>
                            </div>
                        </div>

                        {{-- Parts Used Section --}}
                        <div x-data="{ parts: [{ name: '', quantity: 1, part_number: '' }] }" class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">Parts Used</h4>
                                    <p class="text-xs text-gray-500">Add any parts or components replaced</p>
                                </div>
                                <button type="button" @click="parts.push({ name: '', quantity: 1, part_number: '' })" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-xs rounded-lg hover:bg-blue-100 transition">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Part
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-for="(part, index) in parts" :key="index">
                                    <div class="flex items-start space-x-2 bg-white p-3 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <input type="text" x-model="part.name" :name="`parts_used[${index}][name]`" 
                                                   placeholder="Part name" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-24">
                                            <input type="number" x-model="part.quantity" :name="`parts_used[${index}][quantity]`" 
                                                   placeholder="Qty" min="1" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-32">
                                            <input type="text" x-model="part.part_number" :name="`parts_used[${index}][part_number]`" 
                                                   placeholder="Part #" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <button type="button" @click="parts.splice(index, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div x-show="parts.length === 0" class="text-center py-4 bg-white rounded-lg border border-gray-200 mt-2">
                                <p class="text-xs text-gray-500">No parts added. Click "Add Part" to list parts used.</p>
                            </div>
                        </div>

                        {{-- Technician Remarks --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="technician_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                                Additional Remarks
                            </label>
                            <textarea name="technician_remarks" id="technician_remarks" rows="2"
                                      class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any additional notes or observations..."></textarea>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-200 pt-4">
                        <button type="button" @click="showCompleteModal = false" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Complete Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Verify Work Modal --}}
    <div x-show="showVerifyModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showVerifyModal = false"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <form method="POST" action="{{ route('work-orders.verify', $workOrder) }}" class="p-6">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Verify Work Order</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Please review the completed work and add any verification notes.
                                </p>
                                <div>
                                    <label for="supervisor_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                                        Verification Remarks
                                    </label>
                                    <textarea name="supervisor_remarks" id="supervisor_remarks" rows="3"
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                              placeholder="Add your verification notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 sm:ml-3 sm:w-auto">
                            Verify & Complete
                        </button>
                        <button type="button" @click="showVerifyModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection