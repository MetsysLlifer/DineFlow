@extends('layouts.base')
@section('title','New Menu Item')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">New Menu Item</h1>
        <a href="{{ route('admin.menu-items.index') }}" class="text-sm text-gray-600 hover:underline">Back to Menu Items</a>
    </div>
    <div class="bg-white shadow rounded p-6">
        <form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Item Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="border rounded px-3 py-2 w-full" required>
                @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Price (₱)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="border rounded px-3 py-2 w-full" required>
                @error('price')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Category</label>
                <input type="text" name="category" value="{{ old('category', 'Main Course') }}" list="categories" class="border rounded px-3 py-2 w-full" required>
                <datalist id="categories">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                    <option value="Appetizers">
                    <option value="Main Course">
                    <option value="Desserts">
                    <option value="Beverages">
                </datalist>
                @error('category')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Image (optional)</label>
                <input type="file" name="image" accept="image/*" class="border rounded px-3 py-2 w-full">
                @error('image')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
                <a href="{{ route('admin.menu-items.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
