<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Ledger</h1>
                <p class="text-gray-600 mt-1">Complete inventory transaction history</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.stock-in.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors">
                    + Stock In
                </a>
                <a href="{{ route('inventory.stock-out.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                    + Stock Out
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by product SKU, name, or reference..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select wire:model.live="productFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Products</option>
                    @foreach($stockBalances as $item)
                        <option value="{{ $item['product_id'] }}">{{ $item['sku'] }} - {{ $item['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($search || $productFilter)
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-500">Active filters:</span>
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Search: {{ $search }}
                    </span>
                @endif
                @if($productFilter)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Product filtered
                    </span>
                @endif
                <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Clear all
                </button>
            </div>
        @endif
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Ledger Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock In</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Out</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ledger as $entry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $entry->createdAt?->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ $entry->productSku }}</p>
                            <p class="text-sm text-gray-500">{{ $entry->productName }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $entry->transactionType === 'stock_in' ? 'bg-green-100 text-green-800' : ($entry->transactionType === 'stock_out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $entry->transactionType)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600 font-medium">
                            {{ $entry->stockIn > 0 ? '+' . $entry->stockIn : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600 font-medium">
                            {{ $entry->stockOut > 0 ? '-' . $entry->stockOut : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            {{ $entry->balance }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $entry->reference ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No ledger entries found</h3>
                            <p class="mt-1 text-sm text-gray-500">Start by adding stock in or stock out transactions.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($ledger->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $ledger->links() }}
            </div>
        @endif
    </div>
</div>