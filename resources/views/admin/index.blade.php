<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Menu Item</title>
	@vite(['resources/css/app.css'])
	<style>
		/* minimal tweaks for table spacing */
		.table-row { border-top: 1px solid #e5e7eb; }
	</style>
</head>
<body class="bg-gray-100">
	<x-header />

	<main class="p-6 max-w-6xl mx-auto">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-3xl font-bold text-gray-900">Menu Items</h1>
			<div class="flex items-center gap-3">
				<form method="GET" action="{{ route('admin.menu-items.index') }}" class="w-80">
					<input id="search-items" name="q" value="{{ $q }}" class="search-box" placeholder="Search Items..." />
				</form>
				<a href="{{ route('admin.menu-items.create') }}" class="checkout-btn">New Item</a>
			</div>
		</div>

		<div class="mb-4 flex flex-wrap gap-2">
			<a href="{{ route('admin.menu-items.index') }}" class="px-3 py-2 rounded text-sm {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-white border' }}">All</a>
			@foreach($categories as $cat)
				<a href="{{ route('admin.menu-items.index', ['category' => $cat]) }}" class="px-3 py-2 rounded text-sm {{ request('category') === $cat ? 'bg-indigo-600 text-white' : 'bg-white border' }}">{{ $cat }}</a>
			@endforeach
		</div>

		<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
			<div class="p-4">
				<h2 class="text-lg font-semibold">Catalog List</h2>
			</div>
			<div class="overflow-x-auto">
				<table class="w-full text-sm border-t border-gray-200">
					<thead>
						<tr class="bg-gray-50">
							<th class="text-left p-3 w-40">Image</th>
							<th class="text-left p-3">Item Name & Desc</th>
							<th class="text-left p-3">Category</th>
							<th class="text-left p-3">Price</th>
							<th class="text-left p-3">Status</th>
							<th class="text-left p-3 w-40">Actions</th>
						</tr>
					</thead>
					<tbody>
					@forelse ($products as $product)
						@php
							$img = $product->image ? (\Illuminate\Support\Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('storage/'.$product->image)) : 'https://via.placeholder.com/80x80?text=No+Image';
						@endphp
						<tr class="table-row">
							<td class="p-3">
								<img src="{{ $img }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200" />
							</td>
						<td class="p-3">
							<div class="font-semibold text-gray-900">{{ $product->name }}</div>
							<div class="text-gray-500">ID: {{ $product->id }}</div>
						</td>
						<td class="p-3">
							<span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $product->category }}</span>
						</td>
						<td class="p-3">₱ {{ number_format($product->price, 2) }}</td>
							<td class="p-3">
								<span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">Available</span>
							</td>
							<td class="p-3">
				<a href="{{ route('admin.menu-items.edit', $product) }}" class="qty-btn inline-flex items-center justify-center mr-2">Edit</a>
				<form action="{{ route('admin.menu-items.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
					@csrf
					@method('DELETE')
					<button class="qty-btn text-red-600">Delete</button>
				</form>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="p-4 text-center text-gray-600">No items found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
			<div class="p-4">
				{{ $products->links() }}
			</div>
		</div>
	</main>
</body>
</html>
