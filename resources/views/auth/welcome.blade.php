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
        @php
          $logoPath = file_exists(public_path('images/logo.png')) ? asset('images/logo.png') : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iIzE2NjNlYSIvPgo8cGF0aCBkPSJNMTIgMTJIMjhWMjhIMTJWMjhaIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPgo8L3N2Zz4K';
        @endphp
        <img src="{{ $logoPath }}" alt="Macwas Logo" class="h-10 w-10">
        <span class="text-xl sm:text-2xl font-semibold text-blue-700 tracking-tight">Macwas</span>
      </div>

      <nav class="flex space-x-5 text-sm sm:text-base font-medium">
        <a href="{{ url('/login') }}" class="text-gray-700 hover:text-blue-700 transition-colors">Login</a>
        <a href="{{ url('/register') }}" class="text-gray-700 hover:text-blue-700 transition-colors">Register</a>
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

    <a href="{{ url('/login') }}"
       class="bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-800 transition">
      Get Started
    </a>
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
    @includeIf('components.cookie-table', ['cookies' => $cookieItems ?? null])
  </section>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-500 mt-auto">
    © {{ date('Y') }} {{ config('app.name','Macwas') }} Water System — All rights reserved.
  </footer>

  @includeIf('components.cookie-consent')

</body>
</html>
