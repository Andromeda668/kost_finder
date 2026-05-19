<?php

namespace App\Http\Controllers;

use App\Jobs\SendBookingPaymentNotification;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = config('services.payment_gateway.secret') ?: env('PAYMENT_GATEWAY_SECRET');
        $signature = $request->header('X-Payment-Signature') ?? $request->header('X-Signature');
        $payload = (string) $request->getContent();

        if ($secret && $signature) {
            $calculated = hash_hmac('sha256', $payload, $secret);
            if (! hash_equals($calculated, $signature)) {
                Log::warning('Payment webhook signature mismatch.');
                return response()->json(['ok' => false], 400);
            }
        }

        $data = $request->json()->all();

        // Expecting booking_id in payload (top-level or metadata)
        $bookingId = $data['booking_id'] ?? $data['metadata']['booking_id'] ?? null;
        $status = strtolower((string) ($data['status'] ?? ''));

        if (! $bookingId) {
            Log::warning('Payment webhook without booking_id', $data);
            return response()->json(['ok' => false], 400);
        }

        $booking = Booking::find($bookingId);
        if (! $booking) {
            Log::warning('Payment webhook booking not found', ['booking_id' => $bookingId]);
            return response()->json(['ok' => false], 404);
        }

        if (in_array($status, ['paid', 'success', 'settlement', 'completed'], true)) {
            $booking->update(['payment_status' => Booking::PAYMENT_PAID]);
            $booking->paymentLogs()->create([
                'user_id' => null,
                'type' => 'webhook_paid',
                'data' => $data,
                'created_at' => now(),
            ]);

            SendBookingPaymentNotification::dispatch($booking, 'webhook_paid', null, $data);
        } else {
            $booking->paymentLogs()->create([
                'user_id' => null,
                'type' => 'webhook_update',
                'data' => $data,
                'created_at' => now(),
            ]);

            SendBookingPaymentNotification::dispatch($booking, 'webhook_update', null, $data);
        }

        return response()->json(['ok' => true]);
    }
}
