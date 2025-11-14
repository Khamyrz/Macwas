@php
    $shouldSetPolicyCookie = !request()->cookies->has('macwas_cookie_policy');
    if ($shouldSetPolicyCookie && !headers_sent()) {
        setcookie('macwas_cookie_policy', 'pending', [
            'expires' => time() + 31536000,
            'path' => '/',
            'secure' => request()->isSecure(),
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow" />

    <title>{{ config('app.name', 'Macwas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Sweet Alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-sans antialiased">
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-2">
            <div class="flex flex-col items-center justify-center">
                <h1 class="text-lg font-bold text-blue-600 mb-1">Macwas Water Installment & Billing</h1>
                <!-- Logo image - place your logo.png in public/images/ -->
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Water Management Logo" 
                     class="w-auto object-contain"
                     style="height: 80px;"
                     onerror="this.style.display='none'; console.error('Logo image not found at: public/images/logo.png')">
            </div>
        </div>
    </header>

    @yield('content')
    
    <!-- Cookie Consent -->
    @includeIf('components.cookie-consent')
    
    <!-- Sweet Alert Messages -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('message'))
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: '{{ session('message') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if(isset($errors) && $errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</body>
</html>