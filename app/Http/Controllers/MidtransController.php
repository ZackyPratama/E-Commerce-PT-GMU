<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Midtrans\Config;

class MidtransController extends Controller
{
    /**
     * Handle webhook dari Midtrans
     * POST /webhook/midtrans
     */
    public function webhook(Request $request)
    {
        try {
            // Set Midtrans config untuk verifikasi
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$clientKey = config('services.midtrans.client_key');
            Config::$isProduction = config('services.midtrans.is_production');

            // Get data dari request
            $serverKey = config('services.midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;

            // Validasi signature dari Midtrans
            $mySignature = hash(
                "sha512",
                $orderId . $statusCode . $grossAmount . $serverKey
            );

            if ($signatureKey !== $mySignature) {
                \Log::warning('Invalid Midtrans signature', [
                    'order_id' => $orderId,
                    'expected' => $mySignature,
                    'received' => $signatureKey,
                ]);
                return response()->json(['status' => 'signature_mismatch'], 403);
            }

            // Cari order berdasarkan midtrans_order_id
            $order = Order::where('midtrans_order_id', $orderId)->first();

            if (!$order) {
                \Log::warning('Order not found', ['midtrans_order_id' => $orderId]);
                return response()->json(['status' => 'order_not_found'], 404);
            }

            // Handle berdasarkan transaction status
            $transactionStatus = $request->transaction_status;
            $paymentType = $request->payment_type ?? null;

            if ($transactionStatus == 'capture') {
                // Credit card berhasil
                if ($statusCode == '200') {
                    $this->markPaymentAsSuccess($order);
                }
            } elseif ($transactionStatus == 'settlement') {
                // Pembayaran sukses (sudah settlement)
                $this->markPaymentAsSuccess($order);
            } elseif ($transactionStatus == 'pending') {
                // Pembayaran pending (menunggu konfirmasi bank)
                $this->markPaymentAsPending($order);
            } elseif (
                $transactionStatus == 'deny' ||
                $transactionStatus == 'cancel' ||
                $transactionStatus == 'expire'
            ) {
                // Pembayaran gagal
                $this->markPaymentAsFailed($order, $transactionStatus);
            } elseif ($transactionStatus == 'refund') {
                // Refund
                $this->markPaymentAsRefunded($order);
            }

            \Log::info('Midtrans webhook processed', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'order_internal_id' => $order->id,
            ]);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            \Log::error('Midtrans webhook error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark order payment as success
     */
    private function markPaymentAsSuccess(Order $order)
    {
        if ($order->payment_status !== PaymentStatusEnum::PAID) {
            $order->update([
                'payment_status' => PaymentStatusEnum::PAID,
                'status' => 'confirmed',
                'payment_completed_at' => now(),
            ]);

            \Log::info('Order payment marked as success', ['order_id' => $order->id]);
        }
    }

    /**
     * Mark order payment as pending
     */
    private function markPaymentAsPending(Order $order)
    {
        if ($order->payment_status !== PaymentStatusEnum::PENDING) {
            $order->update([
                'payment_status' => PaymentStatusEnum::PENDING,
            ]);

            \Log::info('Order payment marked as pending', ['order_id' => $order->id]);
        }
    }

    /**
     * Mark order payment as failed
     */
    private function markPaymentAsFailed(Order $order, $reason = null)
    {
        if ($order->payment_status !== PaymentStatusEnum::FAILED) {
            $order->update([
                'payment_status' => PaymentStatusEnum::FAILED,
                'status' => 'cancelled',
            ]);

            \Log::warning('Order payment marked as failed', [
                'order_id' => $order->id,
                'reason' => $reason,
            ]);
        }
    }

    /**
     * Mark order payment as refunded
     */
    private function markPaymentAsRefunded(Order $order)
    {
        if ($order->payment_status !== PaymentStatusEnum::REFUNDED) {
            $order->update([
                'payment_status' => PaymentStatusEnum::REFUNDED,
                'status' => 'refunded',
            ]);

            \Log::info('Order payment marked as refunded', ['order_id' => $order->id]);
        }
    }
}
