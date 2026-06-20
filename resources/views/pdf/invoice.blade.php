<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        @page { margin: 32px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 0.95rem;
            line-height: 1.55;
            color: #0F1419;
            background: #F1F3F5;
            padding: 24px;
        }
        .card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 2.25rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #0F1419;
            margin: 0;
        }
        .header .sub {
            color: #4A5568;
            font-size: 0.75rem;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-top: 4px;
        }
        .badge-b2b {
            display: inline-block;
            background: #2C5EF5;
            color: #FFFFFF;
            padding: 4px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-bottom: 12px;
        }
        .divider {
            border: 0;
            border-top: 1px solid #4A5568;
            margin: 16px 0;
        }
        .grid-rows {
            display: table;
            width: 100%;
        }
        .grid-row {
            display: table-row;
        }
        .grid-label {
            display: table-cell;
            color: #4A5568;
            font-size: 0.75rem;
            font-family: 'DejaVu Sans Mono', monospace;
            padding: 4px 16px 4px 0;
            white-space: nowrap;
            width: 1%;
            vertical-align: top;
        }
        .grid-value {
            display: table-cell;
            color: #0F1419;
            padding: 4px 0;
            vertical-align: top;
        }
        .payment-status {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 0.75rem;
        }
        .payment-status.paid { color: #0F1419; }
        .payment-status.unpaid { color: #4A5568; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        table.items thead th {
            background: #0F1419;
            color: #FFFFFF;
            font-size: 0.75rem;
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: left;
            padding: 10px 12px;
            letter-spacing: 0;
        }
        table.items thead th:first-child { border-radius: 6px 0 0 0; }
        table.items thead th:last-child { border-radius: 0 6px 0 0; }
        table.items tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #F1F3F5;
        }
        table.items tbody tr:last-child td { border-bottom: none; }
        table.items td.numeric { text-align: right; }
        table.items .variant {
            color: #4A5568;
            font-size: 0.8rem;
        }
        .totals {
            margin-top: 16px;
        }
        .totals table { width: 100%; }
        .totals td {
            padding: 4px 12px;
            font-size: 0.9rem;
        }
        .totals .label { text-align: right; color: #4A5568; }
        .totals .value { text-align: right; }
        .totals .grand-total td {
            border-top: 2px solid #0F1419;
            padding-top: 10px;
            font-weight: 600;
        }
        .totals .grand-total .value {
            color: #2C5EF5;
            font-size: 1.1rem;
        }
        .notes {
            margin-top: 24px;
            padding: 16px;
            background: #F1F3F5;
            border-radius: 10px;
        }
        .notes strong {
            font-size: 0.75rem;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #4A5568;
        }
        .notes p {
            font-size: 0.85rem;
            color: #0F1419;
            margin-top: 4px;
        }
        .footer {
            text-align: center;
            color: #4A5568;
            font-size: 0.75rem;
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #F1F3F5;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($isB2B)
                <div class="badge-b2b">B2B</div>
            @endif
            <h1>Invoice</h1>
            <div class="sub">{{ $order->order_number }}</div>
        </div>

        <div class="divider"></div>

        <div class="grid-rows">
            <div class="grid-row">
                <div class="grid-label">Tanggal</div>
                <div class="grid-value">{{ $order->created_at->format('d M Y') }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Pembayaran</div>
                <div class="grid-value">
                    <span class="payment-status {{ $order->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum dibayar' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="grid-rows">
            @if($isB2B)
                <div class="grid-row">
                    <div class="grid-label">Perusahaan</div>
                    <div class="grid-value">{{ $order->customer->company_name ?? '-' }}</div>
                </div>
                <div class="grid-row">
                    <div class="grid-label">NPWP</div>
                    <div class="grid-value">{{ $order->customer->company_registration_number ?? '-' }}</div>
                </div>
            @endif
            <div class="grid-row">
                <div class="grid-label">Pelanggan</div>
                <div class="grid-value">{{ $order->shipping_full_name }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-label">Alamat</div>
                <div class="grid-value">
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:50%;">Produk</th>
                    <th style="width:10%;">Qty</th>
                    <th style="width:20%;" class="numeric">Harga</th>
                    <th style="width:20%;" class="numeric">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if($item->variant_name)
                                <br><span class="variant">{{ $item->variant_name }}</span>
                            @endif
                        </td>
                        <td class="numeric">{{ $item->quantity }}</td>
                        <td class="numeric">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="numeric">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td class="label">Diskon</td>
                        <td class="value">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Pengiriman</td>
                    <td class="value">
                        @if($order->shipping_cost > 0)
                            Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                        @else
                            Gratis
                        @endif
                    </td>
                </tr>
                @if($order->tax_amount > 0)
                    <tr>
                        <td class="label">Pajak</td>
                        <td class="value">Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td class="label">Total</td>
                    <td class="value">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($order->customer_notes)
            <div class="notes">
                <strong>Catatan</strong>
                <p>{{ $order->customer_notes }}</p>
            </div>
        @endif

        <div class="footer">
            {{ config('app.name') }} &mdash; Terima kasih telah berbelanja bersama kami.
        </div>
    </div>
</body>
</html>
