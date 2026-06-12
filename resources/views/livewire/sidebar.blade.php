<div>
    @if($isOpen)
        <div class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" wire:click="toggleSidebar"></div>
    @endif

    <aside :class="{'translate-x-0': $wire.isOpen, '-translate-x-full': !$wire.isOpen}"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-800 to-slate-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto flex flex-col">

        <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">Warehouse</span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @foreach($menuItems as $item)
                @if(count($item['children']) > 0)
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors text-slate-300 hover:bg-slate-700/30 hover:text-white">
                            <span class="flex items-center gap-3">{{ $item['label'] }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="mt-1 ml-4 space-y-1">
                            @foreach($item['children'] as $child)
                                <a href="{{ route($child['route']) }}"
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                          {{ request()->routeIs($child['route'] . '*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-400 hover:bg-slate-700/30 hover:text-white' }}">
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-700/30 hover:text-white' }}">
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="p-4 border-t border-slate-700">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()?->name ?? 'User' }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>