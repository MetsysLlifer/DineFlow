@extends('layouts.base')

@section('title', 'Staff Login')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Staff Login</h1>
        @if($errors->any())
            <div class="mb-4 rounded bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input id="password" name="password" type="password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300" />
                    Remember me
                </label>
                <span class="text-gray-400">No signup available</span>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Login</button>
        </form>
        <p class="mt-6 text-xs text-gray-400">Authorized roles: admin, manager, cashier, kitchen, host, analyst.</p>
    </div>
</div>
@endsection
