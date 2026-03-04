@extends('layouts.app')

@section('title', 'Profile - EMMS')

@section('content')
<div class="py-8" x-data="{ activeTab: 'profile' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Manage your account settings and preferences
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if(Auth::user()->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                        <span class="w-2 h-2 rounded-full mr-2 @if(Auth::user()->is_active) bg-green-500 @else bg-gray-500 @endif"></span>
                        {{ Auth::user()->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ Auth::user()->role->name ?? 'No Role' }}
                    </span>
                </div>
            </div>
            
            {{-- Breadcrumbs --}}
            <nav class="flex mt-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    <li>
                        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    </li>
                    <li>
                        <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li class="text-gray-900 font-medium">Profile</li>
                </ol>
            </nav>
        </div>

        {{-- Profile Sections Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column - Profile Summary Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-center">
                        <div class="relative inline-block">
                            <div class="h-24 w-24 rounded-full bg-white/10 backdrop-blur mx-auto flex items-center justify-center border-4 border-white/30">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="h-20 w-20 rounded-full object-cover">
                                @else
                                    <span class="text-4xl font-bold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-white">{{ Auth::user()->name }}</h2>
                        <p class="text-sm text-blue-100">{{ Auth::user()->email }}</p>
                        <p class="mt-2 text-xs text-blue-200">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Employee ID</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->employee_id ?? 'Not set' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Department</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->department ?? 'Not set' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Phone</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->phone ?? 'Not set' }}</span>
                            </div>
                            <div class="border-t border-gray-200 my-4"></div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Email Verified</span>
                                @if(Auth::user()->email_verified_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="text-yellow-600 text-xs">Not verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Forms --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Update Profile Information --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
                        <p class="text-sm text-gray-600">Update your account's profile information and email address.</p>
                    </div>
                    <div class="px-6 py-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Update Password</h3>
                        <p class="text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                    <div class="px-6 py-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection