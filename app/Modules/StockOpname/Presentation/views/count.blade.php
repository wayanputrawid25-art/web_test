<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Count Items</h1>
                <p class="text-gray-600 mt-1">{{ $session->code }} - {{ $session->name }}</p>
            </div>
            <a href="{{ route('stock_opnames.show', $session->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Progress -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Counting Progress</span>
            <span class="text-sm text-gray-500">{{ $getCountedProgress()['percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $getCountedProgress()['percentage'] }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm text-gray-500">
            <span>{{ $getCountedProgress()['counted'] }} counted</span>
            <span>{{ $getCountedProgress()['total'] }} total</span>
        </div>
    </div>

    <!-- Messages -->
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

    <!-- Count Form -->
    <form wire:submit.prevent="saveDraft" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Items to Count</h2>
            <div class="flex gap-2">
                <button type="button" wire:click="saveDraft"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    Save Draft
                </button>
                <button type="button" wire:click="saveAndSubmit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    Save & Submit
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">System Qty</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Counted Qty</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Variance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $item)
                        <tr class="{{ isset($countValues[$item['id']]) && $countValues[$item['id']] !== '' && $item['system_quantity'] != $countValues[$item['id']] ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $item['product_sku'] }}</p>
                                <p class="text-sm text-gray-500">{{ $item['product_name'] }}</p>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ number_format($item['system_quantity'], 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       wire:model.live="countValues.{{ $item['id'] }}"
                                       placeholder="Enter count"
                                       class="w-32 px-3 py-2 mx-auto block border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center
                                              {{ isset($countValues[$item['id']]) && $countValues[$item['id']] !== '' ? 'bg-green-50' : 'bg-white' }}">
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $counted = isset($countValues[$item['id']]) && $countValues[$item['id']] !== '' ? (float)$countValues[$item['id']] : null;
                                    $variance = $counted !== null ? round($counted - $item['system_quantity'], 2) : null;
                                @endphp
                                @if($variance !== null)
                                    <span class="font-medium {{ $variance > 0 ? 'text-green-600' : ($variance < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <input type="text"
                                       wire:model="notesValues.{{ $item['id'] }}"
                                       placeholder="Optional notes..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(empty($items))
            <div class="p-8 text-center text-gray-500">
                No items to count. Add products to this session first.
            </div>
        @endif
    </form>
</div>