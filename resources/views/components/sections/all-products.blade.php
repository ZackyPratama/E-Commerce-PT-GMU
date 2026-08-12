@props(['products' => []])

<section class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">Semua Produk</h2>
            <a href="{{ route('products.index') }}"
                class="text-[#4A5568] hover:text-[#0F1419] text-[0.95rem] transition-colors">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <livewire:product-card :product="$product" :key="'all-' . $product->id" />
            @empty
                @for($i = 1; $i <= 4; $i++)
                    <div
                        class="bg-[#FFFFFF]/70 backdrop-blur border border-[#4A5568]/10 rounded-2xl overflow-hidden">
                        <div class="aspect-square bg-[#F1F3F5]"></div>
                        <div class="p-4 space-y-3">
                            <div class="h-4 bg-[#F1F3F5] rounded"></div>
                            <div class="h-4 bg-[#F1F3F5] rounded w-2/3"></div>
                        </div>
                    </div>
                @endfor
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>
