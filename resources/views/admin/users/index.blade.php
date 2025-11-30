@extends('layouts.base')
@section('title','Users')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Users</h1>
    <div class="flex items-center gap-3">
      <form method="GET" action="{{ route('admin.users.index') }}" class="w-80">
        <input name="q" value="{{ $q }}" class="border rounded px-3 py-2 w-full" placeholder="Search name/email" />
      </form>
      <a href="{{ route('admin.users.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">New User</a>
    </div>
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
          <th class="text-left px-3 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
          <tr class="border-b">
            <td class="px-3 py-2">{{ $user->name }}</td>
            <td class="px-3 py-2 text-gray-600">{{ $user->email }}</td>
            <td class="px-3 py-2">
              <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs uppercase tracking-wide">{{ $user->role }}</span>
            </td>
            <td class="px-3 py-2">
              <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 border rounded text-xs">Edit</a>
              @if(auth()->id() !== $user->id)
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <button class="px-3 py-1 bg-red-600 text-white rounded text-xs">Delete</button>
              </form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection