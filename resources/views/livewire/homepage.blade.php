<div class="bg-[#F1F3F5] font-['Geist',sans-serif] min-h-screen pb-16">
    <!-- Hero Banner Promo -->
    <x-sections.hero-banner />

    <!-- Kategori Pilihan -->
    <x-sections.category-grid :categories="$categories" />

    <!-- Promo Hari Ini / Flash Sale -->
    <x-sections.flash-sale :products="$featuredProducts" />

    <!-- Produk Terbaru -->
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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

    <!-- Trust / Keunggulan (anchor "tentang") -->
    <section id="tentang" class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    class="bg-[#FFFFFF]/70 backdrop-blur border border-[#4A5568]/10 rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Jaminan Kualitas</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Produk alat listrik & kabel teruji standar
                        SNI, dipilih cermat dari distributor resmi.</p>
                </div>
                <div
                    class="bg-[#FFFFFF]/70 backdrop-blur border border-[#4A5568]/10 rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Pengiriman Cepat</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Pesanan dikirim cepat langsung ke alamat
                        Anda, untuk kebutuhan proyek maupun ritel.</p>
                </div>
                <div
                    class="bg-[#FFFFFF]/70 backdrop-blur border border-[#4A5568]/10 rounded-2xl p-6 flex flex-col items-center text-center">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-[#F1F3F5] text-[#0F1419] rounded-[10px] mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-[1.125rem] font-semibold text-[#0F1419] mb-2">Pembayaran Aman</h3>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568]">Transaksi diproses aman untuk B2B maupun
                        B2C, termasuk pembayaran via transfer bank.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 border-t border-[#4A5568]/5">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Contact Info -->
                <div>
                    <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419] mb-4">
                        Hubungi Kami
                    </h2>
                    <p class="text-[0.95rem] leading-[1.55] text-[#4A5568] mb-12 max-w-md">
                        Punya pertanyaan mengenai produk atau butuh bantuan instalasi? Tim ahli kami siap membantu Anda
                        mendapatkan solusi kelistrikan terbaik.
                    </p>

                    <div class="space-y-8">
                        <div class="flex items-center gap-6">
                            <div
                                class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center border border-[#4A5568]/10 text-[#0F1419] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase tracking-wider mb-1">
                                    Email</p>
                                <p class="text-[0.95rem] font-medium text-[#0F1419]">gmukaryatech@gmail.com</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div
                                class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center border border-[#4A5568]/10 text-[#0F1419] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase tracking-wider mb-1">
                                    Telepon</p>
                                <p class="text-[0.95rem] font-medium text-[#0F1419]">+62 812 3456 7890</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form Card -->
                <div class="bg-white rounded-2xl p-8 border border-[#4A5568]/10 shadow-sm">
                    <form action="#" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label
                                    class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Nama</label>
                                <input type="text" placeholder="Nama lengkap"
                                    class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Email</label>
                                <input type="email" placeholder="alamat@email.com"
                                    class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase">Pesan</label>
                            <textarea rows="4" placeholder="Apa yang bisa kami bantu?"
                                class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:outline-none focus:ring-1 focus:ring-[#0F1419] transition-all resize-none"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-[#2C5EF5] text-white py-3 px-6 rounded-[10px] text-[0.95rem] font-medium hover:opacity-90 transition-opacity">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
