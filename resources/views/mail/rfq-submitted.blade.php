<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFQ Baru</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7C3AED; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .item { border-bottom: 1px solid #e5e7eb; padding: 10px 0; }
        .item:last-child { border-bottom: none; }
        .total { background: #7C3AED; color: white; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .button { display: inline-block; background: #7C3AED; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0;">RFQ Baru</h1>
    </div>
    <div class="content">
        <p>Halo Admin,</p>
        <p>Pelanggan <strong>{{ $rfq->customer->name }}</strong>
            @if($rfq->customer->company_name) dari <strong>{{ $rfq->customer->company_name }}</strong> @endif
            telah mengajukan permintaan penawaran baru.</p>
        <div class="details">
            <p><strong>No. RFQ:</strong> {{ $rfq->rfq_number }}</p>
            <p><strong>Tanggal:</strong> {{ $rfq->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Pelanggan:</strong> {{ $rfq->customer->name }} ({{ $rfq->customer->email }})</p>
            @if($rfq->customer->company_name)
                <p><strong>Perusahaan:</strong> {{ $rfq->customer->company_name }}</p>
            @endif
        </div>
        <h3>Item yang Diminta</h3>
        @foreach($rfq->items as $item)
            <div class="item">
                <strong>{{ $item->product?->name ?? 'Produk' }}</strong>
                @if($item->variant)
                    <br><span style="color:#6b7280;">{{ $item->variant->name }}</span>
                @endif
                <br>Jumlah: {{ $item->quantity }} × Rp{{ number_format($item->customer_requested_price, 0, ',', '.') }}
            </div>
        @endforeach
        <a href="{{ url('/admin/rfqs/' . $rfq->id . '/edit') }}" class="button">Review RFQ</a>
        @if($rfq->customer_notes)
            <div style="background:#fef3c7;padding:15px;border-radius:8px;margin-top:20px;">
                <strong>Catatan Pelanggan:</strong><br>{{ $rfq->customer_notes }}
            </div>
        @endif
    </div>
    <div class="footer">
        <p>{{ config('app.name') }} &mdash; Sistem Manajemen RFQ</p>
    </div>
</body>
</html>
