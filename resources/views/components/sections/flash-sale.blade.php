@props(['products' => []])

<section class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">Promo Hari Ini</h2>

            <!-- Placeholder countdown — siap dihubungkan ke data backend -->
            <div class="flex items-center gap-2">
                <span class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] uppercase tracking-wider">Berakhir dalam</span>
                <div class="flex items-center gap-1.5">
                    <span class="bg-[#0F1419] text-[#FFFFFF] text-[0.95rem] font-semibold w-10 h-10 flex items-center justify-center rounded-[6px]">08</span>
                    <span class="text-[#4A5568] font-semibold">:</span>
                    <span class="bg-[#0F1419] text-[#FFFFFF] text-[0.95rem] font-semibold w-10 h-10 flex items-center justify-center rounded-[6px]">45</span>
                    <span class="text-[#4A5568] font-semibold">:</span>
                    <span class="bg-[#0F1419] text-[#FFFFFF] text-[0.95rem] font-semibold w-10 h-10 flex items-center justify-center rounded-[6px]">12</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <livewire:product-card :product="$product" :key="'promo-' . $product->id" />
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
    </div>
</section>