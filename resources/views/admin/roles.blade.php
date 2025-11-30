@extends('layouts.base')
@section('title','Manage Roles')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Roles</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
    </div>
    @if(session('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded p-3">{{ session('status') }}</div>
    @endif
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-3 py-2">Name</th>
                    <th class="text-left px-3 py-2">Email</th>
                    <th class="text-left px-3 py-2">Role</th>
                    <th class="text-left px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="border-b">
                    <td class="px-3 py-2">{{ $user->name }}</td>
                    <td class="px-3 py-2 text-gray-600">{{ $user->email }}</td>
                    <td class="px-3 py-2">
                        <span class="inline-block px-2 py-1 rounded bg-gray-100 text-xs uppercase tracking-wide">{{ $user->role }}</span>
                    </td>
                    <td class="px-3 py-2">
                        <form method="POST" action="{{ route('admin.roles.update', $user) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="role" class="border rounded px-2 py-1 text-xs">
                                @foreach(['admin','manager','cashier','kitchen','host','analyst','customer'] as $r)
                                    <option value="{{ $r }}" @selected($user->role === $r)>{{ $r }}</option>
                                @endforeach
                            </select>
                            <button class="px-3 py-1 bg-indigo-600 text-white text-xs rounded">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
