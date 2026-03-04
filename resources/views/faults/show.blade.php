@extends('layouts.app')

@section('title', 'Fault ' . $fault->fault_number)

@section('content')
@php
    $symptoms = is_string($fault->symptoms) ? json_decode($fault->symptoms, true) : $fault->symptoms;
    $partsReplaced = is_string($fault->parts_replaced) ? json_decode($fault->parts_replaced, true) : $fault->parts_replaced;
@endphp

<div x-data="{ 
    activeTab: 'details',
    showAssignModal: false,
    showResolveModal: false,
    selectedTechnician: null
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with breadcrumbs and actions --}}
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li><a href="{{ route('faults.index') }}" class="hover:text-gray-700">Faults</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li class="text-gray-900 font-medium">{{ $fault->fault_number }}</li>
                </ol>
            </nav>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">Fault Report</h1>
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($fault->status === 'resolved') bg-green-100 text-green-800
                        @elseif($fault->status === 'closed') bg-gray-100 text-gray-800
                        @elseif($fault->status === 'investigating') bg-blue-100 text-blue-800
                        @elseif($fault->status === 'in_progress') bg-purple-100 text-purple-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $fault->status)) }}
                    </span>
                </div>
                
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    @can('assign', $fault)
                    <button @click="showAssignModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Assign Technician
                    </button>
                    @endcan
                    
                    @can('resolve', $fault)
                    <button @click="showResolveModal = true" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Resolve Fault
                    </button>
                    @endcan
                    
                    <a href="{{ route('faults.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Asset</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fault->asset->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $fault->asset->asset_code ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Reported By</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fault->reportedBy->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $fault->reportedBy->employee_id ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Downtime</p>
                        <p class="text-sm font-semibold text-gray-900">
                            @if($fault->downtime_minutes)
                                {{ floor($fault->downtime_minutes / 60) }}h {{ $fault->downtime_minutes % 60 }}m
                            @else
                                In Progress
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">Started: {{ $fault->downtime_start->format('M d, H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Assigned To</p>
                        @if($fault->assignedTo)
                            <p class="text-sm font-semibold text-gray-900">{{ $fault->assignedTo->name }}</p>
                            <p class="text-xs text-gray-500">Since {{ $fault->updated_at->format('M d') }}</p>
                        @else
                            <p class="text-sm text-gray-500">Unassigned</p>
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
                    <button @click="activeTab = 'details'" :class="{ 'border-red-500 text-red-600': activeTab === 'details', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'details' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Fault Details
                    </button>
                    <button @click="activeTab = 'symptoms'" :class="{ 'border-red-500 text-red-600': activeTab === 'symptoms', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'symptoms' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Symptoms
                    </button>
                    @if($fault->status === 'resolved' || $fault->status === 'closed')
                    <button @click="activeTab = 'resolution'" :class="{ 'border-red-500 text-red-600': activeTab === 'resolution', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'resolution' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Resolution
                    </button>
                    @endif
                    @if($fault->workOrders && $fault->workOrders->count() > 0)
                    <button @click="activeTab = 'workorders'" :class="{ 'border-red-500 text-red-600': activeTab === 'workorders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'workorders' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fault Information</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Fault Number</dt>
                                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $fault->fault_number }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Fault Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $fault->fault_type) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Reported On</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $fault->created_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $fault->updated_at->diffForHumans() }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $fault->description }}</p>
                                </div>
                            </div>

                            @if($fault->images && count($fault->images) > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attached Images</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    @foreach($fault->images as $image)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($image) }}" alt="Fault image" class="h-24 w-24 object-cover rounded-lg border border-gray-200">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition rounded-lg flex items-center justify-center">
                                            <a href="{{ Storage::url($image) }}" target="_blank" class="opacity-0 group-hover:opacity-100 text-white">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Timeline</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full bg-yellow-500 block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Fault Reported</p>
                                            <p class="text-xs text-gray-500">{{ $fault->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($fault->assigned_to)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full bg-blue-500 block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Assigned to {{ $fault->assignedTo->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $fault->updated_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($fault->downtime_end)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="h-2 w-2 mt-2 rounded-full bg-green-500 block"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-900">Fault Resolved</p>
                                            <p class="text-xs text-gray-500">{{ $fault->downtime_end->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if($fault->requires_followup)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-yellow-800">Follow-up Required</p>
                                        <p class="text-xs text-yellow-700 mt-1">This fault requires additional follow-up actions.</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Symptoms Tab --}}
                <div x-show="activeTab === 'symptoms'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Observed Symptoms</h3>
                    @if($symptoms && count($symptoms) > 0)
                        <div class="space-y-3">
                            @foreach($symptoms as $symptom)
                            <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 mr-3">
                                    <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-100">
                                        <svg class="h-3 w-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $symptom }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No symptoms recorded for this fault.</p>
                        </div>
                    @endif
                </div>

                {{-- Resolution Tab --}}
                @if($fault->status === 'resolved' || $fault->status === 'closed')
                <div x-show="activeTab === 'resolution'" x-cloak>
                    <div class="space-y-6">
                        @if($fault->root_cause)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Root Cause</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-700">{{ $fault->root_cause }}</p>
                            </div>
                        </div>
                        @endif

                        @if($fault->corrective_actions)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Corrective Actions</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-700">{{ $fault->corrective_actions }}</p>
                            </div>
                        </div>
                        @endif

                        @if($partsReplaced && count($partsReplaced) > 0)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Parts Replaced</h3>
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
                                        @foreach($partsReplaced as $part)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $part['name'] ?? '' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $part['part_number'] ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $part['quantity'] ?? 1 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Work Orders Tab --}}
                @if($fault->workOrders && $fault->workOrders->count() > 0)
                <div x-show="activeTab === 'workorders'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Work Orders</h3>
                    <div class="space-y-4">
                        @foreach($fault->workOrders as $workOrder)
                        <a href="{{ route('work-orders.show', $workOrder) }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $workOrder->work_order_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $workOrder->title }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                    @if($workOrder->status === 'completed') bg-green-100 text-green-800
                                    @elseif($workOrder->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($workOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($workOrder->status) }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Assign Technician Modal --}}
    <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAssignModal = false"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <form method="POST" action="{{ route('faults.assign', $fault) }}" class="p-6">
                    @csrf
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Technician</h3>
                    
                    <div class="mb-4">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                            Select Technician <span class="text-red-500">*</span>
                        </label>
                        <select name="assigned_to" id="assigned_to" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a technician...</option>
                            @foreach($technicians ?? [] as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }} ({{ $technician->employee_id ?? 'No ID' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAssignModal = false" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700">
                            Assign Technician
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Resolve Fault Modal --}}
    <div x-show="showResolveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showResolveModal = false"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <form method="POST" action="{{ route('faults.resolve', $fault) }}" class="p-6">
                    @csrf
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Resolve Fault</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="root_cause" class="block text-sm font-medium text-gray-700 mb-1">
                                Root Cause <span class="text-red-500">*</span>
                            </label>
                            <textarea name="root_cause" id="root_cause" rows="3" required
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                      placeholder="What caused the fault?"></textarea>
                        </div>

                        <div>
                            <label for="corrective_actions" class="block text-sm font-medium text-gray-700 mb-1">
                                Corrective Actions <span class="text-red-500">*</span>
                            </label>
                            <textarea name="corrective_actions" id="corrective_actions" rows="3" required
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                      placeholder="What was done to fix it?"></textarea>
                        </div>

                        {{-- Parts Replaced --}}
                        <div x-data="{ parts: [{ name: '', quantity: 1, part_number: '' }] }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Parts Replaced</label>
                                <button type="button" @click="parts.push({ name: '', quantity: 1, part_number: '' })" 
                                        class="text-sm text-blue-600 hover:text-blue-800">
                                    + Add Part
                                </button>
                            </div>
                            <template x-for="(part, index) in parts" :key="index">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" x-model="part.name" :name="`parts_replaced[${index}][name]`" 
                                           placeholder="Part name" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <input type="number" x-model="part.quantity" :name="`parts_replaced[${index}][quantity]`" 
                                           placeholder="Qty" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" min="1">
                                    <input type="text" x-model="part.part_number" :name="`parts_replaced[${index}][part_number]`" 
                                           placeholder="Part #" class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <button type="button" @click="parts.splice(index, 1)" class="text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showResolveModal = false" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-green-700">
                            Resolve Fault
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection