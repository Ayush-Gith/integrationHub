<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhookEvent;

    /**
     * Create a new job instance.
     */
    public function __construct(WebhookEvent $webhookEvent)
    {
        $this->webhookEvent = $webhookEvent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // ✅ Simulate processing (could be calling external API, etc.)
            sleep(2); // simulate some work

            // ✅ Update status to "processed"
            if ($this->webhookEvent) {
                $this->webhookEvent->update(['status' => 'processed']);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProcessWebhookJob failed', ['error' => $e->getMessage(), 'webhook_event_id' => $this->webhookEvent->id ?? null]);
            // rethrow so job can be retried if desired
            throw $e;
        }
    }
}
