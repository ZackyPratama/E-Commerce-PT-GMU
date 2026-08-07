<header class="bg-[#FFFFFF]/80 backdrop-blur-md border-b border-[#4A5568]/10 sticky top-0 z-50 transition-all duration-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Top Bar -->
        <div class="flex items-center justify-between gap-4 h-20">
            <!-- Logo -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}"
                    class="text-2xl font-bold tracking-tight text-[#0F1419] hover:opacity-80 transition-opacity">
                    {{ config('app.name', 'E-Commerce') }}
                </a>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden flex-1 max-w-2xl lg:block">
                <livewire:search-bar />
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-6 shrink-0">
                @auth('customer')
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="text-[#4A5568] hover:text-[#0F1419] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 mt-2 w-56 bg-[#FFFFFF] rounded-[10px] shadow-lg border border-[#4A5568]/10 py-2 z-50"
                            @click="open = false">
                            <a href="{{ route('customer.dashboard') }}"
                                class="block px-4 py-2 text-sm text-[#0F1419] hover:bg-[#F1F3F5]">Dashboard</a>
                            <a href="{{ route('customer.orders') }}"
                                class="block px-4 py-2 text-sm text-[#0F1419] hover:bg-[#F1F3F5]">Pesanan</a>
                            @if(auth('customer')->user()->isB2BApproved())
                                <a href="{{ route('customer.rfqs.index') }}"
                                    class="block px-4 py-2 text-sm text-[#0F1419] hover:bg-[#F1F3F5]">Permintaan Penawaran</a>
                            @endif
                            <a href="{{ route('customer.profile') }}"
                                class="block px-4 py-2 text-sm text-[#0F1419] hover:bg-[#F1F3F5]">Profil</a>
                            <hr class="my-1 border-[#4A5568]/10">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-[#4A5568] hover:text-[#0F1419] transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="text-sm font-medium bg-[#2C5EF5] text-[#FFFFFF] px-4 py-2 rounded-[10px] hover:opacity-90 transition-opacity">
                            Daftar
                        </a>
                    </div>
                @endauth

                <!-- Cart -->
                <div class="text-[#4A5568] hover:text-[#0F1419] transition-colors">
                    <livewire:cart-icon />
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:block pb-4">
            <ul class="flex items-center gap-8 text-sm">
                <li>
                    <a href="{{ route('home') }}"
                        class="text-[#4A5568] hover:text-[#0F1419] font-medium transition-colors">
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}"
                        class="text-[#4A5568] hover:text-[#0F1419] font-medium transition-colors">
                        Katalog
                    </a>
                </li>
                @foreach(\App\Models\Category::active()->sorted()->limit(5)->get() as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="text-[#4A5568] hover:text-[#0F1419] transition-colors">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
