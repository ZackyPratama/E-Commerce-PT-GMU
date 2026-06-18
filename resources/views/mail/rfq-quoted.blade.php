<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penawaran Harga</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .item { border-bottom: 1px solid #e5e7eb; padding: 10px 0; }
        .item:last-child { border-bottom: none; }
        .total { background: #059669; color: white; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .button { display: inline-block; background: #059669; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0;">Penawaran Harga Tersedia</h1>
    </div>
    <div class="content">
        <p>Hai {{ $rfq->customer->name }},</p>
        <p>Admin telah mengirimkan penawaran harga untuk permintaan penawaran Anda.</p>
        <div class="details">
            <p><strong>No. RFQ:</strong> {{ $rfq->rfq_number }}</p>
            <p><strong>Tanggal:</strong> {{ $rfq->created_at->format('d M Y, H:i') }}</p>
            @if($rfq->valid_until)
                <p><strong>Berlaku sampai:</strong> {{ $rfq->valid_until->format('d M Y') }}</p>
            @endif
        </div>
        <h3>Rincian Penawaran</h3>
        @foreach($rfq->items as $item)
            <div class="item">
                <strong>{{ $item->product?->name ?? 'Produk' }}</strong>
                @if($item->variant)
                    <br><span style="color:#6b7280;">{{ $item->variant->name }}</span>
                @endif
                <br>Jumlah: {{ $item->quantity }}
                <br>Harga Diminta: Rp{{ number_format($item->customer_requested_price, 0, ',', '.') }}
                <br><strong>Harga Ditawarkan: Rp{{ number_format($item->quoted_price, 0, ',', '.') }}</strong>
            </div>
        @endforeach
        <div class="total">
            <p style="margin:0;text-align:right;font-size:20px;"><strong>Total: Rp{{ number_format($rfq->total ?: $rfq->subtotal, 0, ',', '.') }}</strong></p>
        </div>
        @if($rfq->admin_notes)
            <div style="background:#eff6ff;padding:15px;border-radius:8px;margin:20px 0;">
                <strong>Catatan Admin:</strong><br>{{ $rfq->admin_notes }}
            </div>
        @endif
        <div style="text-align:center;">
            <a href="{{ route('customer.rfqs.show', $rfq->id) }}" class="button">Lihat & Tanggapi Penawaran</a>
        </div>
    </div>
    <div class="footer">
        <p>{{ config('app.name') }} &mdash; Sistem Manajemen RFQ</p>
    </div>
</body>
</html>
