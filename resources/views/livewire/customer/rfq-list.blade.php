<div class="bg-gray-50 py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Permintaan Penawaran</h1>
            <p class="mt-2 text-sm text-gray-600">Daftar permintaan penawaran (RFQ) Anda</p>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($rfqs->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">No. RFQ</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Tanggal</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-900">Item</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-gray-900">Total</th>
                            <th class="text-center px-6 py-4 text-sm font-semibold text-gray-900">Status</th>
                            <th class="text-center px-6 py-4 text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($rfqs as $rfq)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900">{{ $rfq->rfq_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $rfq->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $rfq->items->count() }} item
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    Rp {{ number_format($rfq->total ?: $rfq->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
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
                                            'under_review' => 'Ditinjau',
                                            'quoted' => 'Penawaran Dikirim',
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'expired' => 'Kadaluarsa',
                                            'converted' => 'Dikonversi',
                                        ];
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('customer.rfqs.show', $rfq->id) }}"
                                        class="text-blue-600 hover:text-indigo-700 font-medium text-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $rfqs->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto w-24 h-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Permintaan Penawaran</h2>
                <p class="text-gray-600 mb-6">Anda belum mengajukan permintaan penawaran. Tambahkan produk ke keranjang lalu ajukan penawaran.</p>
                <a href="{{ route('products.index') }}"
                    class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>
