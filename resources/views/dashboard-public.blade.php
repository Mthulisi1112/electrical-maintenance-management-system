@extends('layouts.app')

@section('title', 'Welcome - EMMS')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-800 opacity-90"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <div class="flex justify-center mb-8">
                <div class="h-24 w-24 rounded-full bg-white/10 backdrop-blur flex items-center justify-center border-4 border-white/30">
                    <span class="text-5xl font-bold text-white">⚡</span>
                </div>
            </div>
            <h1 class="text-5xl font-extrabold text-white sm:text-6xl">
                Electrical Maintenance
                <span class="block text-blue-200">Management System</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-blue-100">
                Streamline your electrical asset management, preventive maintenance, 
                and fault reporting with our comprehensive industrial solution.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-blue-700 bg-white hover:bg-gray-50 shadow-lg transition">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-base font-medium rounded-lg text-white hover:bg-white/10 transition">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Create Account
                </a>
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Why Choose EMMS?</h2>
            <p class="mt-4 text-lg text-gray-600">Comprehensive solution for industrial electrical maintenance</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Asset Management --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
                <div class="h-12 w-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Asset Management</h3>
                <p class="text-sm text-gray-600">Track all electrical equipment including motors, transformers, MCCs, and VFDs.</p>
            </div>

            {{-- Preventive Maintenance --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
                <div class="h-12 w-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Preventive Maintenance</h3>
                <p class="text-sm text-gray-600">Schedule and track maintenance tasks with automated work order generation.</p>
            </div>

            {{-- Fault Reporting --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
                <div class="h-12 w-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fault Reporting</h3>
                <p class="text-sm text-gray-600">Quickly report and track electrical faults with severity levels and images.</p>
            </div>

            {{-- Compliance Reports --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
                <div class="h-12 w-12 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Compliance Reports</h3>
                <p class="text-sm text-gray-600">Generate maintenance history and fault reports for compliance audits.</p>
            </div>
        </div>

        {{-- Stats Section --}}
        <div class="mt-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold">500+</div>
                    <div class="mt-2 text-blue-100">Assets Tracked</div>
                </div>
                <div>
                    <div class="text-4xl font-bold">10k+</div>
                    <div class="mt-2 text-blue-100">Work Orders Completed</div>
                </div>
                <div>
                    <div class="text-4xl font-bold">98%</div>
                    <div class="mt-2 text-blue-100">System Uptime</div>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div class="mt-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to get started?</h2>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-lg transition">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                    Create Account
                </a>
            </div>
        </div>
    </div>
</div>
@endsection