<?php

namespace App\Services\PaymentGateway;

use InvalidArgumentException;

class PaymentGatewayManager
{
    public function driver(): PaymentGatewayInterface
    {
        $provider = config('services.payment_gateway.provider') ?: env('PAYMENT_PROVIDER');

        return match ($provider) {
            'midtrans' => new MidtransGateway(),
            default => throw new InvalidArgumentException('Payment provider ['.$provider.'] is not supported.'),
        };
    }
}
