<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    required>
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    required>
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Employee ID --}}
            <div class="space-y-2">
                <label for="employee_id" class="block text-sm font-medium text-gray-700">
                    Employee ID
                </label>
                <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="EMP-12345">
                @error('employee_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Department --}}
            <div class="space-y-2">
                <label for="department" class="block text-sm font-medium text-gray-700">
                    Department
                </label>
                <select name="department" id="department"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Department</option>
                    <option value="Electrical" @selected(old('department', $user->department) == 'Electrical')>Electrical</option>
                    <option value="Maintenance" @selected(old('department', $user->department) == 'Maintenance')>Maintenance</option>
                    <option value="Operations" @selected(old('department', $user->department) == 'Operations')>Operations</option>
                    <option value="Engineering" @selected(old('department', $user->department) == 'Engineering')>Engineering</option>
                    <option value="Facilities" @selected(old('department', $user->department) == 'Facilities')>Facilities</option>
                </select>
                @error('department')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="space-y-2 md:col-span-2">
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    Phone Number
                </label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="+1 (555) 123-4567">
                @error('phone')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role (READ-ONLY - displayed but not editable) --}}
            <div class="space-y-2">
                <label for="role_display" class="block text-sm font-medium text-gray-700">
                    Role
                </label>
                <input type="text" id="role_display" value="{{ $user->role->name ?? 'Not assigned' }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
                    readonly disabled>
                <p class="text-xs text-gray-500">Role can only be changed by an administrator.</p>
            </div>

            {{-- Status (READ-ONLY) --}}
            <div class="space-y-2">
                <label for="status_display" class="block text-sm font-medium text-gray-700">
                    Account Status
                </label>
                <input type="text" id="status_display" 
                    value="{{ $user->is_active ? 'Active' : 'Inactive' }}"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
                    readonly disabled>
            </div>
        </div>

        {{-- Email Verification Status --}}
        @if (!$user->hasVerifiedEmail())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Your email address is unverified.
                        <button form="send-verification" class="underline text-sm text-yellow-700 hover:text-yellow-600">
                            Click here to re-send the verification email.
                        </button>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Saved!
                </p>
            @endif

            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Save Changes
            </button>
        </div>
    </form>
</section>