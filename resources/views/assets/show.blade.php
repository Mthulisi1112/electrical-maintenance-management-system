@extends('layouts.app')

@section('title', $asset->name . ' - Asset Details')

@section('content')
<div x-data="{ 
    activeTab: 'overview',
    showImageModal: false,
    selectedImage: null
}" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with breadcrumbs and actions --}}
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <nav class="flex items-center text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                            </li>
                            <li>
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </li>
                            <li>
                                <a href="{{ route('assets.index') }}" class="hover:text-gray-700">Assets</a>
                            </li>
                            <li>
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </li>
                            <li class="text-gray-900 font-medium truncate">{{ $asset->asset_code }}</li>
                        </ol>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $asset->name }}</h1>
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
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Asset Code: <span class="font-mono">{{ $asset->asset_code }}</span></p>
                </div>
                
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    @can('update', $asset)
                    <a href="{{ route('assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 shadow-sm transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Edit Asset
                    </a>
                    @endcan
                    
                    <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
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
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Power Rating</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $asset->power_rating ?? 'N/A' }} kW</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Voltage</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $asset->voltage_rating ?? 'N/A' }} V</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Current</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $asset->current_rating ?? 'N/A' }} A</p>
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
                        <p class="text-sm font-medium text-gray-500">Installation</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($asset->installation_date)->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Tabs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Tab Headers --}}
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px space-x-6 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'overview'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Overview
                    </button>
                    <button @click="activeTab = 'specifications'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'specifications', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'specifications' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Technical Specifications
                    </button>
                    <button @click="activeTab = 'maintenance'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'maintenance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'maintenance' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Maintenance History
                    </button>
                    <button @click="activeTab = 'faults'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'faults', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'faults' }" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Fault History
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="p-6">
                {{-- Overview Tab --}}
                <div x-show="activeTab === 'overview'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Left Column - Basic Info --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">General Information</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Asset Code</dt>
                                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $asset->asset_code }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $asset->type) }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Manufacturer</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $asset->manufacturer ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Model</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $asset->model ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $asset->serial_number ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <dt class="text-sm font-medium text-gray-500">Installation Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($asset->installation_date)->format('F j, Y') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Details</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="h-5 w-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $asset->location }}</p>
                                            <p class="text-sm text-gray-500 mt-1">Full address or area within facility</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - QR Code & Status --}}
                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Asset QR Code</h3>
                                <div class="bg-white p-4 rounded-lg inline-block mx-auto shadow-sm">
                                    @if($asset->qr_code)
                                        {{-- Display the QR code image --}}
                                        <img src="{{ route('assets.qrcode', $asset) }}" 
                                            alt="QR Code for {{ $asset->asset_code }}"
                                            class="h-32 w-32 mx-auto">
                                    @else
                                        {{-- Generate and display QR code on the fly --}}
                                        <img src="{{ route('assets.qrcode', $asset) }}" 
                                            alt="QR Code for {{ $asset->asset_code }}"
                                            class="h-32 w-32 mx-auto">
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Scan to access asset details</p>
                                
                                {{-- Download QR Code Button --}}
                                <a href="{{ route('assets.qrcode', $asset) }}?download=1" 
                                class="mt-3 inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition"
                                download="qrcode-{{ $asset->asset_code }}.svg">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download QR
                                </a>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-sm font-medium text-gray-700 mb-3">Asset Status Timeline</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Current Status</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($asset->status === 'operational') bg-green-100 text-green-800
                                            @elseif($asset->status === 'maintenance') bg-yellow-100 text-yellow-800
                                            @elseif($asset->status === 'faulty') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">QR Code ID</span>
                                        <span class="text-gray-900 font-mono text-xs">{{ $asset->qr_code ?? 'Not generated' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Last Updated</span>
                                        <span class="text-gray-900">{{ $asset->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Created By</span>
                                        <span class="text-gray-900">{{ $asset->creator->name ?? 'System' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Specifications Tab --}}
                <div x-show="activeTab === 'specifications'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical Specifications</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($asset->technical_specs)
                            @foreach(json_decode($asset->technical_specs, true) as $key => $value)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                            </div>
                            @endforeach
                        @else
                            <div class="col-span-2 text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No technical specifications</h3>
                                <p class="mt-1 text-sm text-gray-500">No specifications have been added for this asset.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Maintenance History Tab --}}
                <div x-show="activeTab === 'maintenance'" x-cloak>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Maintenance History</h3>
                        @can('create', App\Models\WorkOrder::class)
                        <a href="{{ route('work-orders.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Schedule Maintenance
                        </a>
                        @endcan
                    </div>
                    
                    @if($asset->workOrders && $asset->workOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Work Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technician</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($asset->workOrders as $workOrder)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $workOrder->work_order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($workOrder->scheduled_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($workOrder->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $workOrder->technician->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($workOrder->status === 'completed') bg-green-100 text-green-800
                                            @elseif($workOrder->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($workOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($workOrder->status === 'verified') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ str_replace('_', ' ', ucfirst($workOrder->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('work-orders.show', $workOrder) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No maintenance history</h3>
                        <p class="mt-1 text-sm text-gray-500">No maintenance records found for this asset.</p>
                    </div>
                    @endif
                </div>

                {{-- Fault History Tab --}}
                <div x-show="activeTab === 'faults'" x-cloak>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Fault History</h3>
                        @can('create', App\Models\Fault::class)
                        <a href="{{ route('faults.create', ['asset_id' => $asset->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Report Fault
                        </a>
                        @endcan
                    </div>

                    @if($asset->faults && $asset->faults->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fault #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reported By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($asset->faults as $fault)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $fault->fault_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($fault->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst(str_replace('_', ' ', $fault->fault_type)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($fault->severity === 'critical') bg-red-100 text-red-800
                                            @elseif($fault->severity === 'high') bg-orange-100 text-orange-800
                                            @elseif($fault->severity === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($fault->severity) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($fault->status === 'resolved') bg-green-100 text-green-800
                                            @elseif($fault->status === 'investigating') bg-blue-100 text-blue-800
                                            @elseif($fault->status === 'reported') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($fault->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $fault->reportedBy->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('faults.show', $fault) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No fault history</h3>
                        <p class="mt-1 text-sm text-gray-500">No faults have been reported for this asset.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection