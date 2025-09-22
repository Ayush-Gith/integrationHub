<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Webhook;
use App\Events\WebhookReceived;

class TriggerWebhook extends Command
{
    protected $signature = 'webhook:trigger {id}';
    protected $description = 'Manually trigger processing of a webhook by ID';

    public function handle()
    {
        $webhook = Webhook::find($this->argument('id'));

        if (!$webhook) {
            $this->error("Webhook not found.");
            return 1;
        }

        WebhookReceived::dispatch($webhook);
        $this->info("Webhook #{$webhook->id} dispatched for processing.");
        return 0;
    }
}
