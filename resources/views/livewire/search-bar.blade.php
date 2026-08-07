<div class="w-full">
    <form action="{{ route('products.index') }}" method="GET">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-[#4A5568]/60" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" autocomplete="off"
                placeholder="Cari alat listrik, kabel, panel, dan lainnya..."
                class="w-full pl-10 pr-20 py-2.5 bg-[#F1F3F5] border border-[#4A5568]/10 rounded-[10px] text-[0.95rem] text-[#0F1419] placeholder:text-[#4A5568]/60 focus:outline-none focus:border-[#2C5EF5] focus:ring-1 focus:ring-[#2C5EF5] transition">
            <button type="submit"
                class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-[#2C5EF5] text-[#FFFFFF] rounded-[6px] px-4 py-1.5 text-sm font-medium hover:opacity-90 transition-opacity">
                Cari
            </button>
        </div>
    </form>
</div>
