<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create User</h1>
                <p class="text-gray-600 mt-1">Add a new user account</p>
            </div>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">User Information</h2>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               wire:model="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               wire:model="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               wire:model="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Roles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                        <div class="space-y-2">
                            @foreach($roles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedRoles"
                                           value="{{ $role }}"
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ ucfirst($role) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end pt-4">
                        <button type="button" wire:click="cancel"
                                class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 mr-2">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Save User</span>
                            <span wire:loading wire:target="save">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Permissions</h3>
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-medium text-red-600">SuperAdmin</h4>
                        <p class="text-sm text-gray-500 mt-1">Full access to all features</p>
                    </div>
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-medium text-blue-600">WarehouseAdmin</h4>
                        <p class="text-sm text-gray-500 mt-1">Warehouse operations, stock, approval, reports</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600">Operator</h4>
                        <p class="text-sm text-gray-500 mt-1">Task management, stock opname, data entry</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>