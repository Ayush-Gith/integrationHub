<?php

namespace App\Events;

use App\Models\Webhook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $webhook;

    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }
}

