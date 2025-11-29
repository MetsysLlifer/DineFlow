@extends('layouts.app')

@section('title', 'WebDev Grocery Shopping Cart')

@section('content')

{{-- Runtime config (routes + CSRF) for external JS --}}
<script>
	window.App = {
		routes: {
			productsGet: '{{ route("products.get") }}',
			cartGet: '{{ route("cart.get") }}',
			cartAdd: '{{ route("cart.add") }}',
			cartRemove: '{{ route("cart.remove") }}',
			submitOrder: '{{ route("orders.submit") }}'
		},
		csrfToken: '{{ csrf_token() }}'
	};
</script>

{{-- Load compiled app.js (preferred); fallback to Fetch.js if not available --}}
<script>
	(function () {
		var compiled = document.createElement('script');
		compiled.src = '{{ asset("js/app.js") }}';
		compiled.async = true;
		compiled.onload = function () { window._appCompiled = true; };
		compiled.onerror = function () { window._appCompiled = false; };
		document.head.appendChild(compiled);

		// After delay, if compiled didn't load, fallback to Fetch.js
		setTimeout(function () {
			if (window._appCompiled || typeof window.fetchProducts === 'function') {
				return; // compiled or external already loaded
			}
			// Load fallback
			var fallback = document.createElement('script');
			fallback.src = '{{ asset("js/Fetch.js") }}';
			fallback.async = true;
			document.head.appendChild(fallback);
		}, 500);
	})();
</script>
@endsection
