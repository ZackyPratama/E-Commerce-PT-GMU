<x-layouts.front-end-layout :title="'Pembayaran Pesanan #' . $order->id">
    <div class="bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Pembayaran Pesanan</h1>

            <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Detail Pesanan #{{ $order->id }}</h2>

                        <!-- Order Items -->
                        <div class="space-y-4 mb-6 border-b pb-4">
                            @foreach($order->items as $item)
                                <div class="flex gap-4">
                                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                        @if($item->product && $item->product->images->first())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                                alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-gray-400 text-xs">No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                        @if($item->variant_name)
                                            <p class="text-sm text-gray-600">{{ $item->variant_name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="space-y-2 text-sm mb-6">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($order->shipping_cost > 0)
                                <div class="flex justify-between">
                                    <span>Ongkir:</span>
                                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span>Pajak:</span>
                                    <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Diskon:</span>
                                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Shipping Address -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">📦 Alamat Pengiriman</h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>{{ $order->shipping_full_name }}</strong></p>
                                <p>{{ $order->shipping_phone }}</p>
                                <p>{{ $order->shipping_address_line_1 }}</p>
                                @if($order->shipping_address_line_2)
                                    <p>{{ $order->shipping_address_line_2 }}</p>
                                @endif
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }}
                                    {{ $order->shipping_postal_code }}
                                </p>
                                <p>{{ $order->shipping_country }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">💳 Metode Pembayaran</h2>

                        <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                            <p class="text-sm text-blue-900">
                                <strong>Midtrans Snap Payment</strong><br>
                                Pembayaran aman melalui berbagai metode
                            </p>
                        </div>

                        <p class="text-2xl font-bold text-gray-900 mb-6">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </p>

                        <button type="button" id="payButton" onclick="handlePayment()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 mb-4 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Lanjutkan Pembayaran
                        </button>

                        <a href="{{ route('customer.orders.show', $order->id) }}"
                            class="block text-center text-gray-600 hover:text-gray-900 font-medium py-2">
                            ← Kembali
                        </a>

                        <div class="mt-4 pt-4 border-t">
                            <p class="text-xs text-gray-500 text-center">
                                Anda akan dialihkan ke halaman pembayaran Midtrans yang aman
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap Container -->
    <div id="snap-container"></div>

    <!-- Midtrans Snap Script -->
    <script src="https://app.{{ config('services.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script type="text/javascript">
        function handlePayment() {
            const button = document.getElementById('payButton');
            button.disabled = true;
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Memproses...';

            snap.pay('{{ $snapToken }}', {
                // Callback saat pembayaran berhasil
                onSuccess: function (result) {
                    console.log('Payment success:', result);
                    window.location.href = "{{ route('checkout.success', $order->id) }}";
                },

                // Callback saat pembayaran pending
                onPending: function (result) {
                    console.log('Payment pending:', result);
                    window.location.href = "{{ route('checkout.pending', $order->id) }}";
                },

                // Callback saat pembayaran error
                onError: function (result) {
                    console.log('Payment error:', result);
                    window.location.href = "{{ route('checkout.error', $order->id) }}";

                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Lanjutkan Pembayaran';
                },

                // Callback saat window snap ditutup
                onClose: function () {
                    console.log('Payment popup closed');
                    alert('Anda menutup jendela pembayaran');

                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> Lanjutkan Pembayaran';
                }
            });
        }
    </script>
</x-layouts.front-end-layout>