<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>DineFlow</title>

	<!-- Tailwind (CDN for quick styling) -->
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

	<!-- App styles (optional) -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
	<div class="flex min-h-screen items-center justify-center px-6">
		<div class="max-w-2xl text-center">
			<h1 class="text-5xl font-extrabold tracking-tight mb-4">DineFlow</h1>
			<p class="text-lg md:text-xl text-gray-600 mb-8">
				Skip the wait with DineFlow — designed for mid‑high restaurants.
			</p>

			<a href="{{ route('products.index') }}"
			   class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-white font-semibold shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
				Browse Menu
			</a>
		</div>
	</div>
</body>
</html>
