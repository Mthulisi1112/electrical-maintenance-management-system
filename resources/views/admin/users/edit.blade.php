@extends('layouts.app')

@section('title', 'Edit User - Admin')

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-500 mb-2">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li><a href="{{ route('admin.users.index') }}" class="hover:text-gray-700">User Management</a></li>
                    <li><svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></li>
                    <li class="text-gray-900 font-medium">Edit User</li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-200 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700">
                <h2 class="text-lg font-semibold text-white">User Information</h2>
                <p class="text-sm text-purple-100">Edit user details and permissions</p>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Employee ID --}}
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Employee ID
                        </label>
                        <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               placeholder="EMP-12345">
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Department --}}
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                            Department
                        </label>
                        <select name="department" id="department"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select Department</option>
                            <option value="Electrical" @selected(old('department', $user->department) == 'Electrical')>Electrical</option>
                            <option value="Maintenance" @selected(old('department', $user->department) == 'Maintenance')>Maintenance</option>
                            <option value="Operations" @selected(old('department', $user->department) == 'Operations')>Operations</option>
                            <option value="Engineering" @selected(old('department', $user->department) == 'Engineering')>Engineering</option>
                            <option value="Facilities" @selected(old('department', $user->department) == 'Facilities')>Facilities</option>
                        </select>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" id="role_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                            Account Status
                        </label>
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_active" value="1" class="form-radio text-purple-600"
                                       @checked(old('is_active', $user->is_active))>
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_active" value="0" class="form-radio text-purple-600"
                                       @checked(old('is_active', !$user->is_active))>
                                <span class="ml-2 text-sm text-gray-700">Inactive</span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Verification --}}
                    <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Email Verification</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($user->email_verified_at)
                                        Verified on {{ $user->email_verified_at->format('M d, Y H:i') }}
                                    @else
                                        Not verified yet
                                    @endif
                                </p>
                            </div>
                            @if(!$user->email_verified_at)
                                <a href="{{ route('admin.users.verify-email', $user) }}" 
                                   class="text-sm text-purple-600 hover:text-purple-800 font-medium"
                                   onclick="event.preventDefault(); document.getElementById('verify-form').submit();">
                                    Send Verification Email
                                </a>
                                <form id="verify-form" action="{{ route('admin.users.verify-email', $user) }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-purple-600 border border-transparent rounded-lg text-sm text-white hover:bg-purple-700 transition">
                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection