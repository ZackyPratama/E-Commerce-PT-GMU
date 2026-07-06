<div class="bg-gray-50 py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm">
            <ol class="flex items-center gap-2">
                <li><a href="{{ route('customer.dashboard') }}" class="text-gray-500 hover:text-blue-600">Akun</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('customer.rfqs.index') }}" class="text-gray-500 hover:text-blue-600">RFQ</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">{{ $rfq->rfq_number }}</li>
            </ol>
        </nav>

        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $rfq->rfq_number }}</h1>
                    <p class="text-sm text-gray-600">Diajukan pada {{ $rfq->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'submitted' => 'bg-blue-100 text-blue-800',
                            'under_review' => 'bg-yellow-100 text-yellow-800',
                            'quoted' => 'bg-green-100 text-green-800',
                            'accepted' => 'bg-emerald-100 text-emerald-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-100 text-gray-800',
                            'converted' => 'bg-purple-100 text-purple-800',
                        ];
                        $statusLabels = [
                            'draft' => 'Draft',
                            'submitted' => 'Diajukan',
                            'under_review' => 'Ditinjau Admin',
                            'quoted' => 'Penawaran Diberikan',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            'expired' => 'Kadaluarsa',
                            'converted' => 'Dikonversi ke Pesanan',
                        ];
                    @endphp
                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $statusClasses[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                    </span>
                </div>
            </div>

            @if($rfq->customer_notes)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-900 mb-1">Catatan Anda:</p>
                    <p class="text-sm text-gray-600">{{ $rfq->customer_notes }}</p>
                </div>
            @endif

            @if($rfq->admin_notes)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 mb-1">Catatan Admin:</p>
                    <p class="text-sm text-blue-700">{{ $rfq->admin_notes }}</p>
                </div>
            @endif

            @if($rfq->valid_until)
                <div class="mb-6 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm font-medium text-yellow-900">
                        Penawaran berlaku sampai: {{ $rfq->valid_until->format('d M Y') }}
                    </p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Produk</th>
                        <th class="text-center px-6 py-4 text-sm font-semibold text-gray-900">Qty</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Harga Satuan</th>
                        @if($rfq->isQuoted() || $rfq->isAccepted() || $rfq->isConverted())
                            <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Harga Ditawarkan</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($rfq->items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}"
                                            alt="{{ $item->product->name }}"
                                            class="w-12 h-12 rounded object-cover bg-gray-100">
                                    @else
                                        <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 text-lg">{{ $item->product?->name ? substr($item->product->name, 0, 1) : '?' }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->product?->name ?? 'Produk Tidak Tersedia' }}</p>
                                        @if($item->variant)
                                            <p class="text-sm text-gray-500">{{ $item->variant->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">
                                @if($item->customer_requested_price)
                                    Rp {{ number_format($item->customer_requested_price, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            @if($rfq->isQuoted() || $rfq->isAccepted() || $rfq->isConverted())
                                <td class="px-6 py-4 text-right font-medium text-[#2C5EF5]">
                                    @if($item->quoted_price)
                                        Rp {{ number_format($item->quoted_price, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    @if($item->subtotal)
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                @if($rfq->isQuoted() || $rfq->isAccepted() || $rfq->isConverted())
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-900">Subtotal</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format($rfq->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($rfq->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-2 text-right text-sm text-gray-600">Diskon</td>
                                <td class="px-6 py-2 text-right text-sm text-green-600">- Rp {{ number_format($rfq->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($rfq->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-2 text-right text-sm text-gray-600">Pajak</td>
                                <td class="px-6 py-2 text-right text-sm">Rp {{ number_format($rfq->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="border-t-2 border-gray-300">
                            <td colspan="4" class="px-6 py-4 text-right font-bold text-lg text-gray-900">Total</td>
                            <td class="px-6 py-4 text-right font-bold text-lg text-[#2C5EF5]">Rp {{ number_format($rfq->total ?: $rfq->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @else
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right font-semibold text-gray-900">Subtotal Diminta</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format($rfq->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($rfq->isQuoted())
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-4 justify-center">
                    <button wire:click="acceptQuotation"
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                        Terima Penawaran
                    </button>
                    <button wire:click="rejectQuotation"
                        class="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                        Tolak Penawaran
                    </button>
                </div>
            </div>
        @endif

        @if($rfq->isAccepted())
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Penawaran Diterima</h2>
                <p class="text-gray-600">Pesanan sedang diproses, silakan lanjutkan ke pembayaran.</p>
            </div>
        @endif

        @if($rfq->isConverted() && $rfq->order)
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 text-purple-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Dikonversi ke Pesanan</h2>
                <p class="text-gray-600 mb-6">RFQ ini telah dikonversi menjadi pesanan.</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('customer.orders.show', $rfq->order->id) }}"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Lihat Pesanan
                    </a>
                    <a href="{{ route('customer.orders.invoice', $rfq->order->id) }}"
                        class="inline-block bg-white text-blue-600 border-2 border-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition font-semibold">
                        Download Invoice
                    </a>
                </div>
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('customer.rfqs.index') }}"
                class="text-blue-600 hover:text-indigo-700 font-medium">
                ← Kembali ke Daftar RFQ
            </a>
        </div>
    </div>
</div>
