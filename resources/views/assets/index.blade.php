@extends('layouts.app')

@section('title', 'Assets - EMMS')

@section('content')
<div x-data="{ 
    showFilters: false,
    deleteModal: false,
    faultToDelete: null,
    confirmDelete(faultId) {
        this.faultToDelete = faultId;
        this.deleteModal = true;
    }
}" class="py-8">

   <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header with stats summary --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Electrical Assets</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage and monitor all registered electrical equipment</p>
                </div>
                @can('create', App\Models\Asset::class)
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Asset
                    </a>
                </div>
                @endcan
            </div>

            {{-- Quick stats cards --}}
            @php
                $totalAssets = $assets->total();
                $operational = $assets->where('status', 'operational')->count();
                $maintenance = $assets->where('status', 'maintenance')->count();
                $faulty = $assets->where('status', 'faulty')->count();
            @endphp
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Total Assets</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalAssets }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Operational</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $operational }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">In Maintenance</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $maintenance }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-500">Faulty</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $faulty }}</p>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Filter Assets</h3>
                </div>
                <button @click="showFilters = !showFilters" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <span class="mr-2" x-text="showFilters ? 'Hide filters' : 'Show filters'"></span>
                    <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            {{-- Filter form --}}
            <div x-show="showFilters" x-cloak class="px-6 pb-6">
                <form method="GET" action="{{ route('assets.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative rounded-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Code, name, location...">
                        </div>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Asset Type</label>
                        <select name="type" id="type" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="motor" @selected(request('type') === 'motor')>Motor</option>
                            <option value="transformer" @selected(request('type') === 'transformer')>Transformer</option>
                            <option value="mcc" @selected(request('type') === 'mcc')>MCC</option>
                            <option value="distribution_board" @selected(request('type') === 'distribution_board')>Distribution Board</option>
                            <option value="vfd" @selected(request('type') === 'vfd')>VFD</option>
                            <option value="switchgear" @selected(request('type') === 'switchgear')>Switchgear</option>
                            <option value="cable" @selected(request('type') === 'cable')>Cable</option>
                            <option value="other" @selected(request('type') === 'other')>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="operational" @selected(request('status') === 'operational')>Operational</option>
                            <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
                            <option value="faulty" @selected(request('status') === 'faulty')>Faulty</option>
                            <option value="decommissioned" @selected(request('status') === 'decommissioned')>Decommissioned</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-3">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('assets.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition text-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Assets Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Table header with result count --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Showing {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} assets</span>
                </div>
                @if(request()->hasAny(['search', 'type', 'status']))
                <span class="text-xs text-gray-500">Filtered results</span>
                @endif
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <span class="text-xs font-bold text-gray-600">{{ substr($asset->asset_code, 0, 3) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $asset->asset_code }}</span>
                                        <p class="text-xs text-gray-500">Installed: {{ \Carbon\Carbon::parse($asset->installation_date)->format('M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                        {{ ucfirst(str_replace('_', ' ', $asset->type)) }}
                                    </span>
                                    @if($asset->manufacturer)
                                    <span class="ml-2">{{ $asset->manufacturer }} {{ $asset->model }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $asset->location }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($asset->voltage_rating)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                        {{ $asset->voltage_rating }}V
                                    </span>
                                    @endif
                                    @if($asset->power_rating)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $asset->power_rating }}kW
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="View details">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    @can('update', $asset)
                                    <a href="{{ route('assets.edit', $asset) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" title="Edit asset">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    
                                    @can('delete', $asset)
                                    <button @click="confirmDelete({{ $asset->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Delete asset">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No assets found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding a new asset.</p>
                                @can('create', App\Models\Asset::class)
                                <div class="mt-6">
                                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add New Asset
                                    </a>
                                </div>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination :paginator="$assets" />
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-modal name="confirm-asset-deletion" :show="$errors->isNotEmpty()" focusable>
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-medium text-gray-900">Delete Asset</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Are you sure you want to delete this asset? All of its data will be permanently removed. This action cannot be undone.
                    </p>
                </div>
            </div>

            <form method="POST" :action="`/assets/${assetToDelete}`" class="mt-6">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Delete Asset
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection