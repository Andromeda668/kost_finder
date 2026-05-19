<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingPaymentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Booking $booking;
    public string $event;
    public ?int $performedBy;
    public array $payload;

    public function __construct(Booking $booking, string $event, ?int $performedBy = null, array $payload = [])
    {
        $this->booking = $booking;
        $this->event = $event;
        $this->performedBy = $performedBy;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        Log::info('Processing booking payment notification', [
            'booking_id' => $this->booking->id,
            'event' => $this->event,
            'performed_by' => $this->performedBy,
            'payload' => $this->payload,
        ]);

        // Placeholder: implement real notification sending here.
    }
}
