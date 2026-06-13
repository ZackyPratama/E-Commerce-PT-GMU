<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgb(43, 43, 228);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }

        .order-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .item {
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .total {
            background: #4F46E5;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">Terima Kasih atas Pesanan Anda!</h1>
    </div>

    <div class="content">
        <p>Hai {{ $order->customer->name }},</p>

        <p>kami telah menerima pesanan Anda dan sedang mempersiapkannya. Kami akan memberi tahu Anda ketika pesanan
            sudah dalam perjalanan!</p>

        <div class="order-details">
            <h2 style="margin-top: 0;">Detail Pesanan</h2>
            <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
            <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
            <p><strong>Metode Pembayaran:</strong>
                {{ $order->payment_method === 'midtrans' ? 'Kartu Kredit/Debit' : 'Cash on Delivery' }}</p>
            <p><strong>Status Pembayaran:</strong> {{ ucfirst($order->payment_status) }}</p>
        </div>

        <h3>Item Pesanan</h3>
        @foreach($order->items as $item)
            <div class="item">
                <strong>{{ $item->product_name }}</strong>
                @if($item->variant_name)
                    <br><span style="color: #6b7280;">{{ $item->variant_name }}</span>
                @endif
                <br>Jumlah: {{ $item->quantity }} × Rp{{ number_format($item->price, 0, ',', '.') }}
                <br><strong>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
            </div>
        @endforeach

        <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
            <table width="100%" style="margin-top: 10px;">
                <tr>
                    <td>Subtotal:</td>
                    <td align="right">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td style="color: #059669;">Diskon:</td>
                        <td align="right" style="color: #059669;">
                            -Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Pengiriman:</td>
                    <td align="right">
                        @if($order->shipping_cost > 0)
                            Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                        @else
                            <span style="color: #059669;">FREE</span>
                        @endif
                    </td>
                </tr>
                @if($order->tax_amount > 0)
                    <tr>
                        <td>Pajak:</td>
                        <td align="right">Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="total">
            <table width="100%">
                <tr>
                    <td><strong style="font-size: 18px;">Total:</strong></td>
                    <td align="right"><strong
                            style="font-size: 24px;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Alamat Pengiriman</h3>
        <p>
            {{ $order->shipping_full_name }}<br>
            {{ $order->shipping_phone }}<br>
            {{ $order->shipping_address_line_1 }}<br>
            @if($order->shipping_address_line_2)
                {{ $order->shipping_address_line_2 }}<br>
            @endif
            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
            {{ $order->shipping_country }}
        </p>

        <div style="text-align: center;">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="button">
                Lihat Detail Pesanan
            </a>
        </div>

        @if($order->customer_notes)
            <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <strong>Catatan Anda:</strong><br>
                {{ $order->customer_notes }}
            </div>
        @endif
    </div>
</body>

</html>