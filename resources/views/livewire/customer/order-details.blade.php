<div class="bg-gray-50 py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- header --}}
        <div class="mb-8">
            <nav class="text-sm mb-4">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ route('customer.dashboard') }}" class="text-gray-500 hover:text-blue-600">Akun</a></li>
                    <li class="text-gray-400">/</li>
                    <li><a href="{{ route('customer.orders') }}" class="text-gray-500 hover:text-blue-600">Pesanan</a></li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900 font-medium">{{ $order->order_number }}</li>
                </ol>
            </nav>
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan</h1>
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.orders.invoice', $order->id) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        Download Invoice
                    </a>
                    <span class="px-4 py-2 rounded-lg text-sm font-semibold {{ 
                    $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                    ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                    ($order->status === 'shipped' ? 'bg-blue-100 text-blue-800' :
                    'bg-yellow-100 text-yellow-800')) 
                }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
        {{-- content/ grid --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- order info --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pesanan</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nomor Pesanan</p>
                            <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Pesanan</p>
                            <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status Pembayaran</p>
                            <span class="inline-block px-2 py-1 text-sm rounded {{ 
                                $order->payment_status->value === 'paid' || $order->payment_status->value === 'completed' ? 'bg-green-100 text-green-800' : 
                                'bg-yellow-100 text-yellow-800' 
                            }}">
                                {{ ucfirst($order->payment_status->value) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-900">
                                {{ $order->payment_method === 'midtrans' ? 'Midtrans Payment Gateway' : 'Cash on Delivery' }}
                            </p>
                        </div>
                        @if($order->tracking_number)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600">Nomor Pelacakan</p>
                                <p class="font-semibold text-gray-900 font-mono">{{ $order->tracking_number }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- order Items --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Item Pesanan</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                            alt="{{ $item->product_name }}"
                                            class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                    @if($item->variant_name)
                                        <p class="text-sm text-gray-600">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }} × Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- Shipping Address --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Alamat Pengiriman</h2>
                    <div class="text-gray-700">
                        <p class="font-semibold">{{ $order->shipping_full_name }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                        <p class="mt-2">{{ $order->shipping_address_line_1 }}</p>
                        @if($order->shipping_address_line_2)
                            <p>{{ $order->shipping_address_line_2 }}</p>
                        @endif
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>
                {{-- Order History Timeline --}}
                @php
                    $statusFlow = ['pending', 'processing', 'shipped', 'delivered'];
                    $labels = [
                        'pending' => 'Pesanan Dibuat',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                    $icons = [
                        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'processing' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                        'shipped' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l-2 1m2-1l6 3 6-3m-6-3l6 3-6-3zm0 0l-6-3m6 3V6',
                        'delivered' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ];
                    $completedStatuses = $order->statusHistories->pluck('status')->all();
                    $currentStatus = $order->status;
                    $isCancelled = $currentStatus === 'cancelled';
                    $currentIndex = array_search($currentStatus, $statusFlow);
                @endphp
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Riwayat Pesanan</h2>
                    <div class="relative">
                        <div class="absolute left-4.5 top-3 bottom-3 w-0.5 bg-[#4A5568]/20"></div>
                        <div class="space-y-0">
                            @foreach($statusFlow as $i => $status)
                                @php
                                    $isCompleted = in_array($status, $completedStatuses);
                                    $isActive = $status === $currentStatus;
                                    $isFuture = !$isCompleted && !$isActive;

                                    if ($isCancelled) {
                                        $isActive = false;
                                        $isFuture = $i > $currentIndex;
                                        $isCompleted = $i < $currentIndex;
                                    }

                                    $circleClass = $isCompleted ? 'bg-green-500' : ($isActive ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-gray-200');
                                    $iconClass = $isCompleted || $isActive ? 'text-white' : 'text-gray-400';
                                    $lineClass = $i < count($statusFlow) - 1 ? ($isCompleted ? 'bg-green-400' : 'bg-[#4A5568]/20') : '';
                                    $textClass = $isCancelled && $i === $currentIndex ? 'text-red-600' : ($isCompleted || $isActive ? 'text-gray-900' : 'text-gray-400');
                                @endphp
                                <div class="flex gap-4 pb-8 relative">
                                    <div class="shrink-0 relative z-10">
                                        <div class="w-[36px] h-[36px] rounded-full flex items-center justify-center transition-colors {{ $circleClass }}">
                                            @if($isCompleted)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$status] }}" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-2 -mt-0.5">
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold {{ $textClass }}">{{ $labels[$status] }}</p>
                                            @php $historyEntry = $order->statusHistories->firstWhere('status', $status); @endphp
                                            @if($historyEntry)
                                                <p class="text-sm text-gray-500 font-mono">{{ $historyEntry->created_at->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>
                                        @if($historyEntry && $historyEntry->notes && $historyEntry->notes !== 'Order created')
                                            <p class="text-sm text-gray-500 mt-0.5">{{ $historyEntry->notes }}</p>
                                        @endif
                                    </div>
                                    @if($i < count($statusFlow) - 1)
                                        <div class="absolute left-[30px] top-[36px] bottom-0 w-0.5 {{ $lineClass }}"></div>
                                    @endif
                                </div>
                            @endforeach

                            @if($isCancelled)
                                <div class="flex gap-4 pb-0 relative">
                                    <div class="shrink-0 relative z-10">
                                        <div class="w-[36px] h-[36px] rounded-full flex items-center justify-center bg-red-100">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['cancelled'] }}" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-0 -mt-0.5">
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold text-red-600">{{ $labels['cancelled'] }}</p>
                                            @php $cancelEntry = $order->statusHistories->firstWhere('status', 'cancelled'); @endphp
                                            @if($cancelEntry)
                                                <p class="text-sm text-gray-500 font-mono">{{ $cancelEntry->created_at->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>
                                        @if($cancelEntry && $cancelEntry->notes && $cancelEntry->notes !== 'Order created')
                                            <p class="text-sm text-gray-500 mt-0.5">{{ $cancelEntry->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Order Summary --}}
            <div>
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span class="font-medium">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengiriman</span>
                            <span class="font-medium">
                                @if($order->shipping_cost > 0)
                                    Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-green-600">Gratis</span>
                                @endif
                            </span>
                        </div>
                        @if($order->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pajak</span>
                                <span class="font-medium">Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-2xl font-bold text-blue-600">
                                Rp{{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if($order->customer_notes)
                        <div class="border-t pt-4">
                            <p class="text-sm font-medium text-gray-900 mb-2">Catatan Pesanan</p>
                            <p class="text-sm text-gray-600">{{ $order->customer_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>