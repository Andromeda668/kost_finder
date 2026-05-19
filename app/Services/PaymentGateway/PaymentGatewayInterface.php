<?php

namespace App\Services\PaymentGateway;

use App\Models\Booking;

interface PaymentGatewayInterface
{
    public function createPaymentRequest(Booking $booking): array;

    public function verifyWebhookSignature(string $payload, ?string $signature): bool;
}
