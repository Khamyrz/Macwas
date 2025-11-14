@php
// Set security headers for this page (required by securityheaders.com)
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');

if (!request()->cookies->has('macwas_cookie_policy') && !headers_sent()) {
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
  <title>{{ config('app.name','Macwas') }}</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6715976395005960"
  crossorigin="anonymous"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      color: #1f2937;
    }
  </style>
</head>

<body class="bg-white flex flex-col min-h-screen">

  <!-- HEADER -->
  <header class="fixed top-0 left-0 w-full bg-white border-b border-gray-200 z-20 shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3 sm:px-6">
      <div class="flex items-center space-x-2">
        <img src="{{ asset('images/logo.png') }}" alt="Macwas Logo" class="h-10 w-10">
        <span class="text-xl sm:text-2xl font-semibold text-blue-700 tracking-tight">Macwas</span>
      </div>

      <nav class="flex space-x-5 text-sm sm:text-base font-medium">
        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-700 transition-colors">Login</a>
        <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-700 transition-colors">Register</a>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <section class="flex flex-col justify-center items-center text-center flex-grow pt-32 pb-16 px-6 sm:pt-40 sm:pb-24">
    <h1 class="text-3xl sm:text-5xl font-bold text-gray-900 leading-snug mb-4 max-w-3xl">
      Smarter Water Management for Every Community
    </h1>
    <p class="text-base sm:text-lg text-gray-600 max-w-2xl mb-8">
      Empowering transparency and efficiency in water services — from billing to maintenance.
    </p>

    @auth
      <a href="{{ route('dashboard') }}"
         class="bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-800 transition">
        Go to Dashboard
      </a>
    @else
      <a href="{{ route('login') }}"
         class="bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-800 transition">
        Get Started
      </a>
    @endauth
  </section>

  <!-- FEATURES -->
  <section class="max-w-6xl mx-auto px-6 py-12 sm:py-20 grid grid-cols-1 sm:grid-cols-3 gap-8">
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition p-8 text-center">
      <div class="text-blue-700 text-4xl mb-4">💧</div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">Automated Billing</h3>
      <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
        Streamline billing with precise, real-time tracking and easy invoice payments.
      </p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition p-8 text-center">
      <div class="text-blue-700 text-4xl mb-4">📊</div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">Service Monitoring</h3>
      <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
        Gain insights into water usage, system performance, and maintenance needs.
      </p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition p-8 text-center">
      <div class="text-blue-700 text-4xl mb-4">👥</div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">Citizen Portal</h3>
      <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
        Give residents easy access to account management and transparent service updates.
      </p>
    </div>
  </section>

  <!-- COOKIE DISCLOSURE -->
  <section class="max-w-5xl mx-auto px-6 pb-16 sm:pb-20">
    @include('components.cookie-table')
  </section>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-500 mt-auto">
    © {{ date('Y') }} {{ config('app.name','Macwas') }} Water System — All rights reserved.
  </footer>

  @include('components.cookie-consent')

</body>
</html>
