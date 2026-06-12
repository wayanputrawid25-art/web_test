<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Stock Report</h1>
        <p class="text-gray-600 mt-1">Overview of all stock levels and valuation</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Products</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalProducts) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Stock Quantity</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalStock) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Stock Value</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $lowStockCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Out of Stock Items</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $outOfStockCount }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Top 10 Products by Stock</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topProducts as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-right text-sm {{ $product->current_stock <= $product->min_stock ? 'font-medium text-red-600' : 'text-gray-900' }}">
                                {{ number_format($product->current_stock) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">{{ number_format($product->min_stock) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                Rp {{ number_format($product->current_stock * $product->cost_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>