<div class="group relative bg-[#FFFFFF] border border-[#4A5568]/10 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <!-- Product Image -->
        <div class="aspect-square overflow-hidden bg-[#F1F3F5]">
            @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center bg-[#F1F3F5]">
                    <span class="text-6xl font-semibold text-[#4A5568]">{{ substr($product->name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-2">
            @if($product->is_featured)
                <span class="bg-[#0F1419] text-[#FFFFFF] text-xs font-medium px-2 py-1 rounded-[6px]">
                    Rekomendasi
                </span>
            @endif
            @if($product->discount_percentage > 0)
                <span class="bg-red-600 text-[#FFFFFF] text-xs font-semibold px-2 py-1 rounded-[6px]">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
            @if($product->stock_status === 'out_of_stock')
                <span class="bg-[#4A5568] text-white text-xs font-semibold px-2 py-1 rounded-[6px]">
                    Stok Habis
                </span>
            @endif
        </div>
    </a>

    <!-- Product Info -->
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('products.show', $product->slug) }}" class="block flex-1">
            <!-- Category -->
            <p class="text-[0.75rem] font-['Geist_Mono',monospace] text-[#4A5568] mb-1.5">
                {{ $product->category->name }}
            </p>

            <!-- Product Name -->
            <h3 class="font-semibold text-[#0F1419] text-[0.95rem] leading-snug mb-2 line-clamp-2 group-hover:text-[#2C5EF5] transition">
                {{ $product->name }}
            </h3>

            <!-- Rating -->
            @if($product->review_count > 0)
                <div class="flex items-center gap-1 mb-2">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= floor($product->average_rating) ? 'text-yellow-400 fill-current' : 'text-[#4A5568]/25 fill-current' }}"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-xs text-[#4A5568]">({{ $product->review_count }})</span>
                </div>
            @endif

            <!-- Price -->
            <div class="flex flex-wrap items-center gap-2 mt-auto pt-1">
                @if($isB2BApproved && $product->b2b_price)
                    <span class="text-xl font-bold text-[#2C5EF5]">
                        Rp {{ number_format($product->b2b_price, 0, ',', '.') }}
                    </span>
                    @if($product->compare_price && $product->compare_price > $product->b2b_price)
                        <span class="text-sm text-[#4A5568] line-through">
                            Rp {{ number_format($product->compare_price, 0, ',', '.') }}
                        </span>
                    @endif
                    <span
                        class="text-xs bg-[#2C5EF5]/10 text-[#2C5EF5] px-2 py-0.5 rounded font-medium">Grosir</span>
                @else
                    <span class="text-xl font-bold text-[#0F1419]">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    @if($product->compare_price)
                        <span class="text-sm text-[#4A5568] line-through">
                            Rp {{ number_format($product->compare_price, 0, ',', '.') }}
                        </span>
                    @endif
                @endif
            </div>
        </a>

        <!-- Action Button (outline, proporsional) -->
        <div class="mt-4">
            @if($product->stock_status === 'in_stock')
                <button wire:click="addToCart" wire:key="add-{{ $product->id }}"
                    class="w-full cursor-pointer border border-[#2C5EF5] text-[#2C5EF5] py-2 px-4 rounded-[10px] text-sm font-medium transition hover:bg-[#2C5EF5] hover:text-[#FFFFFF]">
                    @if($isB2BApproved)
                        Tambah ke Keranjang
                    @else
                        Keranjang
                    @endif
                </button>
            @else
                <button disabled
                    class="w-full border border-[#4A5568]/20 text-[#4A5568] py-2 px-4 rounded-[10px] text-sm font-medium cursor-not-allowed">
                    Habis
                </button>
            @endif
        </div>
    </div>
</div>