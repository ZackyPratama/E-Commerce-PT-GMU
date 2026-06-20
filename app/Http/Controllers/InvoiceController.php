<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        $order->load(['items', 'customer']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'isB2B' => $order->customer?->isB2BApproved() && $order->rfq_id,
        ]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
