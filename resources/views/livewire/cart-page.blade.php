<div class="bg-gray-50 py-8">
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-medium">Keranjang Belanja</li>
        </ol>
    </nav>

    <!-- Header -->
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

    @if(count($cart) > 0)
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @foreach($cart as $cartKey => $item)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex gap-4">
                            <!-- Product Image -->
                            <div class="shrink-0">
                                <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" 
                                                alt="{{ $item['name'] }}"
                                                class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-linear-to-br from-gray-300 to-gray-400">
                                            <span class="text-2xl text-gray-500">{{ substr($item['name'], 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">{{ $item['name'] }}</h3>
                                @if($item['variant_name'])
                                    <p class="text-sm text-gray-600 mb-2">{{ $item['variant_name'] }}</p>
                                @endif
                                <p class="text-lg font-bold {{ $isB2BApproved ? 'text-[#2C5EF5]' : 'text-blue-600' }}">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                                @if($isB2BApproved && isset($item['b2b_price']) && $item['b2b_price'])
                                    <span class="inline-block text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-medium mt-1">
                                        Harga Grosir
                                    </span>
                                @endif
                            </div>

                            <!-- Quantity & Actions -->
                            <div class="flex flex-col items-end justify-between">
                                <button wire:click="removeItem('{{ $cartKey }}')"
                                        class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-2">
                                    <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 rounded border border-gray-300 hover:bg-gray-100 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-12 text-center font-medium">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 rounded border border-gray-300 hover:bg-gray-100 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-lg font-bold text-gray-900">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Clear Cart -->
                <div class="flex justify-between items-center pt-4">
                    <button wire:click="clearCart"
                            wire:confirm="Apakah Anda yakin ingin mengosongkan keranjang belanja?"
                            class="text-red-600 hover:text-red-700 font-medium">
                        Hapus Keranjang
                    </button>
                    <a href="{{ route('products.index') }}" 
                        class="text-blue-600 hover:text-indigo-700 font-medium">
                        ← Lanjutkan Belanja
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ count($cart) }} items)</span>
                            <span class="font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Hitung saat checkout</span>
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-2xl font-bold text-blue-600">
                                Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @auth('customer')
                        @if($isB2BApproved)
                            <button wire:click="submitRfq"
                                class="block w-full bg-[#2C5EF5] text-white text-center py-3 px-6 rounded-lg hover:opacity-90 transition font-semibold">
                                Ajukan Penawaran
                            </button>
                            <p class="text-xs text-gray-500 text-center mt-2">
                                Permintaan penawaran akan direview oleh admin
                            </p>
                        @else
                            <a href="{{ route('checkout') }}"
                                class="block w-full bg-blue-600 text-white text-center py-3 px-6 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                Lanjut ke Checkout
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="block w-full bg-blue-600 text-white text-center py-3 px-6 rounded-lg hover:bg-indigo-700 transition font-semibold">
                            Login untuk melanjutkan
                        </a>
                        <p class="text-sm text-gray-600 text-center mt-3">
                            Atau <a href="{{ route('register') }}" class="text-blue-600 hover:text-indigo-700">Buat Akun Baru</a>
                        </p>
                    @endauth

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Checkout yang Aman</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Free Shipping on orders over Rp 100.000</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Retur Mudah</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="mx-auto w-24 h-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Anda kosong</h2>
            <p class="text-gray-600 mb-6">Tambahkan beberapa produk untuk memulai!</p>
            <a href="{{ route('products.index') }}" 
                class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                Mulai Berbelanja
            </a>
        </div>
    @endif
</div>
</div>