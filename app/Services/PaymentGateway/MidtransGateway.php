<?php

namespace App\Services\PaymentGateway;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransGateway implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $merchantId;
    protected string $mode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.payment_gateway.secret') ?: env('PAYMENT_GATEWAY_SECRET', '');
        $this->merchantId = config('services.payment_gateway.merchant_id') ?: env('PAYMENT_MERCHANT_ID', '');
        $this->mode = config('services.payment_gateway.mode', 'sandbox');
        $this->baseUrl = $this->mode === 'production'
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    public function createPaymentRequest(Booking $booking): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => 'booking-'.$booking->id.'-'.time(),
                'gross_amount' => $booking->kost->priceFor($booking->tipe_sewa) ?: $booking->kost->harga,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'item_details' => [[
                'id' => 'kost-'.$booking->kost->id,
                'price' => $booking->kost->priceFor($booking->tipe_sewa) ?: $booking->kost->harga,
                'quantity' => 1,
                'name' => $booking->kost->nama_kost,
            ]],
            'enabled_payments' => $booking->kost->available_payment_methods,
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s T'),
                'unit' => 'hour',
                'duration' => 24,
            ],
            'custom_expiry' => [
                'order_time' => now()->format('Y-m-d H:i:s T'),
                'expiry_duration' => 24,
                'unit' => 'hour',
            ],
            'metadata' => [
                'booking_id' => $booking->id,
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->timeout(20)
                ->post($this->baseUrl.'/charge', $payload);

            return $response->json();
        } catch (\Exception $exception) {
            Log::error('Midtrans payment request failed', [
                'message' => $exception->getMessage(),
                'booking_id' => $booking->id,
            ]);

            return ['error' => 'Unable to create payment request at this time.'];
        }
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (! $signature || ! $this->serverKey) {
            return false;
        }

        $calculated = hash_hmac('sha512', $payload, $this->serverKey);

        return hash_equals($calculated, $signature);
    }
}
