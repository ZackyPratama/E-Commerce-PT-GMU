<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman pembayaran dengan Snap UI
     */
    public function showPayment(Order $order)
    {
        // Validasi bahwa order milik customer yang login
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized');
        }

        // Validasi order punya snap token
        if (!$order->snap_token) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'Token pembayaran tidak valid.');
        }

        return view('checkout.payment', [
            'order' => $order,
            'snapToken' => $order->snap_token,
        ]);
    }

    /**
     * Handle successful payment
     */
    public function success(Order $order, Request $request)
    {
        // Validasi order milik customer yang login
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized');
        }

        // Update order status jika belum success
        if ($order->payment_status !== 'completed') {
            $order->update([
                'payment_status' => 'completed',
                'status' => 'confirmed',
                'payment_completed_at' => now(),
            ]);
        }

        // Clear cart dari session
        session()->forget('cart');

        // Redirect ke halaman success
        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    /**
     * Handle pending payment
     */
    public function pending(Order $order)
    {
        // Validasi order milik customer yang login
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('customer.orders.show', $order->id)
            ->with('warning', 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.');
    }

    /**
     * Handle failed/cancelled payment
     */
    public function error(Order $order)
    {
        // Validasi order milik customer yang login
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized');
        }

        // Update order status
        $order->update([
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        // Redirect ke halaman cancel
        return view('checkout.cancel', [
            'order' => $order,
        ]);
    }
}