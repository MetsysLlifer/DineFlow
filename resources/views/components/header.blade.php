@props(['user' => null])

@php
    $user = $user ?? auth()->user();
    $homeRoute = '/';
    
    if ($user) {
        if ($user->hasRole('admin', 'manager')) {
            $homeRoute = route('admin.dashboard');
        } elseif ($user->hasRole('cashier', 'kitchen')) {
            $homeRoute = route('cashier.dashboard');
        } elseif ($user->hasRole('host', 'analyst')) {
            $homeRoute = route('admin.dashboard');
        }
    }
@endphp

<header class="header" role="banner">
    <div class="flex justify-between items-center">
        <a href="{{ $homeRoute }}" class="logo hover:opacity-80 transition-opacity">
            DineFlow
        </a>
        
        <div class="flex items-center gap-4">
            @auth
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-600">{{ $user->name }}</span>
                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs uppercase tracking-wide">
                        {{ $user->role }}
                    </span>
                    
                    @if($user->hasRole('admin', 'manager', 'cashier', 'kitchen', 'host', 'analyst'))
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm underline">
                                Logout
                            </button>
                        </form>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</header>
