@extends('layouts.app')

@section('title', 'Reports - EMMS')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-600">Generate and analyze system reports for compliance and maintenance tracking</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        Read-Only Access
                    </span>
                </div>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Reports</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">3</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Assets</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Asset::count() }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Work Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\WorkOrder::count() }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Faults</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Fault::count() }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Categories --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Assets Report Card --}}
            <a href="{{ route('reports.assets') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all hover:border-blue-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-14 w-14 rounded-xl bg-blue-100 group-hover:bg-blue-200 transition-colors flex items-center justify-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 group-hover:bg-blue-200">
                        PDF / CSV
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Assets Report</h3>
                <p class="text-sm text-gray-600 mb-4">Complete inventory of all electrical assets including specifications, location, and current status.</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Last generated: {{ now()->subDays(2)->format('M d, Y') }}</span>
                    <span class="text-blue-600 group-hover:text-blue-700 font-medium inline-flex items-center">
                        Generate <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                {{-- Preview of report contents --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span> {{ \App\Models\Asset::where('status', 'operational')->count() }} Operational</span>
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1"></span> {{ \App\Models\Asset::where('status', 'maintenance')->count() }} Maintenance</span>
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1"></span> {{ \App\Models\Asset::where('status', 'faulty')->count() }} Faulty</span>
                    </div>
                </div>
            </a>

            {{-- Maintenance Report Card --}}
            <a href="{{ route('reports.maintenance') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all hover:border-green-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-14 w-14 rounded-xl bg-green-100 group-hover:bg-green-200 transition-colors flex items-center justify-center">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 group-hover:bg-green-200">
                        PDF / CSV
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Maintenance Report</h3>
                <p class="text-sm text-gray-600 mb-4">Track all maintenance activities, work order completion rates, and technician performance metrics.</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Last generated: {{ now()->subDays(5)->format('M d, Y') }}</span>
                    <span class="text-green-600 group-hover:text-green-700 font-medium inline-flex items-center">
                        Generate <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span> {{ \App\Models\WorkOrder::where('status', 'in_progress')->count() }} In Progress</span>
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span> {{ \App\Models\WorkOrder::where('status', 'completed')->count() }} Completed</span>
                    </div>
                </div>
            </a>

            {{-- Faults Report Card --}}
            <a href="{{ route('reports.faults') }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all hover:border-red-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-14 w-14 rounded-xl bg-red-100 group-hover:bg-red-200 transition-colors flex items-center justify-center">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 group-hover:bg-red-200">
                        PDF / CSV
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Faults Report</h3>
                <p class="text-sm text-gray-600 mb-4">Analyze fault patterns, root causes, resolution times, and equipment reliability metrics.</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Last generated: {{ now()->subDays(1)->format('M d, Y') }}</span>
                    <span class="text-red-600 group-hover:text-red-700 font-medium inline-flex items-center">
                        Generate <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1"></span> {{ \App\Models\Fault::where('severity', 'critical')->count() }} Critical</span>
                        <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1"></span> {{ \App\Models\Fault::where('status', 'reported')->count() }} Open</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Recent Reports Section --}}
        <div class="mt-12">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recently Generated Reports</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Asset Inventory Report</p>
                                            <p class="text-xs text-gray-500">Complete asset listing with specifications</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Assets</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(2)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">PDF</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">View</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Maintenance History Q1 2026</p>
                                            <p class="text-xs text-gray-500">All work orders and maintenance activities</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Maintenance</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(5)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">CSV</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">View</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-red-100 flex items-center justify-center mr-3">
                                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Fault Analysis Report</p>
                                            <p class="text-xs text-gray-500">Critical faults and resolution times</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Faults</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(1)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">PDF</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">View</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Export Options --}}
        <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Export All Reports</h3>
                        <p class="text-xs text-gray-500">Download all reports in a single archive</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export as CSV
                    </button>
                    <button class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm text-white hover:bg-blue-700 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection