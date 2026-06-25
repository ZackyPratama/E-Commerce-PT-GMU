<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        @page { margin: 20px 30px; }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            color: #0F1419;
            background: #F1F3F5;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .invoice-card {
            max-width: 620px;
            margin: 24px auto;
            background: #FFFFFF;
            border-radius: 10px;
            padding: 28px 32px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .b2b-badge {
            display: inline-block;
            font-size: 10px;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #4A5568;
            border: 1px solid #4A5568;
            border-radius: 4px;
            padding: 2px 8px;
            margin-bottom: 8px;
            letter-spacing: 2px;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin: 0 0 4px 0;
            color: #0F1419;
        }

        .order-number {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            margin: 0;
        }

        .header-right {
            text-align: right;
            font-size: 10px;
        }

        .header-right p {
            margin: 0 0 2px 0;
            color: #4A5568;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .header-right .value {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #0F1419;
            margin-bottom: 10px;
        }

        .header-right .status-paid {
            font-family: 'DejaVu Sans Mono', monospace;
            color: #0F1419;
        }

        .header-right .status-unpaid {
            font-family: 'DejaVu Sans Mono', monospace;
            color: #4A5568;
        }

        hr {
            border: none;
            border-top: 1px solid #4A5568;
            margin: 0 0 28px 0;
        }

        hr.light {
            border-color: #F1F3F5;
        }

        .info-grid {
            display: flex;
            gap: 32px;
            margin-bottom: 28px;
        }

        .info-col {
            flex: 1;
        }

        .info-label {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            margin: 0 0 6px 0;
        }

        .info-name {
            font-size: 12px;
            font-weight: 600;
            color: #0F1419;
            margin: 0;
        }

        .info-sub {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            margin: 2px 0 0 0;
        }

        .info-address {
            font-style: normal;
            font-size: 12px;
            color: #4A5568;
            line-height: 1.55;
            margin: 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 24px;
        }

        table.items thead tr {
            background: #0F1419;
            color: #FFFFFF;
        }

        table.items th {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            font-weight: normal;
            text-align: left;
            padding: 8px 10px;
        }

        table.items th.qty {
            text-align: center;
            width: 10%;
        }

        table.items th.price,
        table.items th.subtotal {
            text-align: right;
            width: 22%;
        }

        table.items th:first-child {
            border-radius: 4px 0 0 0;
        }

        table.items th:last-child {
            border-radius: 0 4px 0 0;
        }

        table.items td {
            padding: 10px;
            border-bottom: 1px solid #F1F3F5;
        }

        table.items td:last-child {
            border-bottom: none;
        }

        table.items td.product-name {
            color: #0F1419;
        }

        table.items td.product-variant {
            display: block;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            margin-top: 3px;
        }

        table.items td.qty-cell {
            text-align: center;
            color: #4A5568;
        }

        table.items td.price-cell {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #4A5568;
        }

        table.items td.subtotal-cell {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #0F1419;
        }

        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 28px;
        }

        .totals {
            width: 220px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #4A5568;
            margin-bottom: 6px;
        }

        .totals-row .amount {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .totals-grand {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 2px solid #0F1419;
            padding-top: 10px;
            margin-top: 6px;
        }

        .totals-grand .label {
            font-size: 12px;
            font-weight: 600;
            color: #0F1419;
        }

        .totals-grand .amount {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 16px;
            font-weight: 600;
            color: #2C5EF5;
        }

        .notes-box {
            background: #F1F3F5;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .notes-label {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            margin: 0 0 4px 0;
        }

        .notes-text {
            font-size: 12px;
            color: #0F1419;
            line-height: 1.55;
            margin: 0;
        }

        .footer {
            text-align: center;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            color: #4A5568;
            padding-top: 20px;
        }

        .no-print { display: none; }
    </style>
</head>
<body>

    <div class="invoice-card">

        {{-- Header --}}
        <div class="header">
            <div>
                @if($isB2B)
                    <div class="b2b-badge">B2B</div>
                @endif
                <h1>Invoice</h1>
                <p class="order-number">{{ $order->order_number }}</p>
            </div>
            <div class="header-right">
                <p>Tanggal</p>
                <div class="value">{{ $order->created_at->format('d M Y') }}</div>
                <p>Pembayaran</p>
                @if($order->payment_status === 'paid')
                    <div class="status-paid">Lunas</div>
                @else
                    <div class="status-unpaid">Belum dibayar</div>
                @endif
            </div>
        </div>

        <hr>

        {{-- Customer info --}}
        <div class="info-grid">
            <div class="info-col">
                <p class="info-label">Tagihan Kepada</p>
                @if($isB2B)
                    <p class="info-name">{{ $order->customer->company_name ?? '—' }}</p>
                    <p class="info-sub">NPWP: {{ $order->customer->company_registration_number ?? '—' }}</p>
                    <p class="info-sub" style="color:#0F1419;font-weight:normal;margin-top:4px;font-family:DejaVu Sans,sans-serif;font-size:12px;">{{ $order->shipping_full_name }}</p>
                @else
                    <p class="info-name">{{ $order->shipping_full_name }}</p>
                @endif
            </div>
            <div class="info-col">
                <p class="info-label">Dikirim Ke</p>
                <address class="info-address">
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2)
                        {{ $order->shipping_address_line_2 }}<br>
                    @endif
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </address>
            </div>
        </div>

        <hr>

        {{-- Items table --}}
        <table class="items">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="qty">Qty</th>
                    <th class="price">Harga</th>
                    <th class="subtotal">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td class="product-name">
                            {{ $item->product_name }}
                            @if($item->variant_name)
                                <span class="product-variant">{{ $item->variant_name }}</span>
                            @endif
                        </td>
                        <td class="qty-cell">{{ $item->quantity }}</td>
                        <td class="price-cell">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="subtotal-cell">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-wrapper">
            <div class="totals">

                <div class="totals-row">
                    <span>Subtotal</span>
                    <span class="amount">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="totals-row">
                        <span>Diskon</span>
                        <span class="amount">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="totals-row">
                    <span>Pengiriman</span>
                    @if($order->shipping_cost > 0)
                        <span class="amount">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    @else
                        <span>Gratis</span>
                    @endif
                </div>

                @if($order->tax_amount > 0)
                    <div class="totals-row">
                        <span>Pajak</span>
                        <span class="amount">Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="totals-grand">
                    <span class="label">Total</span>
                    <span class="amount">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

            </div>
        </div>

        {{-- Notes --}}
        @if($order->customer_notes)
            <div class="notes-box">
                <p class="notes-label">Catatan</p>
                <p class="notes-text">{{ $order->customer_notes }}</p>
            </div>
        @endif

        {{-- Footer --}}
        <hr class="light">
        <div class="footer">
            {{ config('app.name') }} &mdash; Terima kasih telah berbelanja bersama kami.
        </div>

    </div>

</body>
</html>
