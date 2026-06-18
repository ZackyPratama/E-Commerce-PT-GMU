<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFQ Ditolak</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #DC2626; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; }
        .details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #DC2626; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6b7280; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0;">RFQ Ditolak</h1>
    </div>
    <div class="content">
        @if(isset($rfq->customer) && request()->routeIs('admin*'))
            <p>Halo Admin,</p>
            <p>Pelanggan <strong>{{ $rfq->customer->name }}</strong>
                @if($rfq->customer->company_name) dari <strong>{{ $rfq->customer->company_name }}</strong> @endif
                telah <strong>menolak</strong> penawaran untuk {{ $rfq->rfq_number }}.</p>
        @else
            <p>Hai {{ $rfq->customer->name ?? 'Pelanggan' }},</p>
            <p>Permintaan penawaran Anda <strong>{{ $rfq->rfq_number }}</strong> telah ditolak.</p>
        @endif
        <div class="details">
            <p><strong>No. RFQ:</strong> {{ $rfq->rfq_number }}</p>
            <p><strong>Tanggal:</strong> {{ $rfq->created_at->format('d M Y, H:i') }}</p>
        </div>
        @if($rfq->admin_notes && (str_contains($rfq->admin_notes, 'Alasan ditolak') || !isset($rfq->customer)))
            <div style="background:#fef3c7;padding:15px;border-radius:8px;margin:20px 0;">
                <strong>Alasan:</strong><br>{{ preg_replace('/^Alasan ditolak: /', '', $rfq->admin_notes) }}
            </div>
        @endif
        <div style="text-align:center;">
            <a href="{{ route('customer.rfqs.index') }}" class="button">Lihat RFQ Saya</a>
        </div>
    </div>
    <div class="footer">
        <p>{{ config('app.name') }} &mdash; Sistem Manajemen RFQ</p>
    </div>
</body>
</html>
