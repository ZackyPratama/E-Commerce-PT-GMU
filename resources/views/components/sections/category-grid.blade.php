@props(['categories' => []])

<section class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">Kategori Pilihan</h2>
            <a href="{{ route('products.index') }}"
                class="text-[#4A5568] hover:text-[#0F1419] text-[0.95rem] transition-colors">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                    class="group block bg-[#FFFFFF]/70 backdrop-blur border border-[#4A5568]/10 rounded-2xl p-4 hover:shadow-lg hover:border-[#2C5EF5]/20 transition-all duration-300">
                    <div class="aspect-square rounded-[10px] overflow-hidden bg-[#F1F3F5] mb-4">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-[#F1F3F5] text-[#0F1419]">
                                <span class="text-[2rem] font-semibold">{{ substr($category->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-center font-medium text-[0.95rem] text-[#0F1419] truncate">
                        {{ $category->name }}
                    </h3>
                    <p
                        class="text-center text-[0.75rem] font-['Geist_Mono',monospace] tracking-normal text-[#4A5568] mt-1">
                        {{ $category->products_count }} Produk
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>