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

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @filamentStyles

</head>

<body class="bg-[#F1F3F5] text-[#0F1419] font-['Geist',sans-serif] antialiased">
    <!-- Header -->
    <x-header />

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
                        <li><a href="{{ route('home') }}#tentang"
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global Livewire SweetAlert listener -->
    <script>
        Livewire.on('swal', (...params) => {
            const data = Array.isArray(params[0]) ? params[0][0] : params[0];
            Swal.fire(data);
        });
        Livewire.on('error', (...params) => {
            const data = Array.isArray(params[0]) ? params[0][0] : params[0];
            const message = data?.message || (typeof data === 'string' ? data : null) || 'Terjadi kesalahan';
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message,
                confirmButtonColor: '#dc2626'
            });
        });
        Livewire.on('success', (...params) => {
            const data = Array.isArray(params[0]) ? params[0][0] : params[0];
            const message = data?.message || (typeof data === 'string' ? data : '') || '';
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                confirmButtonColor: '#2563eb'
            });
        });
    </script>

</body>

</html>