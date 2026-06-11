<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Roles</h1>
                <p class="text-gray-600 mt-1">Assign roles and permissions to {{ $user->name }}</p>
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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Role Assignment -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Current Roles</h2>
                </div>
                <div class="p-6">
                    @if(count($user->roles) > 0)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $role === 'SuperAdmin' ? 'bg-red-100 text-red-800' : ($role === 'WarehouseAdmin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($role) }}
                                    <button type="button"
                                            wire:click="removeRole('{{ $role }}')"
                                            class="ml-2 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-6">No roles assigned</p>
                    @endif

                    <!-- Add Role -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Add Role</h3>
                        <div class="flex gap-2">
                            <select wire:model.live="selectedRole"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a role...</option>
                                @foreach($roles as $role)
                                    @if(!in_array($role, $user->roles))
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="button"
                                    wire:click="assignRole($wire.selectedRole)"
                                    @if(!$selectedRole) disabled @endif
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                                Add Role
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Overview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Permissions by Role</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">{{ ucfirst($role) }}</h4>
                                @php
                                    $roleModel = \Spatie\Permission\Models\Role::findByName($role);
                                    $permissions = $roleModel ? $roleModel->permissions->pluck('name')->toArray() : [];
                                @endphp
                                <div class="space-y-1">
                                    @forelse($permissions as $permission)
                                        <p class="text-xs text-gray-600">{{ $permission }}</p>
                                    @empty
                                        <p class="text-xs text-gray-400">No permissions</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-indigo-600">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                            {{ $user->status?->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status?->label() ?? 'Unknown' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">User ID</p>
                        <p class="font-medium">#{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-medium">{{ $user->createdAt?->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Role Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Descriptions</h3>
                <div class="space-y-3">
                    <div class="border-b border-gray-200 pb-2">
                        <p class="font-medium text-red-600">SuperAdmin</p>
                        <p class="text-sm text-gray-500">Full system access</p>
                    </div>
                    <div class="border-b border-gray-200 pb-2">
                        <p class="font-medium text-blue-600">WarehouseAdmin</p>
                        <p class="text-sm text-gray-500">Warehouse operations, stock, approval, reports</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Operator</p>
                        <p class="text-sm text-gray-500">Task management, stock opname, data entry</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>