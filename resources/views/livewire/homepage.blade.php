<div class="bg-[#F1F3F5] font-['Geist',sans-serif] min-h-screen pb-16">
    <!-- Hero Section -->
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-[3.75rem] font-semibold tracking-[-0.03em] text-[#0F1419] mb-4 leading-tight">
                    Selamat Datang di {{ config('app.name') }}
                </h1>
                <p class="text-[0.95rem] leading-[1.55] text-[#4A5568] mb-8">
                    Telusuri koleksi produk kami yang berkualitas dan temukan penawaran terbaik untuk kebutuhan Anda!
                </p>
                <a href="{{ route('products.index') }}"
                    class="inline-block bg-[#2C5EF5] text-[#FFFFFF] px-20 py-3 rounded-[10px] text-[0.95rem] font-medium hover:opacity-90 transition-opacity">
                    Belanja Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419] mb-8">Kategori Produk</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                        class="group block bg-[#FFFFFF] text-[#0F1419] rounded-2xl p-6 hover:shadow-sm transition-shadow">
                        <div class="aspect-square rounded-[10px] overflow-hidden bg-[#F1F3F5] mb-4">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                    class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#F1F3F5] text-[#0F1419]">
                                    <span class="text-[2.25rem] font-semibold">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-center font-medium text-[0.95rem] text-[#0F1419]">
                            {{ $category->name }}
                        </h3>
                        <p
                            class="text-center text-[0.75rem] font-['Geist_Mono',monospace] tracking-normal text-[#4A5568] mt-2">
                            {{ $category->products_count }} Produk
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="bg-[#FFFFFF] rounded-2xl p-8 md:p-16">
                <div class="grid md:grid-cols-2 gap-8 lg:gap-16 items-center">
                    <div class="order-2 md:order-1">
                        <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419] mb-4">Tentang Kami
                        </h2>
                        <p class="text-[0.95rem] leading-[1.55] text-[#4A5568] mb-4">
                            Selamat datang di {{ config('app.name') }}. Kami berdedikasi untuk menyediakan produk-produk
                            berkualitas terbaik yang dipilih dengan cermat untuk memenuhi kebutuhan dan gaya hidup Anda.
                        </p>
                        <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">
                            Misi kami adalah memberikan pengalaman belanja online yang luar biasa dengan layanan
                            pelanggan
                            yang tak tertandingi dan pengiriman yang cepat. Kami percaya pada kualitas, kepercayaan, dan
                            kepuasan pelanggan.
                        </p>
                        <a href="#"
                            class="inline-block mt-8 bg-[#0F1419] text-white px-6 py-3 rounded-[10px] text-[0.95rem] font-medium hover:bg-opacity-90 transition-opacity">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                    <div class="order-1 md:order-2">
                        <img src={{ asset('images/about-us.webp') }}
                            alt="Tim kami sedang bekerja"
                            class="rounded-2xl w-full h-full object-cover aspect-square md:aspect-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">Produk Unggulan</h2>
                <a href="{{ route('products.index', ['featured' => 1]) }}"
                    class="text-[#4A5568] hover:text-[#0F1419] text-[0.95rem] transition-colors">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $product)
                    <livewire:product-card :product="$product" :key="$product->id" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">Produk Terbaru</h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="text-[#4A5568] hover:text-[#0F1419] text-[0.95rem] transition-colors">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($newArrivals as $product)
                    <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#FFFFFF] text-[#0F1419] rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Jaminan Kualitas</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Semua produk dipilih dengan cermat dan diuji
                        kualitasnya</p>
                </div>
                <div class="bg-[#FFFFFF] text-[#0F1419] rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Pengiriman Cepat</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Pengiriman cepat langsung ke alamat Anda</p>
                </div>
                <div class="bg-[#FFFFFF] text-[#0F1419] rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Pembayaran Aman</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Informasi pembayaran Anda aman dengan kami
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 border-t border-[#4A5568]/5">
        <div class="mx-auto max-w-7xl px-4 sm:px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Contact Info -->
                <div>
                    <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419] mb-4">
                        Hubungi Kami
                    </h2>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568] mb-12 max-w-md">
                        Punya pertanyaan mengenai produk atau butuh bantuan instalasi? Tim ahli kami siap membantu Anda mendapatkan solusi kelistrikan terbaik.
                    </p>

                    <div class="space-y-8">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center border border-[#4A5568]/10 text-[#0F1419] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase tracking-wider mb-1">Email</p>
                                <p class="text-[0.95rem] font-medium text-[#0F1419]">hello@pa-ecommerce.com</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center border border-[#4A5568]/10 text-[#0F1419] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase tracking-wider mb-1">Telepon</p>
                                <p class="text-[0.95rem] font-medium text-[#0F1419]">+62 812 3456 7890</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form Card -->
                <div class="bg-white rounded-[16px] p-8 border border-[#4A5568]/10 shadow-sm">
                    <form action="#" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Nama</label>
                                <input type="text" placeholder="Nama lengkap" class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Email</label>
                                <input type="email" placeholder="alamat@email.com" class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Pesan</label>
                            <textarea rows="4" placeholder="Apa yang bisa kami bantu?" class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#0F1419] text-white py-3 px-6 rounded-[10px] text-[0.95rem] font-medium hover:opacity-90 transition-opacity">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>