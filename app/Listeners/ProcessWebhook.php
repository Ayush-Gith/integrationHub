<?php

namespace App\Listeners;

use App\Events\WebhookReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WebhookReceived $event)
    {
        try {
            Log::info("Processing webhook #{$event->webhook->id} for platform {$event->webhook->platform}");
            // TODO: Add actual business logic here (sync products, etc.)
        } catch (\Throwable $e) {
            Log::error('ProcessWebhook listener failed', ['error' => $e->getMessage(), 'webhook_id' => $event->webhook->id ?? null]);
            // Do not rethrow to avoid crashing the listener infrastructure unless desired
        }
    }
}
