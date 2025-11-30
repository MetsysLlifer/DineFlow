@extends('layouts.base')

@section('title', 'Admin Dashboard')

@section('content')
<x-header />
<div class="min-h-screen p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="px-4 py-2 rounded bg-gray-800 text-white text-sm hover:bg-gray-700">Logout</button>
            </form>
        </div>

        <nav class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded bg-indigo-600 text-white">Dashboard</a>
            <a href="{{ route('admin.logs') }}" class="px-3 py-2 rounded bg-white shadow hover:bg-gray-50">Activity Logs</a>
            <a href="{{ route('admin.profit') }}" class="px-3 py-2 rounded bg-white shadow hover:bg-gray-50">Profit Graph</a>
            <a href="{{ route('admin.roles') }}" class="px-3 py-2 rounded bg-white shadow hover:bg-gray-50">Roles</a>
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded bg-white shadow hover:bg-gray-50">Users</a>
            <a href="{{ route('admin.menu-items.index') }}" class="px-3 py-2 rounded bg-white shadow hover:bg-gray-50">Menu Items</a>
        </nav>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-5 bg-white shadow rounded">
                <h2 class="font-semibold mb-2">Welcome</h2>
                <p class="text-sm text-gray-600">Logged in as <strong>{{ auth()->user()->name }}</strong><br>Role: <span class="uppercase text-xs tracking-wide">{{ auth()->user()->role }}</span></p>
            </div>
            <div class="p-5 bg-white shadow rounded">
                <h2 class="font-semibold mb-2">System Stats</h2>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>Users: <strong>{{ $usersCount }}</strong></li>
                    <li>Orders: <strong>{{ $ordersCount }}</strong></li>
                    <li>Gross Revenue: <strong>₱ {{ number_format($revenue,2) }}</strong></li>
                </ul>
            </div>
            <div class="p-5 bg-white shadow rounded">
                <h2 class="font-semibold mb-2">Shortcuts</h2>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.logs') }}">View Activity Logs</a></li>
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.profit') }}">View Profit Graph</a></li>
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.roles') }}">Manage Roles</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
