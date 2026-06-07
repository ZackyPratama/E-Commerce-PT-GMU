<div class="bg-gray-50 py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Saya</h1>
            <nav class="text-sm">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ route('customer.dashboard') }}"
                            class="text-gray-500 hover:text-blue-600">Akun</a></li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900 font-medium">Pesanan</li>
                </ol>
            </nav>
        </div>
        {{-- filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center gap-4">
                <label class="text-gray-700 font-medium">Filter by Status:</label>
                <select wire:model.live="statusFilter"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Pesanan</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Diproses</option>
                    <option value="shipped">Dikirim</option>
                    <option value="delivered">Diterima</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
        </div>
        {{-- Orders List --}}
        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- Order Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="flex items-center gap-6">
                                    <div>
                                        <p class="text-sm text-gray-600">Nomor Pesanan</p>
                                        <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tanggal</p>
                                        <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total</p>
                                        <p class="font-semibold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 rounded text-sm font-semibold {{ 
                                                $order->status === 'delivered' ? 'bg-green-100 text-green-800' :
                    ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                        ($order->status === 'shipped' ? 'bg-blue-100 text-blue-800' :
                            'bg-yellow-100 text-yellow-800')) 
                                            }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <a href="{{ route('customer.orders.show', $order->id) }}"
                                        class="text-blue-600 hover:text-indigo-700 font-medium">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex gap-4">
                                        <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}"
                                                    alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                            @if($item->variant_name)
                                                <p class="text-sm text-gray-600">{{ $item->variant_name }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada pesanan ditemukan</h3>
                <p class="text-gray-600 mb-6">
                    @if($statusFilter)
                        Tidak ada pesanan dengan status "{{ $statusFilter }}"
                    @else
                        Anda belum melakukan pemesanan apapun
                    @endif
                </p>
                @if($statusFilter)
                    <button wire:click="$set('statusFilter', '')"
                        class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Tampilkan Semua Pesanan
                    </button>
                @else
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Mulai Berbelanja
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>