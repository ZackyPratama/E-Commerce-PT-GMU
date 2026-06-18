<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFQ Diterima</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563EB; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #2563EB; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0;">RFQ Diterima</h1>
    </div>
    <div class="content">
        <p>Halo Admin,</p>
        <p>Pelanggan <strong>{{ $rfq->customer->name }}</strong>
            @if($rfq->customer->company_name) dari <strong>{{ $rfq->customer->company_name }}</strong> @endif
            telah <strong>menerima</strong> penawaran untuk {{ $rfq->rfq_number }}.</p>
        <div class="details">
            <p><strong>No. RFQ:</strong> {{ $rfq->rfq_number }}</p>
            <p><strong>Tanggal:</strong> {{ $rfq->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Pelanggan:</strong> {{ $rfq->customer->name }} ({{ $rfq->customer->email }})</p>
        </div>
        <p>Pesanan akan segera dibuat. Silakan cek panel admin untuk memproses pesanan.</p>
        <a href="{{ url('/admin/rfqs/' . $rfq->id . '/edit') }}" class="button">Lihat Detail</a>
    </div>
    <div class="footer">
        <p>{{ config('app.name') }} &mdash; Sistem Manajemen RFQ</p>
    </div>
</body>
</html>
