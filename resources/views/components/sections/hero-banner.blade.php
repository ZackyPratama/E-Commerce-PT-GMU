@props(['banners' => []])

@php
    // Struktur statis siap integrasi data backend.
    // Setiap item: [title, subtitle, chip, cta_text, cta_url, color, height]
    $banners = $banners ?: [
        [
            'title' => 'Kabel NYM Lengkap untuk Instalasi Listrik',
            'subtitle' => 'Kabel tembaga murni standar SNI untuk rumah, gedung, dan industri. Harga terjangkau, stok melimpah.',
            'chip_label' => 'Promo Hari Ini',
            'cta_text' => 'Belanja Sekarang',
            'cta_url' => route('products.index'),
            'color' => 'bg-[#0F1419]',
            'layout' => 'large',
        ],
        [
            'title' => 'Panel MCB & Box Distribusi',
            'subtitle' => 'Perlengkapan panel listrik lengkap untuk proyek Anda.',
            'chip_label' => null,
            'cta_text' => 'Lihat Katalog',
            'cta_url' => route('products.index'),
            'color' => 'bg-[#2C5EF5]',
            'layout' => 'small',
        ],
    ];
@endphp

<section class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($banners as $banner)
                @php
                    $isLarge = ($banner['layout'] ?? '') === 'large';
                @endphp
                <a href="{{ $banner['cta_url'] }}"
                    class="{{ $banner['color'] }} relative overflow-hidden rounded-2xl p-8 {{ $isLarge ? 'lg:col-span-2 lg:min-h-[280px] lg:flex lg:flex-col lg:justify-end' : 'lg:min-h-[132px]' }} group transition-shadow hover:shadow-lg">
                    <!-- Glass overlay card -->
                    <div class="absolute inset-0">
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div
                            class="absolute inset-x-0 bottom-0 bg-white/40 backdrop-blur-xl border-t border-white/40 rounded-b-2xl px-8 pt-6 pb-8 {{ $isLarge ? '' : 'hidden' }}">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10">
                        @if($banner['chip_label'])
                            <span
                                class="inline-block bg-[#FFFFFF]/90 text-[#0F1419] text-[0.75rem] font-['Geist_Mono',monospace] font-medium px-3 py-1 rounded-[6px] mb-4">
                                {{ $banner['chip_label'] }}
                            </span>
                        @endif
                        <h3
                            class="text-[#FFFFFF] {{ $isLarge ? 'text-3xl' : 'text-xl' }} font-semibold tracking-[-0.02em] mb-2">
                            {{ $banner['title'] }}
                        </h3>
                        @if($banner['subtitle'])
                            <p class="text-[#FFFFFF]/90 text-[0.95rem] leading-[1.55] mb-5 {{ $isLarge ? 'max-w-xl' : '' }}">
                                {{ $banner['subtitle'] }}
                            </p>
                        @endif
                        <span
                            class="inline-block bg-[#FFFFFF] text-[#0F1419] px-5 py-2.5 rounded-[10px] text-[0.95rem] font-semibold hover:opacity-90 transition-opacity">
                            {{ $banner['cta_text'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>