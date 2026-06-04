<x-layouts.front-end-layout :title="'Pembayaran Diproses - #' . $order->order_number">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Pending Icon -->
                <div
                    class="mx-auto w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">Pembayaran Diproses!</h1>
                <p class="text-lg text-gray-600 mb-8">
                    Pembayaran Anda sedang dalam proses. Silakan tunggu konfirmasi dari sistem kami.
                    Proses ini biasanya memerlukan beberapa saat.
                </p>

                <!-- Order Info -->
                <div class="bg-amber-50 rounded-lg p-6 mb-8 border border-amber-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-left">
                            <p class="text-sm text-gray-600">Nomor Pesanan</p>
                            <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm text-gray-600">Total Harga</p>
                            <p class="font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm text-gray-600">Status Pembayaran</p>
                            <span
                                class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-amber-100 text-amber-800">
                                Menunggu Konfirmasi
                            </span>
                        </div>
                        <div class="text-left">
                            <p class="text-sm text-gray-600">Waktu</p>
                            <p class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Messages -->
                <div class="space-y-4 mb-8">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-blue-900">
                            <strong>💡 Tips:</strong> Anda akan menerima email notifikasi saat pembayaran dikonfirmasi.
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-blue-900">
                            <strong>⏱️ Informasi:</strong> Beberapa metode pembayaran memerlukan waktu 1-24 jam untuk
                            diproses.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('customer.orders.show', $order->id) }}"
                        class="inline-flex items-center justify-center bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Detail Pesanan
                    </a>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center justify-center bg-gray-200 text-gray-900 px-8 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Lanjutkan Belanja
                    </a>
                </div>

                <!-- Help Text -->
                <div class="mt-8 pt-8 border-t">
                    <p class="text-gray-600 mb-4">Ada pertanyaan tentang pesanan Anda?</p>
                    <p class="text-sm text-gray-600">
                        Hubungi tim dukungan kami di
                        <a href="mailto:{{ config('mail.from.address') }}"
                            class="text-blue-600 hover:text-indigo-700 font-medium">
                            {{ config('mail.from.address') }}
                        </a>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <p class="mt-6 text-center text-gray-600">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-indigo-700 font-medium">
                    ← Kembali ke Beranda
                </a>
            </p>
        </div>
    </div>
</x-layouts.front-end-layout>