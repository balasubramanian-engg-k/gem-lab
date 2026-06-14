<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>GHC GEM STONE HALLMARK CENTRE</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind (CDN for quick use) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>[x-cloak]{display:none!important}</style>
  <style>
        .modal {
            display: none;
        }
        .modal.active {
            display: flex;
        }
        .brown-gradient {
            background: linear-gradient(135deg, #8B7355 0%, #A0926B 50%, #8B7355 100%);
        }
        .ghc-logo {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .watermark {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" opacity="0.03"><text x="100" y="80" text-anchor="middle" dominant-baseline="middle" font-size="40" font-weight="bold" fill="gray">GHC</text><text x="100" y="120" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="gray">GEMSTONE HALLMARK</text><text x="100" y="140" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="gray">CENTRE</text></svg>');
            background-repeat: repeat;
            background-size: 200px 200px;
        }
        .qr-pattern {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="0" y="0" width="10" height="10" fill="black"/><rect x="20" y="0" width="10" height="10" fill="black"/><rect x="40" y="0" width="10" height="10" fill="black"/><rect x="60" y="0" width="10" height="10" fill="black"/><rect x="80" y="0" width="10" height="10" fill="black"/><rect x="0" y="20" width="10" height="10" fill="black"/><rect x="80" y="20" width="10" height="10" fill="black"/><rect x="0" y="40" width="10" height="10" fill="black"/><rect x="20" y="40" width="10" height="10" fill="black"/><rect x="40" y="40" width="10" height="10" fill="black"/><rect x="60" y="40" width="10" height="10" fill="black"/><rect x="80" y="40" width="10" height="10" fill="black"/><rect x="0" y="60" width="10" height="10" fill="black"/><rect x="80" y="60" width="10" height="10" fill="black"/><rect x="0" y="80" width="10" height="10" fill="black"/><rect x="20" y="80" width="10" height="10" fill="black"/><rect x="40" y="80" width="10" height="10" fill="black"/><rect x="60" y="80" width="10" height="10" fill="black"/><rect x="80" y="80" width="10" height="10" fill="black"/></svg>');
            background-size: cover;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-100">

  <!-- HEADER -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between py-3">

        <!-- Left: Logo -->
        <div class="flex items-center space-x-3">
          <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600"><img class="shadow-md max-h-10" src="{{ asset('images/id_logo.png') }}"></a>
        </div>

        <!-- Middle: Tabs (desktop) -->
        <nav class="hidden md:block">
          <ul class="flex space-x-1 bg-white rounded-md overflow-hidden border">
            <li>
              <a href="{{ route('dashboard') }}"
                 class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('dashboard') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Dashboard
              </a>
            </li>
            <li>
              <a href="{{ route('gems.index') }}"
                 class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('gems.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Gems
              </a>
            </li>
            <li>
              <a href="{{ route('invoices.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('invoices.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Invoice
              </a>
            </li>
            <li>
              <a href="{{ route('making-mc.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('making-mc.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                        Labour Report
              </a>
            </li>
            @if(Auth::check() && Auth::user()->is_admin)
            <li>
              <a href="{{ route('stones.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('stones.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Stones
              </a>
            </li>
            <li>
              <a href="{{ route('product-types.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('product-types.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Product Type
              </a>
            </li>
            <li>
              <a href="{{ route('report.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('report.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Report
              </a>
            </li>
            <li>
              <a href="{{ route('stock.index') }}" class="inline-block px-4 py-2 text-sm font-medium
                        {{ request()->routeIs('stock.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}">
                Stock
              </a>
            </li>
            @endif
          </ul>
        </nav>

        <!-- Right: user + mobile menu button -->
        <div class="flex items-center space-x-4">

          <!-- Desktop user menu (Alpine) -->
          <div x-data="{ open: false }" class="relative inline-block">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-3 py-1 rounded hover:bg-gray-50 focus:outline-none">
              <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <div x-show="open" x-cloak @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-44 bg-white border rounded shadow-lg z-50">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Logout
                </button>
              </form>
            </div>
          </div>

          <!-- Mobile menu toggle (shows tabs) -->
          <div class="md:hidden" x-data="{ open:false }">
            <button @click="open = !open" class="p-2 rounded hover:bg-gray-100 focus:outline-none">
              <svg x-show="!open" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
              </svg>
              <svg x-show="open" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>

              <!-- Mobile dropdown tabs -->
              <div x-show="open" x-cloak @click.away="open = false" x-transition
                   class="absolute right-4 mt-2 w-44 bg-white border rounded shadow-lg z-40">
                <a href="{{ route('dashboard') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('dashboard') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Dashboard
                </a>
                <a href="{{ route('gems.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('gems.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Gems
                </a>
                <a href="{{ route('invoices.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('invoices.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Invoice
                </a>
                @if(Auth::check() && Auth::user()->is_admin)
                <a href="{{ route('making-mc.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('making-mc.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Making MC
                </a>
                <a href="{{ route('stones.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('stones.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Stones
                </a>
                <a href="{{ route('product-types.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('product-types.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Product Type
                </a>
                <a href="{{ route('report.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('report.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Report
                </a>
                <a href="{{ route('stock.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('stock.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Stock
                </a>
                @endif
                <a href="{{ route('users.index') }}"
                   class="block px-4 py-2 text-sm {{ request()->routeIs('users.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                  Users
                </a>
              </div>
            </button>
          </div>

        </div>
      </div>
    </div>
  </header>

  <!-- MAIN -->
  <main class="flex-1 container mx-auto px-4 ">
    @yield('content')
  </main>

  <!-- FOOTER (sticky bottom) -->
  <footer class="bg-white border-t">
    <div class="container mx-auto px-4 py-4 text-sm text-center text-gray-600">
      &copy; {{ date('Y') }} GHC - Gemstone Hallmark Centre. All rights reserved.
    </div>
  </footer>

  <!-- Alpine (CDN) -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
