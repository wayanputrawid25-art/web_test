<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
                <p class="text-gray-600 mt-1">View product information</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.edit', $product->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Product Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Product Information</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">SKU</label>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-gray-100 text-gray-900 font-mono text-sm rounded">
                            {{ $product->sku }}
                        </span>
                        <button class="text-gray-400 hover:text-gray-600" title="Copy SKU">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $product->status->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->status->label() }}
                    </span>
                </div>

                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Product Name</label>
                    <p class="text-lg font-medium text-gray-900">{{ $product->name }}</p>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                    <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded">
                        {{ $product->category }}
                    </span>
                </div>

                <!-- Created At -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-gray-900">{{ $product->createdAt?->format('F d, Y H:i:s') ?? '-' }}</p>
                </div>

                <!-- Updated At -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-gray-900">{{ $product->updatedAt?->format('F d, Y H:i:s') ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>