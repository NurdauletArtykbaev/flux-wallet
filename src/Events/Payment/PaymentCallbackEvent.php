<?php

namespace Nurdaulet\FluxWallet\Events\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCallbackEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;
    public $data;
    /**
     * Create a new event instance.
     */
    public function __construct($provider, $data)
    {
        $this->provider = $provider;
        $this->data = $data;
    }
}
