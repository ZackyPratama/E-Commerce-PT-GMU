<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'E-Commerce Store') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles

</head>

<body class="bg-[#F1F3F5] text-[#0F1419] font-['Geist',sans-serif] antialiased">
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Top Bar -->
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}"
                        class="text-2xl font-bold tracking-tight text-gray-900 hover:opacity-80 transition-opacity">
                        {{ config('app.name', 'E-Commerce') }}
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden flex-1 max-w-2xl mx-12 lg:block">
                    <livewire:search-bar />
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-6 shrink-0">
                    @auth('customer')
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="text-gray-500 hover:text-gray-900 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                                @click="open = false">
                                <a href="{{ route('customer.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                                <a href="{{ route('customer.orders') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pesanan</a>
                                @if(auth('customer')->user()->isB2BApproved())
                                    <a href="{{ route('customer.rfqs.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Permintaan Penawaran</a>
                                @endif
                                <a href="{{ route('customer.profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</a>
                                <hr class="my-1 border-gray-100">
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            Masuk
                        </a>
                    @endauth

                    <!-- Cart -->
                    <div class="text-gray-500 hover:text-gray-900 transition-colors">
                        <livewire:cart-icon />
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:block pb-4">
                <ul class="flex items-center gap-8 text-sm">
                    <li>
                        <a href="{{ route('home') }}"
                            class="text-gray-500 hover:text-gray-900 font-medium transition-colors">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                            class="text-gray-500 hover:text-gray-900 font-medium transition-colors">
                            Shop
                        </a>
                    </li>
                    @foreach(\App\Models\Category::active()->sorted()->limit(5)->get() as $category)
                        <li>
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                class="text-gray-500 hover:text-gray-900 transition-colors">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    @livewire('notifications')


    <!-- Footer -->
    <footer class="bg-[#0A2540] mt-[64px]">
        <div class="mx-auto max-w-7xl px-4 sm:px-[16px] lg:px-[32px] py-[48px]">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-[32px]">
                <div>
                    <h3 class="text-[1.125rem] font-semibold text-[#FFFFFF] tracking-[-0.02em] mb-[16px]">
                        {{ config('app.name') }}
                    </h3>
                    <p class="text-[#F1F3F5] text-[0.95rem] leading-[1.55]">Toko serba ada untuk produk berkualitas.</p>
                </div>
                <div>
                    <h4 class="text-[1.125rem] font-semibold text-[#FFFFFF] mb-[16px]">Tautan Cepat</h4>
                    <ul class="space-y-[8px]">
                        <li><a href="{{ route('products.index') }}"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Belanja</a>
                        </li>
                        <li><a href="#"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Tentang
                                Kami</a></li>
                        <li><a href="#"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Kontak</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[1.125rem] font-semibold text-[#FFFFFF] mb-[16px]">Layanan Pelanggan</h4>
                    <ul class="space-y-[8px]">
                        <li><a href="#"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Info
                                Pengiriman</a></li>
                        <li><a href="#"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Pengembalian</a>
                        </li>
                        <li><a href="#"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">FAQ</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[1.125rem] font-semibold text-[#FFFFFF] mb-[16px]">Akun</h4>
                    <ul class="space-y-[8px]">
                        <li><a href="{{ route('customer.dashboard') }}"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Dashboard</a>
                        </li>
                        <li><a href="{{ route('customer.orders') }}"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Pesanan</a>
                        </li>
                        @auth('customer')
                            @if(auth('customer')->user()->isB2BApproved())
                                <li><a href="{{ route('customer.rfqs.index') }}"
                                        class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Permintaan Penawaran</a>
                                </li>
                            @endif
                        @endauth
                        <li><a href="{{ route('customer.profile') }}"
                                class="text-[#F1F3F5] hover:text-[#FFFFFF] text-[0.95rem] transition-colors">Profil</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[#F1F3F5]/20 mt-[32px] pt-[32px] text-center text-[#F1F3F5] text-[0.95rem]">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @filamentScripts

</body>

</html>