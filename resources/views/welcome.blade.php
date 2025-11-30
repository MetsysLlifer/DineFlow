@extends('layouts.base')

@section('title', 'DineFlow')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6">
	<div class="max-w-2xl text-center">
		<h1 class="text-5xl font-extrabold tracking-tight mb-4">DineFlow</h1>
		<p class="text-lg md:text-xl text-gray-600 mb-8">
			Skip the wait with DineFlow — designed for mid‑high restaurants.
		</p>

		<a href="{{ route('menu.index') }}"
		   class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-white font-semibold shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
			Browse Menu
		</a>
	</div>
</div>
@endsection
