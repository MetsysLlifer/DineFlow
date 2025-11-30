@extends('layouts.base')
@section('title','New User')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-3xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create User</h1>
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">Back to Users</a>
  </div>
  <div class="bg-white shadow rounded p-6">
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium">Name</label>
        <input name="name" value="{{ old('name') }}" class="border rounded px-3 py-2 w-full" required />
        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="border rounded px-3 py-2 w-full" required />
        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="border rounded px-3 py-2 w-full" required />
        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium">Role</label>
        <select name="role" class="border rounded px-3 py-2 w-full" required>
          @foreach(['admin','manager','cashier','kitchen','host','analyst','customer'] as $r)
            <option value="{{ $r }}" @selected(old('role')===$r)>{{ $r }}</option>
          @endforeach
        </select>
        @error('role')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection